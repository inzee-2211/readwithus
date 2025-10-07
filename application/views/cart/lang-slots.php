<?php defined('SYSTEM_INIT') or die('INVALID USAGE.'); ?>
<?php $steps = Cart::getSteps(); ?>
<script>
    cart.selectLanguage(parseInt('<?php echo $tlangId; ?>'));
    cart.selectDuration(parseInt('<?php echo $duration; ?>'));
</script>
<div class="box box--checkout">
    <div class="box__head">
        <h4><?php echo Label::getLabel('LBL_SELECT_LANGUAGE_AND_DURATION'); ?></h4>
        <div class="step-nav">
            <ul>
                <?php foreach ($steps as $key => $step) { ?>
                    <li class="step-nav_item <?php echo in_array($key, $stepProcessing) ? 'is-process' : ''; ?> <?php echo in_array($key, $stepCompleted) ? 'is-completed' : ''; ?> ">
                        <a href="javascript:void(0);"><?php echo $step; ?></a>
                        <?php if (in_array($key, $stepCompleted)) { ?><span class="step-icon"></span><?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="checkout-title">
            <p>
                <?php echo Label::getLabel('LBL_SELECT_LANGUAGE_AND_TIMESLOT'); ?>. <br/>
                <?php echo Label::getLabel('LBL_CLICK_ON_LANGUAGE_TIMESLOTS'); ?>. <br/>
            </p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <!-- [ LANGUAGE SELECTION ========= -->
                <div class="select-dropdown">
                    <div class="select-dropdown__trigger select-tlang-trigger-js is-selected" onclick="cart.toggleLanguage();">
                        <span class="select-dropdown__trigger-media margin-right-5">
                            <svg class="icon icon--language" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <rect width="24" height="24" fill="none"/>
                                <g transform="translate(2 3)">
                                    <g>
                                        <g>
                                            <path d="M197.221,146.969c-.54,0-1.081-.01-1.621,0a.233.233,0,0,1-.266-.181c-.288-.795-.593-1.583-.885-2.376a.222.222,0,0,0-.246-.181q-1.983.01-3.967,0a.223.223,0,0,0-.249.177c-.293.8-.6,1.595-.891,2.395a.21.21,0,0,1-.237.167c-.488-.009-.977,0-1.465,0-.05,0-.1-.006-.174-.011.137-.368.266-.72.4-1.072q1.809-4.822,3.614-9.644a.244.244,0,0,1,.276-.2c.475.012.951.011,1.426,0a.236.236,0,0,1,.265.185q1.949,5.216,3.908,10.429c.031.082.076.16.114.239v.078Zm-6.476-4.572h2.945l-1.45-3.87H192.2Z" transform="translate(-177.222 -128.771)" />
                                            <path d="M14.528,1.811V3.634h-.237c-.736,0-1.472,0-2.209,0a.236.236,0,0,0-.275.193,15.356,15.356,0,0,1-2.129,4.26c-.32.45-.68.873-1.023,1.307-.056.07-.115.138-.156.189.347.36.677.71,1.016,1.052.385.389.779.769,1.164,1.158a.222.222,0,0,1,.069.175c-.21.585-.43,1.167-.657,1.774L7.256,10.9l-4.53,4.53L1.444,14.152,6.02,9.63c-.5-.674-1.01-1.324-1.479-2A12.82,12.82,0,0,1,3.348,5.451c.457,0,.894.033,1.323-.01a.659.659,0,0,1,.74.44A12.764,12.764,0,0,0,7.193,8.417a.607.607,0,0,1,.052.094,14.38,14.38,0,0,0,2.9-4.89H0v-1.8H6.355V0H8.172V1.811h6.357Z" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="select-dropdown__trigger-label"><?php echo Label::getLabel('LBL_SELECT_LANGUAGE'); ?></span>
                        <span class="select-dropdown__trigger-value selected-tlang-target-js"><?php echo Label::getLabel('LBL_SELECT_LANGUAGE'); ?></span>
                    </div>
                    <div class="select-dropdown__target select-tlang-target-js" style="display: none;">
                        <div class="select-option">
                            <div class="select-option__scroll">
                                <?php foreach ($langslots as $key => $langslot) { ?>
                                    <label class="select-option__label">
                                        <input type="radio" class="select-option__input" name="ordles_tlang_id" onchange="cart.selectLanguage(this.value);"  value="<?php echo $key; ?>" <?php echo ($tlangId == $key) ? 'checked' : ''; ?> />
                                        <div class="select-option__title">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z"/></svg>
                                            <span><?php echo $langslot['name']; ?></span>
                                        </div>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] -->
                <!-- [ TIME SLOTS SELECTION ========= -->
                <div class="select-dropdown">
                    <div class="select-dropdown__trigger select-slot-trigger-js is-selected" onclick="cart.toggleDuration()">
                        <span class="select-dropdown__trigger-media margin-right-5">
                            <svg class="icon icon--time" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M11,20a9,9,0,1,1,9-9A9,9,0,0,1,11,20Zm0-1.8A7.2,7.2,0,1,0,3.8,11,7.2,7.2,0,0,0,11,18.2Zm.9-7.2h3.6v1.8H10.1V6.5h1.8Z" transform="translate(1 1)" />
                            </svg>
                        </span>
                        <span class="select-dropdown__trigger-label"><?php echo Label::getLabel('LBL_SELECT_TIMESLOT'); ?></span>
                        <span class="select-dropdown__trigger-value selected-slot-target-js"><?php echo Label::getLabel('LBL_SELECT_TIMESLOT'); ?></span>
                    </div>
                    <div class="select-dropdown__target select-slot-target-js" style="display: none;">
                        <div class="select-option">
                            <?php $counter = 0; ?>
                            <?php foreach ($langslots as $key => $langslot) { ?>
                                <div class="select-option__scroll timeslot-js timeslot-js-<?php echo $key; ?>" style="display: <?php echo ($counter == 0) ? 'block' : 'none'; ?>">
                                    <?php foreach ($langslot['slots'] as $slot) { ?>
                                        <label class="select-option__label">
                                            <input type="radio" class="select-option__input" name="ordles_duration[<?php echo $key; ?>]" onchange="cart.selectDuration(this.value);" value="<?php echo $slot; ?>" <?php echo ($duration == $slot) ? 'checked' : ''; ?> />
                                            <div class="select-option__title">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z"/></svg>
                                                <span><?php echo str_replace('{slot}', $slot, Label::getLabel('LBL_{slot}_MINUTE_LESSON')); ?></span>
                                            </div>
                                        </label>
                                    <?php } ?>
                                    <?php $counter++; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- ] -->
            </div>
        </div> 
    </div>
    <div class="box-foot">
        <div class="teacher-profile">
            <div class="teacher__media">
                <div class="avtar avtar-md">
                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_SMALL]) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] ?>">
                </div>
            </div>
            <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
        </div>
        <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="cart.priceSlabs('<?php echo $teacher['user_id']; ?>', cart.prop.ordles_tlang_id, cart.prop.ordles_duration, cart.prop.ordles_quantity, cart.prop.ordles_type);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
    </div>
</div>