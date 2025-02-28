<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings menu for the plugin
 */
function dashboard_clock_add_settings_menu() {
    add_options_page(
        __('Dashboard Clock Settings', 'dashboard-clock'),
        __('Dashboard Clock', 'dashboard-clock'),
        'manage_options',
        'dashboard-clock-settings',
        'dashboard_clock_settings_page'
    );
}
add_action('admin_menu', 'dashboard_clock_add_settings_menu');

/**
 * Register settings
 */
function dashboard_clock_register_settings() {
    register_setting('dashboard_clock_settings', 'dashboard_clock_timezone');
    register_setting('dashboard_clock_settings', 'dashboard_clock_country');
}
add_action('admin_init', 'dashboard_clock_register_settings');

/**
 * Render settings page
 */
function dashboard_clock_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Dashboard Clock Settings', 'dashboard-clock'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dashboard_clock_settings');
            do_settings_sections('dashboard_clock_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dashboard_clock_timezone"><?php _e('Select Time Zone:', 'dashboard-clock'); ?></label></th>
                    <td>
                        <select name="dashboard_clock_timezone" id="dashboard_clock_timezone">
                            <?php
                            $timezones = timezone_identifiers_list();
                            $selected_timezone = get_option('dashboard_clock_timezone', 'UTC');
                            foreach ($timezones as $tz) {
                                echo '<option value="' . esc_attr($tz) . '" ' . selected($tz, $selected_timezone, false) . '>' . esc_html($tz) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dashboard_clock_country"><?php _e('Enter Country:', 'dashboard-clock'); ?></label></th>
                    <td>
                        <input type="text" name="dashboard_clock_country" id="dashboard_clock_country" value="<?php echo esc_attr(get_option('dashboard_clock_country', 'Worldwide')); ?>">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
