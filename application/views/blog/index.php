<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="banner banner--main <?php echo (isset($bpCategoryId)) ? '' : 'banner--main'; ?>">
    <div class="banner__media -hide-mobile"><img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_BLOG_PAGE_IMAGE, 0, Afile::SIZE_LARGE]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo Label::getLabel('LBL_BLOG'); ?>"></div>
    <div class="banner__content banner__content--centered">
        <h1><?php echo Label::getLabel('LBL_Blog'); ?></h1>
        <p><?php echo Label::getLabel('LBL_The_place_where_we_write_some_words'); ?></p>
        <div class="form-search form-search--blog">
            <a href="javascript:void(0)" class="blog-toggle blog-toggle-js"><span></span></a>
            <form method="post" onsubmit="searchBlogs(this);return false;">
                <div class="form__element">
                    <input class="form__input" placeholder="<?php echo Label::getLabel('LBL_Blog_Search'); ?>" name="keyword" type="text" />
                    <span class="form__action-wrap">
                        <input class="form__action" value="" type="submit" />
                        <span class="svg-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14.844" height="14.843" viewBox="0 0 14.844 14.843">
                                <path d="M251.286,196.714a4.008,4.008,0,1,1,2.826-1.174A3.849,3.849,0,0,1,251.286,196.714Zm8.241,2.625-3.063-3.062a6.116,6.116,0,0,0,1.107-3.563,6.184,6.184,0,0,0-.5-2.442,6.152,6.152,0,0,0-3.348-3.348,6.271,6.271,0,0,0-4.884,0,6.152,6.152,0,0,0-3.348,3.348,6.259,6.259,0,0,0,0,4.884,6.152,6.152,0,0,0,3.348,3.348,6.274,6.274,0,0,0,6-.611l3.063,3.053a1.058,1.058,0,0,0,.8.34,1.143,1.143,0,0,0,.813-1.947h0Z" transform="translate(-245 -186.438)"></path>
                            </svg>
                        </span>
                    </span>
                </div>
            </form>
        </div>
    </div>
</section>
<section class="section section--nav">
    <div class="container container--fixed">
        <span class="overlay overlay--blog blog-toggle-js"></span>
        <nav class="nav-categories">
            <?php $this->includeTemplate('_partial/blogTopFeaturedCategories.php'); ?>
        </nav>
    </div>
</section>
<section class="section section--blogs">
    <div id='listing'></div>
</section>
<script>
    var bpCategoryId = <?php echo !empty($bpCategoryId) ? $bpCategoryId : 0; ?>;
</script>
</div>
<script>
    /* FOR BLOG CATEGORIES */
    $('.blog-toggle-js').click(function() {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-categories-js");
    });
</script>