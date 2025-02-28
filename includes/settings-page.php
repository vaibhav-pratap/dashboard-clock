<?php
if (!defined('ABSPATH')) {
    exit;
}

// Fetch all PHP-supported time zones
function dashboard_clock_get_timezones() {
    $timezones = timezone_identifiers_list();
    $timezone_list = [];

    foreach ($timezones as $timezone) {
        try {
            $dateTimeZone = new DateTimeZone($timezone);
            $location = explode("/", $timezone);
            $place = end($location);
            $country = count($location) > 1 ? str_replace("_", " ", $location[0]) : 'Unknown';
            $timezone_list[$timezone] = "$place, $country";
        } catch (Exception $e) {
            continue;
        }
    }

    return $timezone_list;
}

// Add settings menu
function dashboard_clock_add_settings_page() {
    add_options_page(
        'Dashboard Clock Settings',
        'Dashboard Clock',
        'manage_options',
        'dashboard-clock-settings',
        'dashboard_clock_render_settings_page'
    );
}
add_action('admin_menu', 'dashboard_clock_add_settings_page');

// Render settings page
function dashboard_clock_render_settings_page() {
    $timezones = dashboard_clock_get_timezones();
    $selected_timezones = get_option('dashboard_clock_timezones', []);

    // Ensure it has 3 elements
    while (count($selected_timezones) < 3) {
        $selected_timezones[] = '';  
    }
    ?>

    <div class="wrap">
        <h1>Dashboard Clock Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('dashboard_clock_settings_group'); ?>
            <?php do_settings_sections('dashboard-clock-settings'); ?>

            <table class="form-table">
                <tr>
                    <th>Select Timezones</th>
                    <td>
                        <?php for ($i = 0; $i < 3; $i++) : ?>
                            <select name="dashboard_clock_timezones[]">
                                <option value="">-- Select Timezone --</option>
                                <?php foreach ($timezones as $timezone => $location) : ?>
                                    <option value="<?php echo esc_attr($timezone); ?>" <?php selected($selected_timezones[$i] ?? '', $timezone); ?>>
                                        <?php echo esc_html($location); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br>
                        <?php endfor; ?>
                    </td>
                </tr>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>

    <?php
}

// Register settings
function dashboard_clock_register_settings() {
    register_setting('dashboard_clock_settings_group', 'dashboard_clock_timezones');
}
add_action('admin_init', 'dashboard_clock_register_settings');
