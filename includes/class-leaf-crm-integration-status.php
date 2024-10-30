<?php

/**
 * Check Wordpress Integration Status 
 */
class Leaf_Crm_Integration_Status
{
    public const NOT_EXIST = 0;
    public const INSTALLED = 1;
    public const ACTIVATED = 2;
    public const CONNECTED = 3;

    /**
     * Defined constants for JS side
     */
    public static function to_array()
    {
        return array(
            "NotExist" => self::NOT_EXIST,
            "Installed" => self::INSTALLED,
            "Activated" => self::ACTIVATED,
            "Connected" => self::CONNECTED,
        );
    }

    /**
     * Function to check if plugin active
     * @param array $plugin_slugs file path to the plugin, usually end with `.php`
     */
    private static function is_plugin_active($plugin_slugs)
    {
        foreach($plugin_slugs as $slug) {
            if(is_plugin_active($slug)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Function to check if plugin is installed
     * @param array $plugin_slugs file path to the plugin, usually end with `.php`
     */
    private static function is_plugin_installed($plugin_slugs)
    {
        $installed_plugins = get_plugins();
        foreach($plugin_slugs as $slug) {
            if(array_key_exists($slug, $installed_plugins)
                || in_array($slug, $installed_plugins, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for plugin status based on their slug path,
     * optionally check if it's connected if token is provided too
     * @param array $plugin_slugs array of file path to the plugin, usually end with `.php`
     * @param boolean $enabled flag wether the plugin state is enabled/not
     */
    private static function get_plugin_status($plugin_slugs, $enabled = false)
    {
        $is_active = self::is_plugin_active($plugin_slugs);
        $is_installed = self::is_plugin_installed($plugin_slugs);
        if (!$is_installed) return self::NOT_EXIST;
        if (!$is_active) return self::INSTALLED;
        if (!$enabled) return self::ACTIVATED;
        return self::CONNECTED;
    }

    /**
     * Get integration status
     * @param object $integration integration instance
     */
    public static function get_status($integration)
    {
        $enabled = $integration['enabled'];
        $identifiers = $integration['identifiers'];
        return self::get_plugin_status($identifiers, $enabled);
    }
}
