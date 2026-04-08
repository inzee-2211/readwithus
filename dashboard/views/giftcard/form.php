<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->addFormTagAttribute('onsubmit', 'setup(this); return false;');
$pmethodField = $form->getField('order_pmethod_id');
$amount = $form->getField('order_total_amount');
$amount->addFieldTagAttribute('id', 'giftcard_price');
$amount->addFieldTagAttribute('onfocusout', 'checkWalletBalance($(this).val(),' . $walletBalance . ');');
$receiverName = $form->getField('ordgift_receiver_name');
$receiverEmail = $form->getField('ordgift_receiver_email');
$submitField = $form->getField('submit');
$submitField->addFieldTagAttribute('class', 'btn btn--primary btn--large btn--block color-white');
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_PURCHASE_GIFTCARD'); ?></h4>
    </div>
    <div class="facebox-panel__body padding-bottom-0">
        <div class="selection selection--checkout selection--payment">
            <?php echo $form->getFormTag(); ?>
            <div class="row justify-content-between">
                <div class="col-md-6 col-xl-6">
                    <div class="field-set">
                        <label class="field_label margin-bottom-2">
                            <?php echo $amount->getCaption(); ?>
                            <?php if ($amount->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php  } ?>
                        </label>
                        <?php echo $amount->getHTML(); ?>
                    </div>
                   
                    <div class="field-set">
                        <label class="field_label margin-bottom-2">
                            <?php echo $receiverName->getCaption(); ?>
                            <?php if ($receiverName->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php  } ?>
                        </label>
                        <?php echo $receiverName->getHTML(); ?>
                    </div>
                    
                    <div class="field-set">
                        <label class="field_label margin-bottom-2">
                            <?php echo $receiverEmail->getCaption(); ?>
                            <?php if ($receiverEmail->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php  } ?>
                        </label>
                        <?php echo $receiverEmail->getHTML(); ?>
                    </div>
                    
                </div>
                <div class="col-md-5 col-xl-5">
                    <div class="selection-title">
                        <label class="field_label margin-bottom-2"><?php echo Label::getLabel('LBL_PAYMENT_METHOD'); ?> <span class="spn_must_field">*</span></label>
                    </div>
                    <div class="payment-wrapper margin-bottom-4">
                        <?php foreach ($pmethodField->options as $id => $name) { ?>
                            <?php if ($walletBalance > 0 && $id == $walletPayId) { ?>
                                <label class="selection-tabs__label add-and-pay-js" style="display: none;">
                                    <input type="checkbox" name="add_and_pay" class="selection-tabs__input" value="1" onclick="cart.selectWallet(this.checked)" />
                                    <div class="selection-tabs__title">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <g>
                                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                            </g>
                                        </svg>
                                        <div class="payment-type">
                                            <p><?php echo str_replace(['{remaining}'], [MyUtility::formatMoney($walletBalance)], Label::getLabel('LBL_PAY_{remaining}_FROM_WALLET_BALANCE')); ?></p>
                                        </div>
                                    </div>
                                </label>
                                <label class="selection-tabs__label wallet-pay-js">
                                    <input type="radio" class="selection-tabs__input" value="<?php echo $id; ?>" <?php echo ($pmethodField->value == $id) ? 'checked' : ''; ?> name="order_pmethod_id" />
                                    <div class="selection-tabs__title">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <g>
                                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                            </g>
                                        </svg>
                                        <div class="payment-type">
                                            <p><?php echo str_replace(['{balance}'], [MyUtility::formatMoney($walletBalance)], Label::getLabel('LBL_WALLET_BALANCE_({balance})')); ?></p>
                                        </div>
                                    </div>
                                </label>
                            <?php } else { ?>
                                <label class="selection-tabs__label payment-method-js">
                                    <input type="radio" class="selection-tabs__input" value="<?php echo $id; ?>" <?php echo ($pmethodField->value == $id) ? 'checked' : ''; ?> name="order_pmethod_id" />
                                    <div class="selection-tabs__title">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <g>
                                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                            </g>
                                        </svg>
                                        <div class="payment-type">
                                            <p><?php echo $name; ?></p>
                                        </div>
                                    </div>
                                </label>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php echo $submitField->getHTML(); ?>
                    <p class="payment-note color-secondary">
                        <?php
                        $labelstr = Label::getLabel('LBL_*_ALL_PURCHASES_ARE_IN_{currencycode}._FOREIGN_TRANSACTION_FEES_MIGHT_APPLY,_ACCORDING_TO_YOUR_BANK_POLICIES');
                        echo str_replace("{currencycode}", $currency['currency_code'], $labelstr);
                        ?>
                    </p>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<?php echo $form->getExternalJS(); ?>