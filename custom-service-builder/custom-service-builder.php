<?php
/*
Plugin Name: Custom Service Builder for Traveler
Description: A modular plugin to add custom services to Traveler with partner dashboards, booking calendar, SMS notifications, and more.
Version: 2.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

// بارگذاری فایل‌های مورد نیاز
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/service-registration.php';
require_once plugin_dir_path(__FILE__) . 'includes/booking-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/partner-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/additional-features.php';

// فعال‌سازی پلاگین
register_activation_hook(__FILE__, 'csb_activate');
function csb_activate() {
    csb_setup_partner_role(); // نقش Partner رو موقع فعال‌سازی تنظیم می‌کنه
}

// بارگذاری استایل‌ها و اسکریپت‌ها
add_action('wp_enqueue_scripts', 'csb_enqueue_assets');
function csb_enqueue_assets() {
    wp_enqueue_style('fullcalendar', plugins_url('assets/fullcalendar/main.css', __FILE__), [], '6.1.10');
    wp_enqueue_script('fullcalendar', plugins_url('assets/fullcalendar/main.js', __FILE__), [], '6.1.10', true);
    wp_enqueue_script('csb-script', plugins_url('assets/js/csb-script.js', __FILE__), ['jquery'], '2.0', true);
    wp_localize_script('csb-script', 'csb_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
}