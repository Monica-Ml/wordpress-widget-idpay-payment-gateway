<?php
require_once('../../../wp-load.php');
require_once('modules/autoload.php');

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        // IDPay change settings
        case 'change_IDPay_settings':
            if (
                    isset($_POST['idpay_api_key']) &&
                    isset($_POST['idpay_limit_payment_min']) &&
                    isset($_POST['idpay_limit_payment_max']) &&
                    isset($_POST['idpay_paymeny_unique_link'])
            ) {
                $idpay_api_key = $_POST['idpay_api_key'];
                $idpay_limit_payment_min = $_POST['idpay_limit_payment_min'];
                $idpay_limit_payment_max = $_POST['idpay_limit_payment_max'];
                $idpay_paymeny_unique_link = $_POST['idpay_paymeny_unique_link'];
                $result = change_IDPay_settings($idpay_api_key,$idpay_limit_payment_min,$idpay_limit_payment_max,$idpay_paymeny_unique_link);
                if ($result) {
                    $result = true;
                    $msg = 'بروز رسانی با موفقیت انحام شد';
                } else {
                    $result = false;
                    $msg = 'خطا در بروز رسانی';
                }
            }
            echo json_encode(array('flag' => $result, 'msg' => $msg));
            break;
        // default
        default:
            echo 'خطا - درخواست نامعتبر است';
    }
} else {
    echo 'خطا - درخواست شما نامعتبر است';
}
