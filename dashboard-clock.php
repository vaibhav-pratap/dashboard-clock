<?php
/**
 * Plugin Name: Dashboard Clock
 * Plugin URI: https://github.com/vaibhav-pratap/dashboard-clock
 * Description: A simple plugin to display a clock on the WordPress dashboard with auto-update from GitHub.
 * Version: 1.1.3
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

// Define plugin constants
define('DASHBOARD_CLOCK_VERSION', '1.1.3');
define('DASHBOARD_CLOCK_DIR', plugin_dir_path(__FILE__));
define('DASHBOARD_CLOCK_URL', plugin_dir_url(__FILE__));
define('DASHBOARD_CLOCK_GITHUB_REPO', 'vaibhav-pratap/dashboard-clock'); // GitHub Repository

// Load update checker
require_once DASHBOARD_CLOCK_DIR . 'includes/update-checker.php';

// Load plugin settings
require_once DASHBOARD_CLOCK_DIR . 'includes/settings-page.php';

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

    // Set default options if not already set
    if (!get_option('dashboard_clock_timezones')) {
        update_option('dashboard_clock_timezones', ['UTC']);
    }
}
register_activation_hook(__FILE__, 'dashboard_clock_activate');

/**
 * Plugin Deactivation Hook
 */
function dashboard_clock_deactivate() {
    delete_option('dashboard_clock_timezones');
}
register_deactivation_hook(__FILE__, 'dashboard_clock_deactivate');

// Add clock to admin dashboard
function dashboard_clock_add_dashboard_widget() {
    wp_add_dashboard_widget('dashboard_clock_widget', 'Multi-Timezone Clock', 'dashboard_clock_display');
}
add_action('wp_dashboard_setup', 'dashboard_clock_add_dashboard_widget');

// Render the clock widget
function dashboard_clock_display() {
    $selected_timezones = get_option('dashboard_clock_timezones', []);

    // Remove empty selections
    $selected_timezones = array_filter($selected_timezones);

    if (empty($selected_timezones)) {
        echo "<p>No time zones selected. Please go to <a href='" . esc_url(admin_url('options-general.php?page=dashboard-clock-settings')) . "'>settings</a> to configure.</p>";
        return;
    }
    ?>

    <style>
        .dashboard-clock { font-size: 18px; margin: 10px 0; }
    </style>

    <div id="dashboard-clock-container">
        <?php foreach ($selected_timezones as $timezone) : ?>
            <div class="dashboard-clock">
                <strong><?php echo esc_html($timezone); ?></strong>: <span class="clock-time" data-timezone="<?php echo esc_attr($timezone); ?>"></span>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function updateClocks() {
            document.querySelectorAll('.clock-time').forEach(el => {
                const timezone = el.getAttribute('data-timezone');
                const time = new Date().toLocaleString("en-US", { timeZone: timezone, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                el.innerHTML = time;
            });
        }
        setInterval(updateClocks, 1000);
        updateClocks();
    </script>

    <?php
}
