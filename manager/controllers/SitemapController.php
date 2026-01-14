<?php

/**
 * Sitemap Controller is used to handle Sitemaps
 *
 * Backwards-compatible version:
 * - Writes sitemap into the SAME place your live site is serving from (user-uploads/)
 * - Still supports legacy behavior if user-uploads path isn't present
 * - Does NOT fail silently: returns JSON error if any write/delete fails
 *
 * @package YoCoach
 * @author Fatbit
 */
class SitemapController extends AdminBaseController
{
    /** @var int */
    private $maxUrlsPerFile = 2000;

    /**
     * Initialize Sitemap
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewSiteMap();
    }

    /**
     * Generate Sitemap
     */
    public function generate()
    {
        $this->objPrivilege->canEditSiteMap();

        // Reset counters each run
        global $sitemapListInc;
        $sitemapListInc = 0;

        // Reset per-file url counter
        $this->resetUrlCounter();

        // Ensure directories + cleanup old list_*.xml
        $this->cleanupOldSitemapsOrFail();

        // Start buffering first file
        $this->startSitemapXml();

        $urls = Sitemap::getUrls($this->siteLangId);

        // Backwards-compatible loop:
        // Supports both:
        // 1) ['Teachers' => [ ['url'=>..., 'frequency'=>...], ... ], 'Courses' => [...]]
        // 2) [ ['url'=>..., 'frequency'=>...], ['url'=>..., ...] ] (older)
        foreach ($urls as $key => $val) {
            if (is_array($val) && isset($val['url'])) {
                // style #2 (flat)
                $this->safeWrite($val);
                continue;
            }

            if (is_array($val)) {
                // style #1 (sections)
                foreach ($val as $item) {
                    $this->safeWrite($item);
                }
            }
        }

        // Close last sitemap file + build index
        $this->endSitemapXmlOrFail();
        $this->writeSitemapIndexOrFail();

        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SITEMAP_HAS_BEEN_UPDATED_SUCCESSFULLY'));
    }

    /* --------------------------------------------------------------------
     * Path helpers (Backwards compatible)
     * ------------------------------------------------------------------ */

    /**
     * Returns absolute directory where sitemap LIST files should be written.
     * Prefers user-uploads/sitemap/ (your live server is serving from there),
     * falls back to <install>/sitemap/ for legacy installs.
     */
    private function getSitemapDirAbs(): string
    {
        // Most YoCoach installs: CONF_INSTALLATION_PATH ends with trailing slash
        $base = rtrim((string)CONF_INSTALLATION_PATH, '/\\') . '/';

        // Preferred (your server currently has /var/www/html/user-uploads/sitemap/list_1.xml)
        $preferred = $base . 'user-uploads/sitemap/';

        // Legacy fallback
        $legacy = $base . 'sitemap/';

        // Choose existing dir if possible, else prefer preferred path
        if (is_dir($preferred)) {
            return $preferred;
        }
        if (is_dir($legacy)) {
            return $legacy;
        }
        return $preferred;
    }

    /**
     * Returns absolute path for sitemap index file (sitemap.xml).
     * Prefers user-uploads/sitemap.xml, falls back to <install>/sitemap.xml.
     */
    private function getSitemapIndexAbs(): string
    {
        $base = rtrim((string)CONF_INSTALLATION_PATH, '/\\') . '/';

        $preferred = $base . 'user-uploads/sitemap.xml';
        $legacy    = $base . 'sitemap.xml';

        // If preferred exists OR preferred base folder exists, use it
        if (file_exists($preferred) || is_dir($base . 'user-uploads/')) {
            return $preferred;
        }
        return $legacy;
    }

    /**
     * Returns public URL prefix for list files in sitemap index.
     * If writing into user-uploads/ then URL should include user-uploads/.
     */
    private function getPublicListBaseUrl(): string
    {
        $base = rtrim((string)CONF_WEBROOT_FRONT_URL, '/') . '/';

        // If we are writing to user-uploads folder, index must reference it
        $dirAbs = $this->getSitemapDirAbs();
        if (strpos($dirAbs, '/user-uploads/sitemap/') !== false || strpos($dirAbs, 'user-uploads' . DIRECTORY_SEPARATOR . 'sitemap') !== false) {
            return $base . 'user-uploads/sitemap/';
        }

        // Legacy
        return $base . 'sitemap/';
    }

    /* --------------------------------------------------------------------
     * Core XML builders
     * ------------------------------------------------------------------ */

    /**
     * Start Sitemap XML buffer
     */
    private function startSitemapXml()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    }

    /**
     * Write a single URL entry, with file splitting at maxUrlsPerFile.
     */
    private function writeSitemapUrl(string $url, string $freq = 'weekly')
    {
        // Maintain per-run counter (NOT static across requests)
        $this->incUrlCounter();

        if ($this->getUrlCounter() > $this->maxUrlsPerFile) {
            // close current list file
            $this->endSitemapXmlOrFail();
            // reset url counter for next file
            $this->resetUrlCounter();
            // start new buffer
            $this->startSitemapXml();
            // count this one as first url in new file
            $this->incUrlCounter();
        }

        $freq = $freq ?: 'weekly';

        echo "<url>\n";
        echo "  <loc>" . htmlentities($url) . "</loc>\n";
        echo "  <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "  <changefreq>" . htmlentities($freq) . "</changefreq>\n";
        echo "  <priority>0.8</priority>\n";
        echo "</url>\n";
    }

    /**
     * End Sitemap XML buffer and write list_N.xml into correct folder.
     * Throws JSON error on failure (no silent fail).
     */
    private function endSitemapXmlOrFail()
    {
        global $sitemapListInc;
        $sitemapListInc++;

        echo '</urlset>' . "\n";
        $contents = ob_get_clean();

        $dir = $this->getSitemapDirAbs();
        $this->ensureDirOrFail($dir);

        $fileAbs = $dir . 'list_' . $sitemapListInc . '.xml';

        $ok = @file_put_contents($fileAbs, $contents);
        if ($ok === false) {
            $err = error_get_last();
            FatUtility::dieJsonError('Sitemap write failed: ' . $fileAbs . ' | ' . ($err['message'] ?? 'unknown error'));
        }

        @chmod($fileAbs, 0644);
    }

    /**
     * Write sitemap.xml index pointing to list_*.xml URLs.
     * Throws JSON error on failure.
     */
    private function writeSitemapIndexOrFail()
    {
        global $sitemapListInc;

        $listBaseUrl = $this->getPublicListBaseUrl();

        ob_start();
        echo "<?xml version='1.0' encoding='UTF-8'?>\n";
        echo "<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
        for ($i = 1; $i <= $sitemapListInc; $i++) {
            echo "  <sitemap><loc>" . $listBaseUrl . "list_" . $i . ".xml</loc></sitemap>\n";
        }
        echo "</sitemapindex>\n";
        $contents = ob_get_clean();

        $indexAbs = $this->getSitemapIndexAbs();
        $indexDir = dirname($indexAbs);

        $this->ensureDirOrFail($indexDir);

        $ok = @file_put_contents($indexAbs, $contents);
        if ($ok === false) {
            $err = error_get_last();
            FatUtility::dieJsonError('Sitemap index write failed: ' . $indexAbs . ' | ' . ($err['message'] ?? 'unknown error'));
        }

        @chmod($indexAbs, 0644);
    }

    /* --------------------------------------------------------------------
     * Cleanup (no silent failures)
     * ------------------------------------------------------------------ */

    /**
     * Remove old list_*.xml from whichever sitemap dir we will write into.
     * Creates directory if missing.
     */
    private function cleanupOldSitemapsOrFail()
    {
        $dir = $this->getSitemapDirAbs();
        $this->ensureDirOrFail($dir);

        $pattern = $dir . 'list_*.xml';
        $files = glob($pattern);
        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file) && !@unlink($file)) {
                    $err = error_get_last();
                    FatUtility::dieJsonError('Failed to delete old sitemap file: ' . $file . ' | ' . ($err['message'] ?? 'unknown error'));
                }
            }
        }
    }

    /**
     * Ensure directory exists and is writable.
     */
    private function ensureDirOrFail(string $dirAbs)
    {
        if (!is_dir($dirAbs)) {
            if (!@mkdir($dirAbs, 0775, true)) {
                $err = error_get_last();
                FatUtility::dieJsonError('Failed to create directory: ' . $dirAbs . ' | ' . ($err['message'] ?? 'unknown error'));
            }
        }

        if (!is_writable($dirAbs)) {
            FatUtility::dieJsonError('Directory not writable: ' . $dirAbs . ' (check permissions/owner: should be www-data)');
        }
    }

    /* --------------------------------------------------------------------
     * URL write safety
     * ------------------------------------------------------------------ */

    /**
     * Safely write an item from Sitemap::getUrls()
     */
    private function safeWrite($val)
    {
        if (!is_array($val)) return;
        if (empty($val['url'])) return;

        $url  = (string)$val['url'];
        $freq = (string)($val['frequency'] ?? 'weekly');

        $this->writeSitemapUrl($url, $freq);
    }

    /* --------------------------------------------------------------------
     * Per-request URL counter (avoid static bugs)
     * ------------------------------------------------------------------ */

    private function resetUrlCounter()
    {
        $GLOBALS['__rwu_sitemap_url_counter'] = 0;
    }

    private function incUrlCounter()
    {
        if (!isset($GLOBALS['__rwu_sitemap_url_counter'])) {
            $GLOBALS['__rwu_sitemap_url_counter'] = 0;
        }
        $GLOBALS['__rwu_sitemap_url_counter']++;
    }

    private function getUrlCounter(): int
    {
        return (int)($GLOBALS['__rwu_sitemap_url_counter'] ?? 0);
    }
}
