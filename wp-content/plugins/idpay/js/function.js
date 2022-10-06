var ajax_timeout = 10000;
//======================================================================================================================
// save IDPay settings
function change_IDPay_settings() {
    (function ($) {
        let idpay_api_key = $('#idpay_api_key').val();
        let idpay_limit_payment_min = $('#idpay_limit_payment_min').val();
        let idpay_limit_payment_max = $('#idpay_limit_payment_max').val();
        let idpay_paymeny_unique_link = $('#idpay_paymeny_unique_link').val();
        if (idpay_api_key != '' && idpay_limit_payment_min != '' && idpay_limit_payment_max != '' && idpay_paymeny_unique_link != '') {
            var DataString = {
                'action': 'change_IDPay_settings',
                'idpay_api_key': idpay_api_key,
                'idpay_limit_payment_min': idpay_limit_payment_min,
                'idpay_limit_payment_max': idpay_limit_payment_max,
                'idpay_paymeny_unique_link': idpay_paymeny_unique_link
            };
            $.ajax({
                url: '../wp-content/plugins/idpay/ajax_action.php',
                type: 'POST',
                timeout: ajax_timeout,
                data: DataString,
                beforeSend: function () {
                    $('#btn_change_IDPay_settings').prop('disabled', true);
                },
                error: function (request, status, error) {
                    console.log(error);
                    $('#btn_change_IDPay_settings').prop('disabled', false);
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    if (obj.flag == true) {
                        $('#match_StartDate').val('');
                        $('#match_EndDate').val('');
                        $('#idpay_main_modal_body').html(obj.msg);
                        $('#idpay_main_modal').modal('show');
                    } else {
                        $('#idpay_main_modal_body').html(obj.msg);
                        $('#idpay_main_modal').modal('show');
                    }
                    $('#btn_change_IDPay_settings').prop('disabled', false);
                }
            }).done(function () {

            });
        } else {
            $('#idpay_main_modal_body').html('خطا - تمامی قسمت ها باید تکمیل گردد');
            $('#idpay_main_modal').modal('show');
        }
    }(jQuery));
}
