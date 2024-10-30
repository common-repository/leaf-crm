<?php

/**
 * This class contain constants and source of all information for Leaf CRM plugin
 * @since      1.0.0
 */
class Leaf_Crm_Constants
{
    public const API_BASE_URL = 'https://app.leaf.my/api/integration/wordpress';
    public const WEB_BASE_URL = 'https://www.leaf.my';
    public const LEAF_SUBSCRIBE_URL = self::API_BASE_URL . '/subscribe';
    public const LEAF_UNSUBSCRIBE_URL = self::API_BASE_URL . '/unsubscribe';
    public const WP_OPTION_NAME = 'leaf_crm_options';
    public const WP_OPTION_VERSION_NAME = 'leaf_crm_version';
    public const WP_SAVE_HOOK_NAME = 'leaf_crm_save_options';
    public const LEAF_TOKEN_NAME = 'leaf_crm_token';
    public const LEAF_WEBSITE_NAME = 'leaf_website';

    public const SUPPORTED_INTEGRATIONS = array(
        array(
            'key' => 'cf7',
            'name' => 'Contact Form 7',
            'icon' => 'contact-forms/cf7.svg',
            'identifiers' => array('contact-form-7/wp-contact-form-7.php'),
            'webhookUrl' => self::API_BASE_URL . '/cf7/savelead'
        ),
        array(
            'key' => 'wpforms',
            'name' => 'WPForms',
            'icon' => 'contact-forms/wpforms.svg',
            'identifiers' => array('wpforms/wpforms.php', 'wpforms-lite/wpforms.php'),
            'webhookUrl' => self::API_BASE_URL . '/wpforms/savelead'
        ),
        array(
            'key' => 'forminator_form',
            'name' => 'Forminator',
            'icon' => 'contact-forms/forminator.png',
            'identifiers' => array('forminator/forminator.php'),
            'webhookUrl' => self::API_BASE_URL . '/forminator_form/savelead'
        ),
        array(
            'key' => 'ninja_form',
            'name' => 'Ninja Forms',
            'icon' => 'contact-forms/ninjaforms.png',
            'identifiers' => array('ninja-forms/ninja-forms.php'),
            'webhookUrl' => self::API_BASE_URL . '/ninja_form/savelead'
        ),
    );

    public static function get_webhook_url($integration_key)
    {
        $supported_keys = array_column(self::SUPPORTED_INTEGRATIONS, 'key');
        $found_index = array_search($integration_key, $supported_keys);
        return self::SUPPORTED_INTEGRATIONS[$found_index]['webhookUrl'];
    }

    public static function get_icon_url($file)
    {
        return plugins_url('admin/img/' . $file, dirname(__FILE__));
    }
}
