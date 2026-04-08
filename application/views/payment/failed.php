<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--grey section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="message-display">
                    <div class="message-display__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                            <path d="M150,87.869V34.9L115.088,0H0V200H150v-0.366A56.228,56.228,0,0,0,150,87.869ZM112.488,15.082L134.912,37.5H112.488V15.082ZM12.5,187.5V12.488H100V49.994h37.5V87.869A56,56,0,0,0,108.423,100H25v12.5H96.982A55.964,55.964,0,0,0,90.768,125H25v12.5H87.869a55.839,55.839,0,0,0,20.566,50H12.5Zm131.25-.732a43.024,43.024,0,1,1,43.018-43.018A43.111,43.111,0,0,1,143.75,186.768ZM25,75H125V87.5H25V75Z"></path>
                            <path fill="#fd4444" d="M156.25,118.75l-12.5,12.5-12.5-12.5-12.5,12.5,12.5,12.5-12.5,12.5,12.5,12.5,12.5-12.5,12.5,12.5,12.5-12.5-12.5-12.5,12.5-12.5Z"></path>
                        </svg>
                    </div>
                    <span class="-gap"></span>
                    <h1 class="-color-secondary"><?php echo Label::getLabel('LBL_PAYMENT_FAILED'); ?></h1>
                    <?php $msgs = array_merge($messageData['msgs'], $messageData['errs'], $messageData['info'], $messageData['dialog']); ?>
                    <?php if (count($msgs) > 0) { ?>
                        <span class="-gap"></span>
                        <p><?php echo current($msgs); ?></p>
                    <?php } ?>
                    <span class="-gap"></span>
                    <div class="-align-center">
                        <h6>
                            <?php echo Label::getLabel('MSG_YOUR_ORDER_PAYMENT_HAS_FAILED'); ?>
                            <a class="-color-secondary underline" href="<?php echo MyUtility::makeUrl('Contact'); ?>">
                                <?php echo Label::getLabel('LBL_CONTACT_HERE'); ?></a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>