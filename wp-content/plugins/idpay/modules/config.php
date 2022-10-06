<?php
/**
 * Date: 02/01/2019
 * Time: 00:20
 */

// error reporting
ini_set('display_errors','off');// todo off
//===================================== IDPay settings ===============================================
define('IDPay_limit_payment_min',10000);
define('IDPay_limit_payment_max',500000000);
define('IDPay_API_KEY',''); // Your IDPay API KEY
define('IDPay_unique_link','IDPay.ir/mydigi');