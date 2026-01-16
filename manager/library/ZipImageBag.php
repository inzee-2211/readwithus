<?php
/**
 * ZipImageBag (PHP 7.4 compatible)
 * - Safely extracts image files from a ZIP into a temp directory
 * - Indexes by relative path and by basename
 * - Resolves CSV "image" value as:
 *    - URL (http/https) -> null (let caller download)
 *    - absolute path (/... or C:\...) -> null
 *    - "cat.png" or "topic1/cat.png" -> extracted path (or null if not found)
 */

class ZipImageBag
{
    /** @var string */
    private $extractDir;

    /** @var array "folder/file.png" => "/tmp/.../folder/file.png" */
    private $byRelative = [];

    /** @var array "file.png" => ["/tmp/.../a/file.png", "/tmp/.../b/file.png"] */
    private $byBase = [];

    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * @param string $zipTmpPath Path to uploaded temp ZIP file
     * @throws Exception
     */
    public function __construct(string $zipTmpPath)
    {
        $this->extractDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'rwu_zip_' . bin2hex(random_bytes(8));

        if (!is_dir($this->extractDir) && !mkdir($this->extractDir, 0775, true)) {
            throw new Exception('Unable to create temp directory for ZIP extraction.');
        }
if (!class_exists('ZipArchive')) {
    throw new Exception('ZIP feature is not available on this server (ZipArchive missing).');
}

        $this->extractZipSafely($zipTmpPath);
        $this->indexFiles();
    }

    public function cleanup(): void
    {
        $this->rrmdir($this->extractDir);
    }

    /**
     * Resolve a CSV value to an extracted image file path (if possible).
     *
     * @param string|null $csvValue
     * @return string|null
     * @throws Exception on ambiguous basename matches
     */
    public function resolve(?string $csvValue): ?string
    {
        $csvValue = trim((string)$csvValue);
        if ($csvValue === '') {
            return null;
        }

        // URL: let existing downloader handle it
        if (preg_match('~^https?://~i', $csvValue)) {
            return null;
        }

        // Absolute unix path
        if (substr($csvValue, 0, 1) === '/') {
            return null;
        }

        // Absolute windows path like C:\ or C:/
        if (preg_match('~^[a-zA-Z]:[\\\\/]~', $csvValue)) {
            return null;
        }

        // Normalize slashes
        $key = str_replace('\\', '/', $csvValue);

        // Try relative match first
        if (isset($this->byRelative[$key])) {
            return $this->byRelative[$key];
        }

        // Fallback by basename
        $base = basename($key);
        if (!isset($this->byBase[$base])) {
            return null;
        }

        if (count($this->byBase[$base]) > 1) {
            throw new Exception("Ambiguous image '{$base}' found multiple times in ZIP. Use relative path in CSV like 'folder/{$base}'.");
        }

        return $this->byBase[$base][0];
    }

    /**
     * Safely extract ZIP:
     * - limits number of files
     * - limits total uncompressed size
     * - prevents zip-slip path traversal
     * - extracts ONLY allowed image extensions
     *
     * @param string $zipTmpPath
     * @throws Exception
     */
    private function extractZipSafely(string $zipTmpPath): void
    {
        $zip = new ZipArchive();
        $ok = $zip->open($zipTmpPath);
        if ($ok !== true) {
            throw new Exception('Unable to open ZIP file.');
        }

        $maxFiles = 5000;
        $maxTotalUncompressed = 300 * 1024 * 1024; // 300MB
        $total = 0;

        if ($zip->numFiles > $maxFiles) {
            $zip->close();
            throw new Exception('ZIP contains too many files.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $name = (string)$stat['name'];
            if ($name === '') {
                continue;
            }

            // Skip directories (PHP 7.4 compatible)
            if (substr($name, -1) === '/') {
                continue;
            }

            // Normalize slashes
            $name = str_replace('\\', '/', $name);

            // Block null bytes
            if (strpos($name, "\0") !== false) {
                $zip->close();
                throw new Exception('ZIP contains invalid file names.');
            }

            // Prevent zip-slip traversal:
            // - contains "../"
            // - starts with "../"
            // - starts with "/"
            // - windows drive "C:/"
            if (strpos($name, '../') !== false || substr($name, 0, 3) === '../' || substr($name, 0, 1) === '/' || preg_match('~^[a-zA-Z]:/~', $name)) {
                $zip->close();
                throw new Exception('ZIP contains invalid paths.');
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXT, true)) {
                continue; // ignore non-images
            }

            $total += (int)($stat['size'] ?? 0);
            if ($total > $maxTotalUncompressed) {
                $zip->close();
                throw new Exception('ZIP total uncompressed size too large.');
            }

            $targetPath = $this->extractDir . DIRECTORY_SEPARATOR . $name;
            $targetDir  = dirname($targetPath);

            if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
                $zip->close();
                throw new Exception('Unable to create ZIP extraction directory.');
            }

            $stream = $zip->getStream($stat['name']);
            if (!$stream) {
                $zip->close();
                throw new Exception('Unable to read a file from ZIP.');
            }

            $out = fopen($targetPath, 'wb');
            if (!$out) {
                fclose($stream);
                $zip->close();
                throw new Exception('Unable to write extracted file.');
            }

            while (!feof($stream)) {
                $buf = fread($stream, 8192);
                if ($buf === false) {
                    break;
                }
                fwrite($out, $buf);
            }

            fclose($out);
            fclose($stream);
        }

        $zip->close();
    }

    /**
     * Index extracted image files
     * @throws Exception
     */
    private function indexFiles(): void
    {
        $baseDir = realpath($this->extractDir);
        if (!$baseDir) {
            throw new Exception('ZIP extraction directory invalid.');
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->extractDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($it as $fileInfo) {
            /** @var SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }

            $fullPath = $fileInfo->getRealPath();
            if (!$fullPath) {
                continue;
            }

            // Ensure file stays inside extraction dir
            if (strpos($fullPath, $baseDir . DIRECTORY_SEPARATOR) !== 0) {
                continue;
            }

            $rel = substr($fullPath, strlen($baseDir) + 1);
            $rel = str_replace('\\', '/', $rel);

            $this->byRelative[$rel] = $fullPath;

            $base = basename($rel);
            if (!isset($this->byBase[$base])) {
                $this->byBase[$base] = [];
            }
            $this->byBase[$base][] = $fullPath;
        }
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $file) {
            /** @var SplFileInfo $file */
            if ($file->isDir()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }

        @rmdir($dir);
    }
}
