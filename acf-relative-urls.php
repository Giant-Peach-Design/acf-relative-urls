<?php

/**
 * Plugin Name: ACF Relative URLs
 * Description: Stores ACF link/URL fields as relative paths for environment portability
 * Version: 1.0.0
 * Author: Giant Peach
 */

namespace GiantPeach\AcfRelativeUrls;

class AcfRelativeUrls
{
    public function __construct()
    {
        // Convert to relative on save
        add_filter('acf/update_value/type=link', [$this, 'makeRelativeOnSave'], 10, 3);
        add_filter('acf/update_value/type=url', [$this, 'makeRelativeOnSave'], 10, 3);

        // Convert to absolute on load
        add_filter('acf/load_value/type=link', [$this, 'makeAbsoluteOnLoad'], 10, 3);
        add_filter('acf/load_value/type=url', [$this, 'makeAbsoluteOnLoad'], 10, 3);
    }

    /**
     * Convert absolute URLs to relative paths on save
     */
    public function makeRelativeOnSave($value, $postId, $field): mixed
    {
        if (empty($value)) {
            return $value;
        }

        // Handle link fields (array with 'url' key)
        if (is_array($value) && isset($value['url'])) {
            $value['url'] = $this->toRelative($value['url']);
            return $value;
        }

        // Handle URL fields (plain string)
        if (is_string($value)) {
            return $this->toRelative($value);
        }

        return $value;
    }

    /**
     * Convert relative paths to absolute URLs on load
     */
    public function makeAbsoluteOnLoad($value, $postId, $field): mixed
    {
        if (empty($value)) {
            return $value;
        }

        // Handle link fields (array with 'url' key)
        if (is_array($value) && isset($value['url'])) {
            $value['url'] = $this->toAbsolute($value['url']);
            return $value;
        }

        // Handle URL fields (plain string)
        if (is_string($value)) {
            return $this->toAbsolute($value);
        }

        return $value;
    }

    /**
     * Convert absolute URL to relative path if it's an internal link
     */
    private function toRelative(string $url): string
    {
        if (empty($url)) {
            return $url;
        }

        $siteUrl = home_url();

        // Only convert internal URLs
        if (strpos($url, $siteUrl) === 0) {
            $relative = str_replace($siteUrl, '', $url);
            // Ensure it starts with /
            return $relative ?: '/';
        }

        return $url;
    }

    /**
     * Convert relative path to absolute URL
     */
    private function toAbsolute(string $url): string
    {
        if (empty($url)) {
            return $url;
        }

        // If it starts with / but not // (protocol-relative), it's internal
        if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) {
            return home_url($url);
        }

        return $url;
    }
}

// Initialize when plugins are loaded
add_action('plugins_loaded', function () {
    if (function_exists('acf')) {
        new AcfRelativeUrls();
    }
});
