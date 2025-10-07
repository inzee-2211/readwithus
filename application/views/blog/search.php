<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (!empty($postList)) {
    foreach ($postList as $blogPost) {
        ?>
        <div class="blog-row">
            <div class="container container--narrow">
                <div class="row justify-content-center align-items-center">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-5">
                        <div class="blog__media ratio ratio--4by3">
                            <a href="<?php echo MyUtility::makeUrl('Blog', 'postDetail', array($blogPost['post_id'])); ?>">
                                <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('image', 'show', array(Afile::TYPE_BLOG_POST_IMAGE, $blogPost['post_id'], Afile::SIZE_MEDIUM)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $blogPost['post_title']; ?>">
                            </a>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-7">
                        <div class="blog__content">
                            <span class="inline-icon -display-inline -color-fill">
                                <span class="svg-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="16" viewBox="0 0 13 16">
                                        <path d="M903.143,732h-9.286A1.824,1.824,0,0,0,892,733.778V748l6.5-2.667L905,748V733.778A1.824,1.824,0,0,0,903.143,732Z" transform="translate(-892 -732)" />
                                    </svg>
                                </span>
                            </span>
                            <?php
                            $categoryIds = !empty($blogPost['categoryIds']) ? explode(',', $blogPost['categoryIds']) : array();
                            $categoryNames = !empty($blogPost['categoryNames']) ? explode('~', $blogPost['categoryNames']) : array();
                            $categoryCodes = !empty($blogPost['categoryCodes']) ? explode(',', $blogPost['categoryCodes']) : array();
                            $categories = array_combine($categoryIds, $categoryNames);
                            ?>
                            <?php
                            if (!empty($categories)) {
                                foreach ($categories as $id => $name) {
                                    if ($name == end($categories)) {
                                        ?>
                                        <a href="<?php echo MyUtility::makeUrl('Blog', 'category', array($id)); ?>" class="text--dark"><?php echo $name; ?></a>
                                        <?php
                                        break;
                                    }
                                    ?>
                                    <a href="<?php echo MyUtility::makeUrl('Blog', 'category', array($id)); ?>" class="text--dark"><?php echo $name; ?></a>,
                                    <?php
                                }
                            }
                            ?>
                            <h2><a href="<?php echo MyUtility::makeUrl('Blog', 'postDetail', array($blogPost['post_id'])); ?>" title="<?php echo $blogPost['post_title']; ?>"><?php echo $blogPost['post_title']; ?></a></h2>
                            <div class="blog__actions">
                                <span class="blog__action -display-inline">
                                    <span class="inline-icon -display-inline -color-fill">
                                        <span class="svg-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14.844" height="16" viewBox="0 0 14.844 16">
                                                <path d="M563.643,153.571h2.571v2.572h-2.571v-2.572Zm3.143,0h2.857v2.572h-2.857v-2.572Zm-3.143-3.428h2.571V153h-2.571v-2.857Zm3.143,0h2.857V153h-2.857v-2.857ZM563.643,147h2.571v2.571h-2.571V147Zm6.571,6.571h2.857v2.572h-2.857v-2.572ZM566.786,147h2.857v2.571h-2.857V147Zm6.857,6.571h2.571v2.572h-2.571v-2.572Zm-3.429-3.428h2.857V153h-2.857v-2.857Zm-3.227-4.656a0.278,0.278,0,0,1-.2.084h-0.572a0.287,0.287,0,0,1-.285-0.285v-2.572a0.287,0.287,0,0,1,.285-0.285h0.572a0.287,0.287,0,0,1,.285.285v2.572A0.278,0.278,0,0,1,566.987,145.487Zm6.656,4.656h2.571V153h-2.571v-2.857ZM570.214,147h2.857v2.571h-2.857V147Zm3.429,0h2.571v2.571h-2.571V147Zm0.2-1.513a0.278,0.278,0,0,1-.2.084h-0.572a0.289,0.289,0,0,1-.285-0.285v-2.572a0.289,0.289,0,0,1,.285-0.285h0.572a0.289,0.289,0,0,1,.286.285v2.572A0.279,0.279,0,0,1,573.844,145.487Zm3.174-1.576a1.1,1.1,0,0,0-.8-0.34h-1.143v-0.857a1.431,1.431,0,0,0-1.428-1.428h-0.572a1.432,1.432,0,0,0-1.428,1.428v0.857h-3.429v-0.857a1.431,1.431,0,0,0-1.428-1.428h-0.572a1.431,1.431,0,0,0-1.428,1.428v0.857h-1.143a1.16,1.16,0,0,0-1.143,1.143v11.429a1.16,1.16,0,0,0,1.143,1.143h12.571a1.16,1.16,0,0,0,1.143-1.143V144.714A1.1,1.1,0,0,0,577.018,143.911Z" transform="translate(-562.5 -141.281)" />
                                            </svg>
                                        </span>
                                    </span>
                                    <span class="text--dark"><?php echo MyDate::formatDate($blogPost['post_published_on']); ?> </span>
                                </span>
                            </div>
                            <span class="-gap"></span>
                            <div><?php echo nl2br($blogPost['post_short_description']); ?>                            </div>
                            <span class="-gap"></span>
                            <a class="btn btn--primary btn--wide btn--large" href="<?php echo MyUtility::makeUrl('Blog', 'postDetail', array($blogPost['post_id'])); ?>"><?php echo Label::getLabel('Lbl_View_Full_Post'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    $postedData['page'] = $page;
    echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmBlogSearchPaging'));
    $pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'callBackJsFunc' => 'goToSearchPage');
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
} else {
    ?>
    <div class="box -padding-30" style="margin-bottom: 30px;">
        <div class="message-display">
            <div class="message-display__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                    <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
                </svg>
            </div>
            <h5><?php echo Label::getLabel('LBL_No_Result_Found!!'); ?></h5>
            <a href="#" class="btn btn--primary btn--wide btn--large"><?php echo Label::getLabel('LBL_Search_Again'); ?></a>
        </div>
    </div>
    <?php
}
