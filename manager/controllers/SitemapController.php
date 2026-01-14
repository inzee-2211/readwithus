<?php

/**
 * Sitemap Controller is used to handle Sitemaps
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class SitemapController extends AdminBaseController
{

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
    // public function generate()
    // {
    //     $this->objPrivilege->canEditSiteMap();
    //       global $sitemapListInc;
    // $sitemapListInc = 0;
    
    //     $this->startSitemapXml();
    //     $urls = Sitemap::getUrls($this->siteLangId);
    //     foreach ($urls as $url) {
    //         foreach ($url as $val) {
    //             $this->writeSitemapUrl($val['url'], $val['frequency']);
    //         }
    //     }
    //     $this->endSitemapXml();
    //     $this->writeSitemapIndex();
    //     FatUtility::dieJsonSuccess(Label::getLabel('MSG_SITEMAP_HAS_BEEN_UPDATED_SUCCESSFULLY'));
    // }
    public function generate()
{
    $this->objPrivilege->canEditSiteMap();

    // ✅ Reset counter for each generation
    global $sitemapListInc;
    $sitemapListInc = 0;

    // ✅ Remove old list_*.xml so stale data never remains
    $this->cleanupOldSitemaps();

    $this->startSitemapXml();

    $urls = Sitemap::getUrls($this->siteLangId);

    // ✅ FIXED LOOP (see Fix 2 below)
    foreach ($urls as $section => $items) {
        foreach ($items as $val) {
            if (!empty($val['url'])) {
                $this->writeSitemapUrl($val['url'], $val['frequency'] ?? 'weekly');
            }
        }
    }

    $this->endSitemapXml();
    $this->writeSitemapIndex();

    FatUtility::dieJsonSuccess(Label::getLabel('MSG_SITEMAP_HAS_BEEN_UPDATED_SUCCESSFULLY'));
}

private function cleanupOldSitemaps()
{
    $dir = CONF_INSTALLATION_PATH . 'sitemap/';
    if (!is_dir($dir)) return;

    foreach (glob($dir . 'list_*.xml') as $file) {
        @unlink($file);
    }
}

    /**
     * Start Sitemap XML
     */
    private function startSitemapXml()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    }

    /**
     * Write Sitemap Url
     * 
     * @staticvar int $sitemap_i
     * 
     * @param type $url
     * @param type $freq
     */
    private function writeSitemapUrl($url, $freq)
    {
        static $sitemap_i;
        $sitemap_i++;
        if ($sitemap_i > 2000) {
            $sitemap_i = 1;
            $this->endSitemapXml();
            $this->startSitemapXml();
        }
        echo "<url>
                <loc> " . htmlentities($url) . "</loc>
                <lastmod>" . date('Y-m-d') . "</lastmod>
<changefreq>" . htmlentities($freq ?: 'weekly') . "</changefreq>
                <priority>0.8</priority>
            </url>";
        echo "\n";
    }

    /**
     * End Sitemap XML
     * 
     * @global type $sitemapListInc
     */
    // private function endSitemapXml()
    // {
    //     global $sitemapListInc;
    //     $sitemapListInc++;
    //     echo '</urlset>' . "\n";
    //     $contents = ob_get_clean();
    //     $rs = '';
    //     MyUtility::writeFile('sitemap/list_' . $sitemapListInc . '.xml', $contents, $rs);
    // }
    private function endSitemapXml()
{
    global $sitemapListInc;
    $sitemapListInc++;

    echo '</urlset>' . "\n";
    $contents = ob_get_clean();

    $rs = '';
    $fileRel = 'sitemap/list_' . $sitemapListInc . '.xml';
    $ok = MyUtility::writeFile($fileRel, $contents, $rs);

    if (!$ok) {
        FatUtility::dieJsonError('Sitemap write failed: ' . $rs);
    }
}


    /**
     * Write Sitemap Index
     * 
     * @global type $sitemapListInc
     */
 private function writeSitemapIndex()
{
    global $sitemapListInc;

    ob_start();
    echo "<?xml version='1.0' encoding='UTF-8'?>\n";
    echo "<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

    for ($i = 1; $i <= $sitemapListInc; $i++) {
        echo "<sitemap><loc>" . MyUtility::makeFullUrl('', '', [], CONF_WEBROOT_FRONT_URL) . "sitemap/list_" . $i . ".xml</loc></sitemap>\n";
    }

    echo "</sitemapindex>";
    $contents = ob_get_clean();

    $rs = '';
    $ok = MyUtility::writeFile('sitemap.xml', $contents, $rs);

    if (!$ok) {
        FatUtility::dieJsonError('Sitemap index write failed: ' . $rs);
    }
}

}
