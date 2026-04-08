<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section section--page">
    <div class="container container--narrow">
        <div class="row">
            <?php $this->includeTemplate('_partial/blogSidePanel.php'); ?>
            <div class="col-xl-9 col-lg-8" id="listItem">
                <!--Post details start here-->
                <div class="box box--blog -border">
                    <div class="box__head -no-shadow -no-padding-bottom">
                        <div class="slider-single slider-single-js">
                            <?php foreach ($post_images as $post_image) { ?>
                                <div>
                                    <div class="blog__media"><img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('image', 'showById', [$post_image['file_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $blogPostData['post_title']; ?>"></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box__body -padding-20 -no-padding-bottom">
                        <span class="inline-icon -display-inline -color-fill">
                            <span class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="16" viewBox="0 0 13 16">
                                    <path d="M903.143,732h-9.286A1.824,1.824,0,0,0,892,733.778V748l6.5-2.667L905,748V733.778A1.824,1.824,0,0,0,903.143,732Z" transform="translate(-892 -732)"></path>
                                </svg>
                            </span>
                        </span>
                        <?php
                        $categoryIds = !empty($blogPostData['categoryIds']) ? explode(',', $blogPostData['categoryIds']) : array();
                        $categoryNames = !empty($blogPostData['categoryNames']) ? explode('~', $blogPostData['categoryNames']) : array();
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
                        <h1><?php echo $blogPostData['post_title']; ?></h1>
                        <hr>
                            <div class="row row--cols align-items-center justify-content-between">
                                <div class="col-xl-6 col-lg-5 col-md-5 col-sm-5">
                                    <span class="blog__author -display-inline">
                                        <div class="avtar avtar--xsmall -display-inline" data-title="<?php echo CommonHelper::getFirstChar($blogPostData['post_author_name']); ?>">
                                        </div>
                                        <strong><?php echo Label::getLabel('Lbl_By'); ?></strong> <a href="javascript:void(0)" class="text--dark"><?php echo $blogPostData['post_author_name']; ?></a>
                                    </span>
                                </div>
                                <div class="col-xl-6 col-lg-7 col-md-7  col-sm-7 -align-right">
                                    <div class="blog__actions">
                                        <span class="blog__action -display-inline">
                                            <span class="inline-icon -display-inline -color-fill">
                                                <span class="svg-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14.844" height="16" viewBox="0 0 14.844 16">
                                                        <path d="M563.643,153.571h2.571v2.572h-2.571v-2.572Zm3.143,0h2.857v2.572h-2.857v-2.572Zm-3.143-3.428h2.571V153h-2.571v-2.857Zm3.143,0h2.857V153h-2.857v-2.857ZM563.643,147h2.571v2.571h-2.571V147Zm6.571,6.571h2.857v2.572h-2.857v-2.572ZM566.786,147h2.857v2.571h-2.857V147Zm6.857,6.571h2.571v2.572h-2.571v-2.572Zm-3.429-3.428h2.857V153h-2.857v-2.857Zm-3.227-4.656a0.278,0.278,0,0,1-.2.084h-0.572a0.287,0.287,0,0,1-.285-0.285v-2.572a0.287,0.287,0,0,1,.285-0.285h0.572a0.287,0.287,0,0,1,.285.285v2.572A0.278,0.278,0,0,1,566.987,145.487Zm6.656,4.656h2.571V153h-2.571v-2.857ZM570.214,147h2.857v2.571h-2.857V147Zm3.429,0h2.571v2.571h-2.571V147Zm0.2-1.513a0.278,0.278,0,0,1-.2.084h-0.572a0.289,0.289,0,0,1-.285-0.285v-2.572a0.289,0.289,0,0,1,.285-0.285h0.572a0.289,0.289,0,0,1,.286.285v2.572A0.279,0.279,0,0,1,573.844,145.487Zm3.174-1.576a1.1,1.1,0,0,0-.8-0.34h-1.143v-0.857a1.431,1.431,0,0,0-1.428-1.428h-0.572a1.432,1.432,0,0,0-1.428,1.428v0.857h-3.429v-0.857a1.431,1.431,0,0,0-1.428-1.428h-0.572a1.431,1.431,0,0,0-1.428,1.428v0.857h-1.143a1.16,1.16,0,0,0-1.143,1.143v11.429a1.16,1.16,0,0,0,1.143,1.143h12.571a1.16,1.16,0,0,0,1.143-1.143V144.714A1.1,1.1,0,0,0,577.018,143.911Z" transform="translate(-562.5 -141.281)" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <?php echo MyDate::formatDate($blogPostData['post_published_on']); ?>
                                        </span>
                                        | &nbsp;&nbsp;
                                        <a href="javascript:void(0)" class="blog__action -display-inline">
                                            <span class="inline-icon -display-inline -color-fill">
                                                <span class="svg-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 57 57">
                                                        <path d="M37.4,30.28H19.594a1.781,1.781,0,1,0,0,3.562H37.4A1.781,1.781,0,1,0,37.4,30.28Zm3.562-10.685H16.033a1.781,1.781,0,1,0,0,3.562H40.965A1.781,1.781,0,1,0,40.965,19.595ZM28.5,0C12.763,0,0,11.167,0,24.937c0,7.87,4.176,14.876,10.685,19.446v12.61L23.172,49.42a32.715,32.715,0,0,0,5.327.451c15.738,0,28.495-11.163,28.495-24.933S44.237,0,28.5,0Zm0,46.3a28.819,28.819,0,0,1-6.019-.654L14.095,50.7l0.112-8.26C7.78,38.569,3.566,32.179,3.566,24.937c0-11.8,11.163-21.371,24.933-21.371s24.933,9.569,24.933,21.371S42.269,46.309,28.5,46.309Z" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <?php echo $commentsCount; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="cms-container">
                                <?php echo FatUtility::decodeHtmlEntities($blogPostData['post_description']); ?>
                            </div>
                    </div>
                    <div class="box__footer -padding-20 -no-padding-top">
                        <hr />
                        <div class="row row--cols align-items-center justify-content-between">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 -align-right">
                                <ul class="social--share clearfix">
                                    <li class="social--fb"><a class='st-custom-button' data-network="facebook" displayText='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg" alt="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>"></a></li>
                                    <li class="social--tw"><a class='st-custom-button' data-network="twitter" displayText='<?php echo Label::getLabel('LBL_TWITTER'); ?>' title='<?php echo Label::getLabel('LBL_TWITTER'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg" alt="<?php echo Label::getLabel('LBL_TWITTER'); ?>"></a></li>
                                    <li class="social--pt"><a class='st-custom-button' data-network="pinterest" displayText='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg" alt="<?php echo Label::getLabel('LBL_PINTEREST'); ?>"></a></li>
                                    <li class="social--mail"><a class='st-custom-button' data-network="email" displayText='<?php echo Label::getLabel('LBL_EMAIL'); ?>' title='<?php echo Label::getLabel('LBL_EMAIL'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg" alt="<?php echo Label::getLabel('LBL_EMAIL'); ?>"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Post details end here-->
                <span class="-gap"></span>
                <!--Post comments start here-->
                <?php if ($blogPostData['post_comment_opened']) { ?>
                    <div class="box -border">
                        <div class="box__head">
                            <h5><?php echo ($commentsCount) ? sprintf(Label::getLabel('Lbl_Comments(%s)'), $commentsCount) : Label::getLabel('LBL_COMMENTS'); ?></h5>
                            <?php echo $srchCommentsFrm->getFormHtml(); ?>
                        </div>
                        <div class="-padding-20">
                            <?php if ($blogPostData['post_comment_opened']) { ?>
                                <div class="container--repeated">
                                    <div id="comments--listing"></div>
                                </div>
                            <?php } ?>
                            <?php if ($blogPostData['post_comment_opened'] && $siteUserId > 0) { ?>
                                <div id="form-comments" class="form--comments">
                                    <h4><?php echo Label::getLabel('Lbl_Post_your_comments'); ?></h4>
                                    <?php
                                    $postCommentFrm->setFormTagAttribute('class', 'form');
                                    $postCommentFrm->setFormTagAttribute('onsubmit', 'setupPostComment(this);return false;');
                                    $postCommentFrm->developerTags['colClassPrefix'] = 'col-md-';
                                    $postCommentFrm->developerTags['fld_default_col'] = '12';
                                    $nameFld = $postCommentFrm->getField('bpcomment_author_name');
                                    $nameFld->addFieldTagAttribute('readonly', true);
                                    $nameFld->developerTags['col'] = 6;
                                    $emailFld = $postCommentFrm->getField('bpcomment_author_email');
                                    $emailFld->addFieldTagAttribute('readonly', true);
                                    $emailFld->developerTags['col'] = 6;
                                    echo $postCommentFrm->getFormHtml();
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!--Post comments end here-->
        </div>
    </div>
</div>
</section>
<script>
    var boolLoadComments = (<?php echo FatUtility::int($blogPostData['post_comment_opened']); ?>) ? true : false;
<?php if ($blogPostData['post_comment_opened'] == AppConstant::YES) { ?>
        $(document).ready(function () {
            searchComments(<?php echo $blogPostData['post_id']; ?>, 1);
        });
<?php } ?>
    /* BLOG SLIDER */
    $('.slider-single-js').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        adaptiveHeight: true,
        prevArrow: '<a data-role="none" class="slick-prev" aria-label="previous"></a>',
        nextArrow: '<a data-role="none" class="slick-next" aria-label="next"></a>'
    });
    /* FOR LEFT LINKS */
    $('.link--toggle-js').click(function () {
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            $(this).next('.nav--toggled-js > ul > li ul').find('.link--toggle-js').removeClass('is-active');
            $(this).next('.nav--toggled-js > ul > li ul').slideUp();
            $(this).next('.nav--toggled-js > ul > li ul').find('.nav--toggled-js > ul > li ul').slideUp();
            return false;
        }
        $('.link--toggle-js').removeClass('is-active');
        $(this).addClass("is-active");
        $(this).parents('ul').each(function () {
            $(this).siblings('span').addClass('is-active');
        });
        $(this).closest('ul').find('li .nav--toggled-js > ul > li ul').slideUp();
        $(this).next('.nav--toggled-js > ul > li ul').slideDown();
    });
    /* FOR BLOG FILTERS */
    $('.blog-toggle-js').click(function () {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-categories-js");
    });
</script>
<style>
    .box--blog a {color: #0037B4;}
</style>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>