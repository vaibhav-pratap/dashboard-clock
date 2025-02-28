<?php
/**
 * Plugin Name: Dashboard Clock
 * Plugin URI: https://github.com/vaibhav-pratap/dashboard-clock
 * Description: A simple plugin to display a clock on the WordPress dashboard with auto-update from GitHub.
 * Version: 1.0.0
 * Requires PHP: 8.0.30
 * Requires at least: 6.4
 * Author: Vaibhav Singh
 * Author URI: https://exiverlabs.co.in
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dashboard-clock
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Define plugin version
define('DASHBOARD_CLOCK_VERSION', '1.0.0');

/**
 * Plugin Activation Hook
 */
function dashboard_clock_activate() {
    if (version_compare(PHP_VERSION, '8.0.30', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('This plugin requires PHP 8.0.30 or higher.', 'dashboard-clock'));
    }

    if (version_compare(get_bloginfo('version'), '6.4', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('This plugin requires WordPress 6.4 or higher.', 'dashboard-clock'));
    }
    

    // Set default options if needed
    update_option('dashboard_clock_active', true);
}
register_activation_hook(__FILE__, 'dashboard_clock_activate');

/**
 * Plugin Deactivation Hook
 */
function dashboard_clock_deactivate() {
    delete_option('dashboard_clock_active');
}
register_deactivation_hook(__FILE__, 'dashboard_clock_deactivate');

/**
 * Add dashboard widget
 */
function dashboard_clock_add_widget() {
    wp_add_dashboard_widget('dashboard_clock', __('Dashboard Clock', 'dashboard-clock'), 'dashboard_clock_display');
}
add_action('wp_dashboard_setup', 'dashboard_clock_add_widget');

/**
 * Display clock in dashboard
 */
function dashboard_clock_display() {
    echo '<div id="dashboard-clock" style="font-size: 20px; font-weight: bold; color: #0073aa;"></div>';
    wp_enqueue_script('dashboard-clock-js', plugin_dir_url(__FILE__) . 'assets/clock.js', [], DASHBOARD_CLOCK_VERSION, true);
}

// Load Update Checker
require_once plugin_dir_path(__FILE__) . 'includes/update-checker.php';
