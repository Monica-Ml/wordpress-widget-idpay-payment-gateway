<?php
/**
 * Plugin Name: idpay
 * Plugin URI: -
 * Description: -
 * Version: 1.0
 * Author: -
 * Author URI: -
 * License: GPLv2 or later
 */

// include library
include('modules/autoload.php');

add_action('after_setup_theme', 'IDPay_remove_admin_bar');

function IDPay_remove_admin_bar()
{
    show_admin_bar(false);
}

// create custom plugin settings menu
add_action('admin_menu', 'IDPay_create_new_menu');

function IDPay_create_new_menu()
{
    // create new 'main menu'
    add_menu_page('IDPay', 'مدیریت IDPay', 'administrator', __FILE__, 'IDPay_create_management_page', plugins_url(plugin_basename(dirname(__FILE__)).'/images/WordPress/idpay_icon.png'));
}

//call register my settings function
add_action('admin_init', 'IDPay_register_my_settings');

function IDPay_register_my_settings()
{
    //register our settings
    register_setting('baw-settings-group', 'new_option_name');
    register_setting('baw-settings-group', 'some_other_option');
    register_setting('baw-settings-group', 'option_etc');
}

function IDPay_register_my_css()
{
    wp_register_style('idpay-bootstrap-reset', plugins_url('css/bootstrap-reset.css', __FILE__));
    wp_enqueue_style('idpay-bootstrap-reset');

    wp_register_style('idpay-bootstrap.min', plugins_url('css/bootstrap.min.css', __FILE__));
    wp_enqueue_style('idpay-bootstrap.min');

    wp_register_style('idpay-fonts', plugins_url('css/fonts.css', __FILE__));
    wp_enqueue_style('idpay-fonts');

    wp_register_style('idpay-style', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('idpay-style');

    wp_register_style('idpay-style-responsive', plugins_url('css/style-responsive.css', __FILE__));
    wp_enqueue_style('idpay-style-responsive');
}

add_action('admin_init', 'IDPay_register_my_css');

function IDPay_register_my_js()
{
    wp_register_script('idpay-bootstrap.min', plugins_url('js/bootstrap.min.js', __FILE__));
    wp_enqueue_script('idpay-bootstrap.min');

    wp_register_script('idpay-function', plugins_url('js/function.js', __FILE__));
    wp_enqueue_script('idpay-function');
}

add_action('admin_init', 'IDPay_register_my_js');

// management IDPay settings
function IDPay_create_management_page()
{
    $plugin_url = admin_url('admin.php?page=idpay%2Fidpay.php&section=');
    ?>
    <div class="wrap">
        <!-- Tabs name -->
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 sidebar_menu">
            <h4>منو اصلی</h4>
            <div class="frame_sidebar_menu">
                <ul class="nav nav-pills nav-stacked" id="main_menu">
                    <li><a href="<?= $plugin_url; ?>idpay_settings">تنضیمات</a></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <?php
            if (isset($_GET['section'])) {
                $section_name = $_GET['section'];
            } else {
                $section_name = 'idpay_settings';
            }
            ?>
            <?php
            switch ($section_name) {
                case "idpay_settings":
                    list($flg_IDPay_settings,$limit_payment_min,$limit_payment_max,$api_key,$unique_link)=get_IDPay_settings();
                    ?>
                    <!-- Tab : idpay settings -->
                    <div class="idpay_settings_frame_content">
                        <section class="panel">
                            <header class="panel-heading">تنضیمات - IDPay</header>
                            <div class="panel-body">
                                <!-- api key -->
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label">کد درگاه شما</label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="idpay_api_key" id="idpay_api_key" value="<?= $api_key; ?>" placeholder="کد درگاه را وارد کنید">
                                                <span class="input-group-addon">Api Key</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- limit payment min -->
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label">حداقل مبلغ قابل پرداخت</label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="idpay_limit_payment_min" id="idpay_limit_payment_min" value="<?= $limit_payment_min; ?>" placeholder="حداقل مبلغ پرداختی را وارد کنید">
                                                <span class="input-group-addon">تومان</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- limit payment max -->
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label">حداکثر مبلغ قابل پرداخت</label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="idpay_limit_payment_max" id="idpay_limit_payment_max" value="<?= $limit_payment_max; ?>" placeholder="حداکثر مبلغ قابل پرداخت را وارد کنید">
                                                <span class="input-group-addon">تومان</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- payment unique link -->
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label">لینک درگاه پرداخت</label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="idpay_paymeny_unique_link" id="idpay_paymeny_unique_link" value="<?= $unique_link; ?>" placeholder="لینک درگاه پرداخت را وارد کنید">
                                                <span class="input-group-addon">URL</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- limit payment button -->
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="button" class="btn btn-success" name="btn_change_IDPay_settings" id="btn_change_IDPay_settings" value="ثبت تغییرات" onclick="change_IDPay_settings()">
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </section>
                    </div>
                    <?php
                    break;
                default:
                    ?>
                    <div class="idpay_settings_frame_content">
                        <!-- not found -->
                        <section class="panel state-panel">
                            <header class="panel-heading">
                                <strong>خطا</strong>
                            </header>
                            <div class="panel-body">
                                <!-- row -->
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <p>صفحه مورد نظر یافت نشد</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div>
    </div>

    <!-- IDPay Main modal -->
    <div class="modal fade" id="idpay_main_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">پیام</h4>
                </div>
                <div class="modal-body" id="idpay_main_modal_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">خروج</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

//=================================================== tables ===========================================================
// setup tables
IDPay_setup_tables();
function IDPay_setup_tables()
{
    tbl_IDPay_settings();
    tbl_manage_widgets();
}
function tbl_IDPay_settings()
{
    global $wpdb;
    $table_name='idpay_settings';
    $sql="CREATE TABLE IF NOT EXISTS $table_name (
        record_id int(255) NOT NULL AUTO_INCREMENT,
		IDPay_limit_payment_min varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		IDPay_limit_payment_max varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		IDPay_api_key varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		IDPay_unique_link longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		PRIMARY KEY (record_id)
	)  DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // initialize IDPay data
    $result=$wpdb->get_results("SELECT * FROM `$table_name` WHERE `record_id`>'0'");
    if(empty($result)){
        $flg=$wpdb->insert($table_name,
            array('IDPay_limit_payment_min'=>IDPay_limit_payment_min,
                'IDPay_limit_payment_max'=>IDPay_limit_payment_max,
                'IDPay_api_key'=>IDPay_API_KEY,
                'IDPay_unique_link'=>IDPay_unique_link
            )
        );
    }
}
function tbl_manage_widgets()
{
    global $wpdb;
    $table_name='manage_widgets';
    $sql="CREATE TABLE IF NOT EXISTS $table_name (
		widget_id int(255) NOT NULL AUTO_INCREMENT,
		widget_name varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		status int(10) NOT NULL,
		PRIMARY KEY (widget_id)
	)  DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // idpay-PaymentGateway
    $result_tour=$wpdb->get_results("SELECT * FROM `$table_name` WHERE `widget_name`='idpay-PaymentGateway'");
    if(empty($result_tour)){
        $flg_tour=$wpdb->insert($table_name,array('widget_name'=>'idpay-PaymentGateway','status'=>'1'));
    }
}