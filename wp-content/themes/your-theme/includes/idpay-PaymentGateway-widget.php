<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 1/29/2019
 * Time: 22:10
 */
// check widget status
list($widget_IdpayPaymentGateway_status) = check_idpay_PaymentGateway_widget_status();
if ($widget_IdpayPaymentGateway_status != '1') {
    ?>
    <div class="row">
        <p>درگاه پرداخت فعال نمیباشد</p>
    </div>
    <?php
} else {
    list($flg_IDPay_settings, $limit_payment_min, $limit_payment_max, $api_key, $unique_link) = get_IDPay_settings();
    ?>
    <div class="row">
        <form name="frm_charge_user_account" id="frm_charge_user_account"
              action="<?= get_home_url('/') . '/idpay-payment'; ?>" method="post" autocomplete="off"
              onsubmit="return check_idpay_payment_form()">
            <label for=""> حداقل مبلغ شارژ <?= $limit_payment_min; ?> هزار ریال </label>
            <label for=""> حداکثر مبلغ شارژ <?= $limit_payment_max; ?> هزار ریال </label>
            <input type="text" name="charge_amount" id="charge_amount" tabindex="1" class="form-control"
                   placeholder="لطفا مبلغ به ریال وارد شود" value="" minlength="<?= strlen($limit_payment_min); ?>" maxlength="<?= strlen($limit_payment_max); ?>">
            <input type="submit" class="btn btn-success" name="btn_charge_user_account" value="پرداخت">
        </form>
        <p id="idpay_payment_form_error_msg"></p>
    </div>

    <script>
        function check_idpay_payment_form() {
            $('#idpay_payment_form_error_msg').text('');
            let limit_payment_min = '<?= $limit_payment_min; ?>';
            let limit_payment_max = '<?= $limit_payment_max; ?>';
            let charge_amount = $('#charge_amount').val();
            if (charge_amount != '') {
                if (!isNaN(charge_amount)) {
                    if (charge_amount >= limit_payment_min && charge_amount <= limit_payment_max) {
                        return true;
                    } else {
                        $('#idpay_payment_form_error_msg').text('مبلغ وارد شده خارج از بازه تعیین شده می باشد');
                        return false;
                    }
                } else {
                    $('#idpay_payment_form_error_msg').text('مقدار وارد شده نامعتبر میباشد');
                    return false;
                }
            } else {
                $('#idpay_payment_form_error_msg').text('مبلغ مورد نظر خود برای شارژ حساب کاربری وارد نمایید');
                return false;
            }
        }
    </script>
    <?php
}
?>
<style>
    #idpay_payment_form_error_msg {
        color: red;
        font-size: 12px;
        text-align: right;
    }
</style>