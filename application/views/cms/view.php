<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php if ($cPage['cpage_layout'] == Contentpage::CONTENT_PAGE_LAYOUT1_TYPE) { ?>
    <section class="section padding-bottom-0">
        <div class="container container--fixed">
            <div class="intro-head">
                <h6 class="small-title"><?php echo $cPage['cpage_title']; ?></h6>
                <?php if ($cPage['cpage_image_title']) { ?>
                    <h2><?php echo $cPage['cpage_image_title']; ?></h2>
                <?php } ?>
                <?php if ($cPage['cpage_image_content']) { ?>
                    <p><?php echo $cPage['cpage_image_content']; ?></p>
                <?php } ?>
            </div>
            <div class="about-media">
                <div class="media">
                    <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('image', 'show', [Afile::TYPE_CPAGE_BACKGROUND_IMAGE, $cPage['cpage_id'], Afile::SIZE_LARGE]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $cPage['cpage_image_title'] ?? $cPage['cpage_title']; ?>">
                </div>
            </div>
        </div>
    </section>
    <?php
    if ($blockData) {
        if (isset($blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_1]) && $blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_1]['cpblocklang_text']) {
            echo FatUtility::decodeHtmlEntities($blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_1]['cpblocklang_text']);
        }
        if (isset($blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_2]) && $blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_2]['cpblocklang_text']) {
            echo FatUtility::decodeHtmlEntities($blockData[Contentpage::CONTENT_PAGE_LAYOUT1_BLOCK_2]['cpblocklang_text']);
        }
    }
} else {
    ?>
    <style type="text/css">
        .cms-view .section--hiw:before,
        .cms-view .section--hiw .col__content:before {
            display: none;
        }

        .cms-view .col__content {
            padding: 2rem;
            background-color: #eee;
        }

        .cms-view .col__content .media {
            margin: 0;
        }


        .cms-view  .tabs-vertical li h3,.tabs-vertical li p{opacity: 0.3;}
        .cms-view  .tabs-vertical li.is-active h3,.tabs-vertical li.is-active p{opacity: 1;}
    </style>
    <section class="section">
        <div class="container container--narrow">
            <div class="main__title">
                <h2 class="align-center"><?php echo $cPage['cpage_title']; ?></h2>
            </div>
            <div class="who-we__content">
                <?php echo FatUtility::decodeHtmlEntities($cPage['cpage_content']) ?></p>
            </div>
        </div>
    </section>
<?php } ?>
<script>
    /* for faq toggles */
    $(".accordian__body-js").hide();
    $(".accordian__body-js:first").show();
    $(".accordian__title-js").click(function() {
        if ($(this).parents('.accordian-js').hasClass('is-active')) {
            $(this).siblings('.accordian__body-js').slideUp();
            $('.accordian-js').removeClass('is-active');
        } else {
            $('.accordian-js').removeClass('is-active');
            $(this).parents('.accordian-js').addClass('is-active');
            $('.accordian__body-js').slideUp();
            $(this).siblings('.accordian__body-js').slideDown();
        }
    });
    $('.slider-onehalf-js').slick({
        centerPadding: '0px',
        slidesToShow: 2,
        slidesToScroll: 1,
        prevArrow: $('.prev-slide'),
        nextArrow: $('.next-slide'),
        dots: true,
        responsive: [{
                breakpoint: 768,
                settings: {
                    centerPadding: '0px',
                    slidesToShow: 2,
                    arrows: false
                }
            },
            {
                breakpoint: 480,
                settings: {
                    centerPadding: '0px',
                    slidesToShow: 1,
                    arrows: false
                }
            }
        ]
    });
    /* [ FOR PRODUCTS */
    $('.step-slider-js').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
        asNavFor: '.slider-tabs--js'
    });
    $('.slider-tabs--js').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.step-slider-js',
        dots: true,
        centerMode: true,
        focusOnSelect: true
    });
    /* FOR NAV TOGGLES */
    $('.btn--filters-js').click(function() {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-filters-js");
    });
</script>
</div>