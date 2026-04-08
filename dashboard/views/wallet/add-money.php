<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->addFormTagAttribute('onsubmit', 'setupAddMoney(this); return false;');
$methods = $form->getField('pmethod_id');
$amount = $form->getField('amount');
$amount->addFieldTagAttribute('id', 'amount');
$submitField = $form->getField('submit');
$submitField->addFieldTagAttribute('class', 'btn btn--primary btn--large btn--block color-white');
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_ADD_MONEY_TO_WALLET'); ?></h4>
    </div>
    <div class="facebox-panel__body padding-bottom-5">
        <?php echo $form->getFormTag(); ?>
        <div class="padding-3">
            <label class="field_label" ><?php echo $amount->getCaption(); ?>
                <?php if ($amount->requirement->isRequired()) { ?>
                    <span class="spn_must_field">*</span>
                <?php } ?>
            </label>
            <?php echo $amount->getHTML(); ?>
        </div>
        <div class="padding-3">
            <label class="field_label">
                <?php echo Label::getLabel('LBL_PAYMENT_METHOD'); ?>
                <?php if ($amount->requirement->isRequired()) { ?>
                    <span class="spn_must_field">*</span>
                <?php } ?></label>
            <?php echo $methods->getHTML(); ?>
            <p class="payment-note color-secondary">
                <?php
                $labelstr = Label::getLabel('LBL_*_ALL_PURCHASES_ARE_IN_{currencycode}._FOREIGN_TRANSACTION_FEES_MIGHT_APPLY,_ACCORDING_TO_YOUR_BANK_POLICIES');
                echo str_replace("{currencycode}", $currency['currency_code'], $labelstr);
                ?>
            </p>
            <span class="-gap"></span>
            <?php echo $submitField->getHTML(); ?>
        </div>
        </form>
        <?php echo $form->getExternalJS(); ?>
    </div>
</div>