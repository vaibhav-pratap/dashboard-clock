<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Clock Plugin Update Checker
 */
class DashboardClockUpdater {
    private $github_repo;
    private $plugin_slug;

    public function __construct($github_repo, $plugin_slug) {
        $this->github_repo = $github_repo;
        $this->plugin_slug = plugin_basename($plugin_slug);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    /**
     * Check for updates
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = wp_remote_get("https://api.github.com/repos/{$this->github_repo}/releases/latest");

        if (is_wp_error($response)) {
            return $transient;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        if (!isset($release->tag_name)) {
            return $transient;
        }

        $new_version = str_replace('v', '', $release->tag_name);
        $current_version = DASHBOARD_CLOCK_VERSION;

        if (version_compare($new_version, $current_version, '>')) {
            $transient->response[$this->plugin_slug] = (object) [
                'slug'        => $this->plugin_slug,
                'new_version' => $new_version,
                'url'         => "https://github.com/{$this->github_repo}/releases",
                'package'     => $release->assets[0]->browser_download_url ?? $release->zipball_url,
            ];
        }

        return $transient;
    }

    /**
     * Plugin details
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $response = wp_remote_get("https://api.github.com/repos/{$this->github_repo}/releases/latest");
        if (is_wp_error($response)) {
            return $result;
        }

        $release = json_decode(wp_remote_retrieve_body($response));
        if (!isset($release->tag_name)) {
            return $result;
        }

        return (object) [
            'name'          => 'Dashboard Clock',
            'slug'          => $this->plugin_slug,
            'version'       => str_replace('v', '', $release->tag_name),
            'author'        => '<a href="https://exiverlabs.com">Vaibhav Singh</a>',
            'homepage'      => "https://github.com/{$this->github_repo}",
            'sections'      => [
                'description' => 'A simple plugin to display a clock on the WordPress dashboard with time zone & country selection.',
            ],
            'download_link' => $release->zipball_url,
        ];
    }
}

// Initialize the update checker
new DashboardClockUpdater(DASHBOARD_CLOCK_GITHUB_REPO, __FILE__);
