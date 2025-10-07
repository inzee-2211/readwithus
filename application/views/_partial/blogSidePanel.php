<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-xl-3 col-lg-4 order-xl-2 order-lg-2">
    <div class="blog-sidebar">
        <div class="form-search form-search--blog">
            <a href="javascript:void(0)" class="blog-toggle blog-toggle-js"><span></span></a>
            <form method="post" onsubmit="searchBlogss(this);return false;">
                <div class="form__element">
                    <input class="form__input" placeholder="<?php echo Label::getLabel('LBL_BLOG_SEARCHS'); ?>" name="keyword" type="text"/>
                    <input class="form__input" placeholder="<?php echo Label::getLabel('LBL_BLOG_SEARCHS'); ?>" name="page" value="post-detail" type="hidden"/>
                    <span class="form__action-wrap">
                        <input class="form__action" value="" type="submit"/>
                        <span class="svg-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14.844" height="14.843" viewBox="0 0 14.844 14.843">
                                <path d="M251.286,196.714a4.008,4.008,0,1,1,2.826-1.174A3.849,3.849,0,0,1,251.286,196.714Zm8.241,2.625-3.063-3.062a6.116,6.116,0,0,0,1.107-3.563,6.184,6.184,0,0,0-.5-2.442,6.152,6.152,0,0,0-3.348-3.348,6.271,6.271,0,0,0-4.884,0,6.152,6.152,0,0,0-3.348,3.348,6.259,6.259,0,0,0,0,4.884,6.152,6.152,0,0,0,3.348,3.348,6.274,6.274,0,0,0,6-.611l3.063,3.053a1.058,1.058,0,0,0,.8.34,1.143,1.143,0,0,0,.813-1.947h0Z" transform="translate(-245 -186.438)"></path>
                            </svg>
                        </span>
                    </span>
                </div>
            </form>
            <a href="" id="go_back" style="display:none;"><?php echo Label::getLabel('LBL_Go_Back'); ?></a>
        </div>
        <span class="-gap"></span>
        <!--blog search end here-->
        <!--blog cta start here-->
        <div class="box box--cta box--cta-blog -padding-30 -border -align-center">
            <div class="-hide-mobile">
                <h4 class="-text-bold -color-secondary"><?php echo Label::getLabel('Lbl_Write_For_Us'); ?></h4>
                <p><?php echo Label::getLabel('Lbl_We_are_constantly_looking_for_writers_and_contributors_to_help_us_create_great_content_for_our_blog_visitors.'); ?> </p>
            </div>
            <a href="<?php echo MyUtility::makeUrl('Blog', 'contributionForm'); ?>" class="btn btn--secondary btn--block btn--large"><span class="svg-icon"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 510 510" style="enable-background:new 0 0 510 510;" xml:space="preserve">
                        <g>
                            <g>
                                <path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M382.5,280.5h-102v102h-51v-102
                                      h-102v-51h102v-102h51v102h102V280.5z" />
                            </g>
                        </g>
                    </svg>
                </span> <?php echo Label::getLabel('Lbl_Contribute'); ?></a>
        </div>
        <!--blog cta end here-->
        <!--blog filters start here-->
        <span class="overlay overlay--blog blog-toggle-js"></span>
        <div class="blog-filters">
            <div class="box -border">
                <div class="box__head">
                    <h5><?php echo Label::getLabel('Lbl_Categories'); ?></h5>
                </div>
                <div class="box__body">
                    <nav class="nav nav--toggled nav--toggled-js">
                        <?php if (!empty($categoriesArr)) { ?>
                            <ul>
                                <?php foreach ($categoriesArr as $cat) { ?>
                                    <li class="">
                                        <a href="<?php echo MyUtility::makeUrl('Blog', 'category', array($cat['bpcategory_id'])); ?>"><?php
                                            echo $cat['bpcategory_name'];
                                            echo!empty($cat['countChildBlogPosts']) ? "($cat[countChildBlogPosts])" : '';
                                            ?></a>
                                        <?php if (count($cat['children'])) { ?>
                                            <ul>
                                                <?php foreach ($cat['children'] as $children) { ?>
                                                    <li class="">
                                                        <a href="<?php echo MyUtility::makeUrl('Blog', 'category', array($children['bpcategory_id'])); ?>"><?php
                                                            echo $children['bpcategory_name'];
                                                            echo!empty($children['countChildBlogPosts']) ? "($children[countChildBlogPosts])" : '';
                                                            ?></a>
                                                        <?php if (count($children['children'])) { ?>
                                                            <ul class="">
                                                                <?php foreach ($children['children'] as $subChildren) { ?>
                                                                    <li class="">
                                                                        <a href="<?php echo MyUtility::makeUrl('Blog', 'category', array($subChildren['bpcategory_id'])); ?>"><?php echo $subChildren['bpcategory_name']; ?></a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </nav>
                </div>
            </div>
        </div>
        <!--blog filters end here-->
    </div>
</div>
<script>
    (function () {
        var uri = window.location.pathname;
        var parentCat = null;
        $('ul.nav--vertical-js li').each(function () {
            if ($(this).find('ul').length) {
                parentCat = $(this);
                $(this).find('ul li').each(function () {
                    if ($(this).find('a').attr('href') == uri) {
                        $(this).addClass('is-active');
                        $(parentCat).addClass('is-active');
                    }
                });
            } else {
                if ($(this).find('a').attr('href') == uri) {
                    $(this).addClass('is-active');
                }
            }
        });
    })();
</script>