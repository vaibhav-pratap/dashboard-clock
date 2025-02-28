<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auto-update the plugin from GitHub
 */
function dashboard_clock_check_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = plugin_basename(__DIR__ . '/../dashboard-clock.php');
    $github_url = 'https://api.github.com/repos/vaibhav-pratap/dashboard-clock/releases/latest';

    $response = wp_remote_get($github_url, [
        'headers' => ['User-Agent' => 'WordPress/' . get_bloginfo('version')],
    ]);

    if (is_wp_error($response)) {
        error_log('Dashboard Clock: Failed to fetch updates - ' . $response->get_error_message());
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($release['tag_name']) && version_compare($transient->checked[$plugin_slug], $release['tag_name'], '<')) {
        $transient->response[$plugin_slug] = (object) [
            'slug' => 'dashboard-clock',
            'new_version' => $release['tag_name'],
            'url' => $release['html_url'],
            'package' => $release['assets'][0]['browser_download_url'] ?? '',
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'dashboard_clock_check_update');
