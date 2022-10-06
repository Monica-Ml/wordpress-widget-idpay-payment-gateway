<?php
/**
 * Plugin Name:   idpay widget payment gateway
 * Plugin URI: http://example.ir/cms/wp/plugin/idpay-widget
 * Description: show idpay payment gateway
 * Version: 1.0
 * Author: hamid arani
 * Author URI: http://example.ir/
 * License: GPLv2 or later
 *
 *
 *
 *
 *
 * Plugin Name:       GitHub Updater
 * Plugin URI:        https://github.com/crabcraf
 * Description:       A plugin to automatically update GitHub, Bitbucket or GitLab hosted plugins and themes. It also allows for remote installation of plugins or themes into WordPress.
 * Version:           1.0.0
 * Author:            hamid arani
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       github-updater
 * GitHub Plugin URI: https://github.com/crabcraf
 * GitHub Branch:     master
 */

class idpay_PaymentGateway_widget extends WP_Widget {
    public function __construct() {
        $widget_options = array( 'classname' => '', 'description' => 'idpay - payment gateway' );
        parent::__construct( 'idpay_PaymentGateway_widget', 'idpay payment gateway', $widget_options );
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'] . $args['before_title'] . $args['after_title'];
        include get_template_directory().'/includes/idpay-PaymentGateway-widget.php';
        echo $args['after_widget'];
    }
    public function form( $instance ) {

    }
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
        return $instance;
    }
}
function setup_idpay_PaymentGateway_widget() {
    register_widget( 'idpay_PaymentGateway_widget' );
}
add_action( 'widgets_init', 'setup_idpay_PaymentGateway_widget');
