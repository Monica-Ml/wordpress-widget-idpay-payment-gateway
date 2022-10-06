<?php
function get_IDPay_settings()
{
    global $wpdb;
    $sql = "`record_id`>0";
    $result = $wpdb->get_row("SELECT * FROM `idpay_settings` WHERE " . $sql);
    if ($result) {
        $limit_payment_min = $result->IDPay_limit_payment_min;
        $limit_payment_max = $result->IDPay_limit_payment_max;
        $api_key = $result->IDPay_api_key;
        $unique_link = $result->IDPay_unique_link;
        return array(true, $limit_payment_min, $limit_payment_max, $api_key, $unique_link);
    } else {
        return array(false, 0, 0, '', '');
    }
}

function change_IDPay_settings($idpay_api_key, $idpay_limit_payment_min, $idpay_limit_payment_max, $idpay_paymeny_unique_link)
{
    global $wpdb;
    $where = array('record_id' => 1);
    $data_array = array(
        'IDPay_api_key' => $idpay_api_key,
        'IDPay_limit_payment_min' => $idpay_limit_payment_min,
        'IDPay_limit_payment_max' => $idpay_limit_payment_max,
        'IDPay_unique_link' => $idpay_paymeny_unique_link,
    );
    $result = $wpdb->update('idpay_settings', $data_array, $where);
    if ($result == '1') {
        return true;
    } else {
        return false;
    }
}

//*************************************************** widgets **********************************************************
function check_idpay_PaymentGateway_widget_status()
{
    /**
     * description
     * return widget status on or off
     */
    $widget_IdpayPaymentGateway_status = '0';
    // get widget_data
    $widget_data_array = get_widget();
    if ($widget_data_array) {
        foreach ($widget_data_array as $key => $widget_data) {
            $widget_name = $widget_data->widget_name;
            $status = $widget_data->status;
            // idpay PaymentGateway
            if ($widget_name == 'idpay-PaymentGateway') {
                $widget_IdpayPaymentGateway_status = $status;

            }
        }
    }
    return array($widget_IdpayPaymentGateway_status);
}

function get_widget($widget_name = null)
{
    /**
     * description
     * get idpay widgets information
     */
    global $wpdb;
    if ($widget_name != '') {
        $sql = " WHERE `widget_name`='" . $widget_name . "'";
    } else {
        $sql = '';
    }
    // query
    $result = $wpdb->get_results("SELECT * FROM `manage_widgets`" . $sql);
    // result
    if (is_array($result)) {
        return $result;
    } else {
        return false;
    }
}

//************************************************** idpay class *******************************************************
class idpay_temp_transaction
{
    private $IDPay_NewPayment_API_URL = 'https://api.idpay.ir/v1/payment';
    private $IDPay_InquiryPayment_API_URL = 'https://api.idpay.ir/v1/payment/inquiry';
    private $api_key = '';
    private $callback = 'idpay-callback';
    private $curl_ContentType = 'application/json';
    private $curl_SandboxStatus = 'false';
    private $curl_timeout = 60;

    public function __construct () {
        list($flg_IDPay_settings,$limit_payment_min,$limit_payment_max,$api_key,$unique_link)=get_IDPay_settings();
        $this->api_key = $api_key;
    }

    public function set_temp_data($user_id, $amount, $user_mobile, $payment_des, $ip_address, $registered_date_time, $status = 0, $result_id_key = '', $result_link_key = '', $result_status_code = 0)
    {
        global $wpdb;
        $data_array = array(
            'user_id' => $user_id,
            'amount' => $amount,
            'mobile' => $user_mobile,
            'payment_des' => $payment_des,
            'result_id_key' => $result_id_key,
            'result_link_key' => $result_link_key,
            'ip_address' => $ip_address,
            'result_status_code' => $result_status_code,
            'date_time' => $registered_date_time,
            'status' => $status
        );
        $result = $wpdb->insert('idpay_temp_transaction', $data_array);
        if ($result) {
            $order_id = $wpdb->insert_id;
            return array(true, '', $order_id);
        } else {
            return array(false, 'خطا در ثبت تراکنش جدید', '');
        }
    }

    public function update_temp_data($order_id, $user_id, $result_id_key, $result_link_key, $result_status_code = 1, $status = 2)
    {
        global $wpdb;
        $where = array('order_id' => $order_id, 'user_id' => $user_id);
        $data_array = array(
            'result_id_key' => $result_id_key,
            'result_link_key' => $result_link_key,
            'result_status_code' => $result_status_code,
            'status' => $status
        );
        $result = $wpdb->update('idpay_temp_transaction', $data_array, $where);
        if ($result == '1') {
            return array(true, '');
        } else {
            return array(false, 'خطا در بروز رسانی اطلاعات ثبت تراکنش جدید');
        }
    }

    public function update_temp_data_status($order_id, $user_id, $status)
    {
        global $wpdb;
        $where = array('order_id' => $order_id, 'user_id' => $user_id);
        $data_array = array(
            'status' => $status
        );
        $result = $wpdb->update('idpay_temp_transaction', $data_array, $where);
        if ($result == '1') {
            return array(true, '');
        } else {
            return array(false, 'خطا در بروز رسانی وضعیت ثبت تراکنش جدید');
        }
    }

    public function request_new_payment($order_id, $amount, $phone, $desc)
    {
        $callback_url = get_home_url('/') . '/' . $this->callback;
        $params = array(
            'order_id' => $order_id,
            'amount' => $amount,
            'phone' => $phone,
            'desc' => $desc,
            'callback' => $callback_url
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->IDPay_NewPayment_API_URL);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");             // gzip
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);         // request 'timeout second'
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: '.$this->curl_ContentType,
            'X-API-KEY: '.$this->api_key,
            'X-SANDBOX: '.$this->curl_SandboxStatus,
        ));
        // error reporting
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen(curl_debug_file, 'a');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        // execute request
        $output = curl_exec($ch);
        // debug result
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //check http code return
        // close connections
        curl_close($ch);

        if ($http_code == '201') {
            // get response from api
            $result = json_decode($output);
            $result_id_key = $result->id;
            $result_link_key = $result->link;
            return array(true, 'ثبت تراکنش جدید با موفقیت انجام شد', $result_id_key, $result_link_key);
        } else {
            return array(false, ' خطا ' . $this->request_new_payment_errors($http_code), '', '');
        }
    }

    public function request_new_payment_errors($error_code)
    {
        switch ($error_code) {
            case 403:
                $error_des = 'درخواست شما از {ip} ارسال شده است. این IP با IP های ثبت شده در وب سرویس همخوانی ندارد';
                break;
            case 405:
                $error_des = 'تراکنش ایجاد نشد';
                break;
            case 406:
                $error_des = 'اطلاعات وارد شده معتبر نمی باشد';
                break;
            default:
                $error_des = 'کد نامشخص - 301';
        }
        return $error_des;
    }

    public function response_new_payment($response_code)
    {
        $flg_error = false;
        switch ($response_code) {
            case 1:
                $error_des = 'پرداخت انجام نشده است';
                break;
            case 2:
                $error_des = 'پرداخت ناموفق بوده است';
                break;
            case 3:
                $error_des = 'مشکلی رخ داده است';
                break;
            case 100:
                $error_des = 'پرداخت تایید شده است';
                $flg_error = true;
                break;
            case 101:
                $error_des = 'پرداخت قبلا تایید شده است';
                break;
            default:
                $error_des = 'کد نامشخص - 302';
        }
        return array($flg_error, $error_des);
    }

    public function verify_payment($order_id, $user_id,$result_id_key,$amount,$status=2){
        global $wpdb;
        $sql = "`order_id`='".$order_id."' and `user_id`='".$user_id."' and `result_id_key`='".$result_id_key."' and `amount`='".$amount."' and `status`='".$status."'";
        $result = $wpdb->get_row("SELECT * FROM `idpay_temp_transaction` WHERE " . $sql);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function request_inquiry($result_id_key,$order_id){
        $params = array(
            'id' => $result_id_key,
            'order_id' => $order_id,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->IDPay_InquiryPayment_API_URL);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");             // gzip
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);         // request 'timeout second'
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: '.$this->curl_ContentType,
            'X-API-KEY: '.$this->api_key,
            'X-SANDBOX: '.$this->curl_SandboxStatus,
        ));
        // error reporting
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen(curl_debug_file, 'a');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        // execute request
        $output = curl_exec($ch);
        // debug result
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //check http code return
        // close connections
        curl_close($ch);

        if ($http_code == '200') {
            // get response from api
            $result = json_decode($output);
            $result_status = $result->status;
            $result_track_id = $result->track_id;
            $result_id = $result->id;
            $result_order_id = $result->order_id;
            $result_amount = $result->amount;
            $result_card_no = $result->card_no;
            $result_date = $result->date;
            return array(true, 'استعلام تراکنش با موفقیت انجام شد', $result_status, $result_track_id,$result_id,$result_order_id,$result_amount,$result_card_no,$result_date);
        } else {
            return array(false, ' خطا ' . $this->request_inquiry_errors($http_code), '', '', '', '', '', '', '');
        }
    }

    public function request_inquiry_errors($error_code){
        switch ($error_code) {
            case 400:
                $error_des = 'استعلام نتیجه ای نداشت';
                break;
            case 403:
                $error_des = 'حساب بانکی متصل به وب سرویس تایید نشده است';
                break;
            case 406:
                $error_des = 'اطلاعات وارد شده معتبر نمی باشد';
                break;
            default:
                $error_des = 'کد نامشخص - 303';
        }
        return $error_des;
    }

    public function response_inquiry($response_code)
    {
        $flg_error = false;
        switch ($response_code) {
            case 1:
                $error_des = 'پرداخت انجام نشده است';
                break;
            case 2:
                $error_des = 'پرداخت ناموفق بوده است';
                break;
            case 3:
                $error_des = 'مشکلی رخ داده است';
                break;
            case 100:
                $error_des = 'پرداخت تایید شده است';
                $flg_error = true;
                break;
            case 101:
                $error_des = 'پرداخت قبلا تایید شده است';
                break;
            default:
                $error_des = 'کد نامشخص - 304';
        }
        return array($flg_error, $error_des);
    }
}

class idpay_transactions{
    public function set_data(
        $order_id,
        $user_id,
        $transaction_track_id,
        $transaction_id_key,
        $transaction_amount,
        $transaction_card_num,
        $transaction_date,
        $transaction_des,
        $transaction_user_cell,
        $transaction_inquiry_status_code,
        $transaction_status
    ){
        /**
         *          $transaction_status ->      successful      1
         */
        global $wpdb;
        $data_array = array(
            'order_id' => $order_id,
            'user_id' => $user_id,
            'transaction_track_id' => $transaction_track_id,
            'transaction_id_key' => $transaction_id_key,
            'transaction_amount' => $transaction_amount,
            'transaction_card_num' => $transaction_card_num,
            'transaction_date' => $transaction_date,
            'transaction_des' => $transaction_des,
            'transaction_user_cell' =>$transaction_user_cell ,
            'transaction_inquiry_status_code' => $transaction_inquiry_status_code,
            'transaction_status' =>$transaction_status
        );
        $result = $wpdb->insert('idpay_transactions', $data_array);
        if ($result) {
            $record_id = $wpdb->insert_id;
            return array(true, '', $record_id);
        } else {
            return array(false, 'خطا در نهایی شدن تراکنش جدید', '');
        }
    }
    public function get_data($transaction_track_id,$transaction_result_id_key){
        global $wpdb;
        $sql = "`transaction_track_id`='".$transaction_track_id."' or `transaction_id_key`='".$transaction_result_id_key."'";
        $result = $wpdb->get_row("SELECT * FROM `transactions` WHERE " . $sql);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function update_transaction_status($transaction_track_id, $transaction_result_id_key,$transaction_status){
        /**
         *          $transaction_status ->      default            0
         *                                      successful         1
         *                                      exist record       2
         */
        global $wpdb;
        $where = array('transaction_track_id' => $transaction_track_id, 'transaction_id_key' => $transaction_result_id_key);
        $data_array = array(
            'transaction_status' => $transaction_status
        );
        $result = $wpdb->update('idpay_transactions', $data_array, $where);
        if ($result == '1') {
            return array(true, '');
        } else {
            return array(false, 'خطا در بروز رسانی اطلاعات ثبت تراکنش جدید');
        }
    }

    public function update_transaction_inquiry_status_code($transaction_track_id, $transaction_result_id_key,$transaction_inquiry_status_code){
        /**
         *  $transaction_inquiry_status_code ->  default        0
         *                                       successful     1
         *                                       failed         2
         */
        global $wpdb;
        $where = array('transaction_track_id' => $transaction_track_id, 'transaction_id_key' => $transaction_result_id_key);
        $data_array = array(
            'transaction_inquiry_status_code' => $transaction_inquiry_status_code
        );
        $result = $wpdb->update('idpay_transactions', $data_array, $where);
        if ($result == '1') {
            return array(true, '');
        } else {
            return array(false, 'خطا در بروز رسانی اطلاعات ثبت تراکنش');
        }
    }
}
