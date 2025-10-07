<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($bibles) { ?>
    <div class="result-container margin-top-4" >
        <div class="row">
            <?php
            foreach ($bibles as $bible) {
                $viDdata = CommonHelper::getVideoDetail($bible['biblecontent_url']);
                ?>
                <div class="col-md-6">
                    <div class="box box-list -padding-30 -hover-shadow -transition">
                        <h5 class="page-title margin-bottom-4"> <?php echo isset($bible['biblecontentlang_biblecontent_title']) ? $bible['biblecontentlang_biblecontent_title'] : $bible['biblecontent_title']; ?> </h5>
                        <div class="iframe-box ratio ratio--16by9">
                            <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?php echo $viDdata['video_id']; ?>"  frameborder="0" allow="encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmBibleSearchPaging'));
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
} else {
    $this->includeTemplate('_partial/no-record-found.php', $pagingArr, false);
}
?>