<?php

class BulkQuestionImporter
{
    private int $userId;
    private int $userType;
    private int $langId;
  private array $stats = ['total'=>0,'success'=>0,'failed'=>0,'errors'=>[], 'question_ids'=>[]];

    public function __construct(int $userId, int $userType, int $langId)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->langId = $langId;
    }
private function detectDelimiter($handle): string
{
    // Remember current position (should be 0 at start, but safe anyway)
    $pos = ftell($handle);

    $line = '';
    // Read until we get a non-empty line or reach EOF
    while ($line === '' && !feof($handle)) {
        $line = trim((string) fgets($handle));
    }

    // If file is empty or only blank lines -> fall back to comma
    if ($line === '') {
        fseek($handle, $pos);
        return ',';
    }

    $commaCount = substr_count($line, ',');
    $semiCount  = substr_count($line, ';');

    // Choose the one that appears more often; default to comma
    $delimiter = ($semiCount > $commaCount) ? ';' : ',';

    // Reset file pointer so import() can read from the beginning
    fseek($handle, $pos);

    return $delimiter;
}

  public function import(array $csvFile, ?array $zipFile = null): array
{
    if (($csvFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new Exception('CSV upload failed.');
    }
    $zipDir = $this->extractZipIfAny($zipFile);

    $fh = fopen($csvFile['tmp_name'], 'r');
    if (!$fh) {
        throw new Exception('Could not open CSV.');
    }

    // 🔍 Auto-detect delimiter: supports both "," and ";"
    $delimiter = $this->detectDelimiter($fh);

    // header
    $header = fgetcsv($fh, 0, $delimiter);
    if (!$header) {
        throw new Exception('Empty CSV.');
    }
    $header = array_map([$this, 'norm'], $header);

    // rows
    while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
        $this->stats['total']++;
        $line = $this->stats['total'] + 1; // accounting header

        // protect against malformed rows
        if (count($row) != count($header)) {
            $this->fail("Row $line: column count mismatch");
            continue;
        }

        $data = array_combine($header, $row);
        try {
            $this->importRow($data, $zipDir, $line);
            $this->stats['success']++;
        } catch (Exception $e) {
            $this->fail("Row $line: " . $e->getMessage());
        }
    }
    fclose($fh);

    // cleanup
    if ($zipDir && is_dir($zipDir)) {
        $this->rrmdir($zipDir);
    }

    return $this->stats;
}

    private function importRow(array $data, ?string $zipDir, int $line): void
    {
        // Accept BOTH schemas — map into the existing single-form keys:
        // Friendly: title/type/marks/... OR DB-like: question_title/question_type/...

          error_log("Processing row $line: " . json_encode($data));
        $get = fn($keys, $default='') => $this->coalesce($data, $keys, $default);


$typeRaw = $get(['type','question_type'], '');
      // Accept BOTH header styles; coalesce returns '' if nothing found
$post = [
    'question_id'             => 0,

    // strings expected
    'grpcls_title'            => (string)$get(['title','question_title']),
    'grpcls_description'      => (string)$get(['description','question_desc']),
    'grpcls_description_math' => (string)$get(['math_equation','question_math_equation']),
    'grpcls_total_marks'      => (string)( (int)$get(['marks','question_marks'], 0) ),
    'grpcls_tlang_id'         => (string)$this->normalizeQuestionType($typeRaw), // 1/2/3
    'grpcls_hint'             => (string)$get(['hint','question_hint']),

    // IMPORTANT: MUST exist; pass strings, not null
    'course_cate_id'          => (string)( (int)$get(['category_id','question_cat'], 0) ),
    'course_subcate_ida'      => (string)( (int)$get(['subcategory_id','question_subcat'], 0) ),

    // For MCQ – pass '' if not present (do NOT pass null)
    'question_option_1'       => (string)$get(['option_1','question_option_1'], ''),
    'question_option_2'       => (string)$get(['option_2','question_option_2'], ''),
    'question_option_3'       => (string)$get(['option_3','question_option_3'], ''),
    'question_option_4'       => (string)$get(['option_4','question_option_4'], ''),

    'question_answers'        => (string)$get(['correct_answers','question_answers'], ''),
];

// required basics
if ($post['grpcls_title'] === '') { throw new Exception('title/question_title is required.'); }
if ((int)$post['grpcls_total_marks'] <= 0) { throw new Exception('marks/question_marks is required and must be > 0.'); }
if ((int)$post['course_cate_id'] <= 0) { throw new Exception('category_id/question_cat is required.'); }

// image resolution as before...

        // image
        $imageRef = $get(['image','image_filename'], '');
        $imagePath = $this->resolveImage($imageRef, $zipDir);

        // same as single flow
        $post['grpcls_teacher_id'] = $this->userId;
        $post['image'] = $imagePath ? 1 : 0;

        $q = new QuestionClass(0, $this->userId, $this->userType);
        if (!$q->saveClass($post)) {
            throw new Exception($q->getError());
        }
        $qid = $q->getMainTableRecordId();
        $this->stats['question_ids'][] = (int)$qid;

        if ($imagePath) {
            $fileData = [
                'name' => basename($imagePath),
                'type' => $this->mime($imagePath),
                'tmp_name' => $imagePath,
                'error' => 0,
                'size' => filesize($imagePath)
            ];
            $file = new Afile(Afile::TYPE_LESSON_QUESTIONS_FILE);
            if (!$file->saveFile($fileData, $qid)) {
                // non-fatal: we still keep the question
                $this->stats['errors'][] = "Row $line image: " . $file->getError();
            }
        }
    }

    /* ------------ helpers ------------ */

    private function extractZipIfAny(?array $zipFile): ?string
    {
        if (!$zipFile || ($zipFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;

        $dir = sys_get_temp_dir() . '/qzip_' . uniqid();
        if (!mkdir($dir, 0777, true)) throw new Exception('Could not prepare temp folder for images.');

        $zip = new ZipArchive();
        if ($zip->open($zipFile['tmp_name']) !== TRUE) {
            throw new Exception('Could not read ZIP.');
        }
        $zip->extractTo($dir);
        $zip->close();
        return $dir;
    }

    private function resolveImage(string $ref, ?string $zipDir): ?string
    {
        $ref = trim($ref);
        if ($ref === '') return null;

        if (preg_match('~^https?://~i', $ref)) {
            $tmp = tempnam(sys_get_temp_dir(), 'qimg_');
            $bin = @file_get_contents($ref);
            if ($bin === false) return null;
            file_put_contents($tmp, $bin);
            return $tmp;
        }
        if ($zipDir) {
            $candidate = $zipDir . '/' . $ref;
            if (file_exists($candidate)) return $candidate;

            // try basename anywhere under zipDir
            $base = basename($ref);
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($zipDir, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if ($f->isFile() && basename($f->getPathname()) === $base) {
                    return $f->getPathname();
                }
            }
        }
        return null;
    }
private function normalizeQuestionType($raw): int
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        // default: single choice
        return 1;
    }

    // If it's a simple digit, validate 1–3
    if (ctype_digit($raw)) {
        $num = (int) $raw;
        if ($num >= 1 && $num <= 3) {
            return $num;
        }
    }

    $lower = strtolower($raw);

    // Common text values from your CSVs
    if (in_array($lower, ['mcq', 'single', 'single_choice', 'single choice'], true)) {
        return 1; // single choice
    }
    if (in_array($lower, ['multiple', 'multi', 'multiple_choice', 'multiple choice', 'mrq'], true)) {
        return 2; // multiple choice
    }
    if (in_array($lower, ['text', 'short_text', 'short text', 'short answer', 'short_answer'], true)) {
        return 3; // text
    }

    // If we get here, it's not valid – better to fail loudly
    throw new Exception("Invalid question type '{$raw}'. Use 1, 2, 3 or MCQ/MULTIPLE/TEXT.");
}

  private function coalesce(array $src, array $keys, $default = '')
{
    foreach ($keys as $k) {
        $n = $this->norm($k);
        if (array_key_exists($n, $src) && $src[$n] !== '') {
            return $src[$n]; // return as-is
        }
    }
    return $default; // will be '' or '0' per the caller
}

    private function norm(string $h): string
{
    // Remove UTF-8 BOM if present at the beginning
    // (three bytes: 0xEF 0xBB 0xBF)
    $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);

    // Normalize spaces → underscore, lowercase, trim
    return strtolower(trim(preg_replace('/\s+/', '_', $h)));
}

    private function mime(string $path): string
    {
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    private function rrmdir(string $dir): void
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $f) { $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname()); }
        @rmdir($dir);
    }

    private function fail(string $msg): void
    {
        $this->stats['failed']++; $this->stats['errors'][] = $msg;
    }
}
