<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

/**
 * Leaf CRM API Helper function
 * @since 1.0.0
 */
class Leaf_Crm_API
{
    private $plugin_key;
    private $form_name;

    /**
     * Class constructor function
     * @param string $plugin_key plugin identifier from Leaf_Crm_Constants
     * @param string $form_name form identifier
     */
    function __construct($plugin_key = null, $form_name = null)
    {
        $this->plugin_key = $plugin_key;
        $this->form_name = $form_name;
    }

    /**
     * To check if the integration is enabled or not
     * by retrieve the integration state from wp_options
     */
    private function is_integration_enabled()
    {
        $is_enabled = Leaf_Crm_Options::get_single_value($this->plugin_key);
        return 'true' == $is_enabled;
    }

    /** 
     * Make an API submission to the Leaf CRM for the lead
     */
    public function submit_lead_to_leaf($endpoint, $payload)
    {
        $is_enabled = $this->is_integration_enabled();
        if (!$endpoint || !$payload || !$is_enabled) return;
        $payload["form_name"] = $this->form_name; 
        $options = [
            'body' => wp_json_encode($payload),
            'headers' => ['Content-Type' => 'application/json'],
            'blocking' => false,
            'data_format' => 'body',
        ];
        wp_remote_post($endpoint, $options);
    }

    /** 
     * Tell Leaf CRM to connect/reconnect the integration
     */
    public function subscribe_leaf_crm()
    {
        $endpoint = Leaf_Crm_Constants::LEAF_SUBSCRIBE_URL;
        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        if ($token != null) {
            $payload = [
                'leaf_crm_token' => $token,
                'site_url' => get_bloginfo('url')
            ];
            $options = [
                'body' => wp_json_encode($payload),
                'headers' => ['Content-Type' => 'application/json'],
                'blocking' => false,
                'data_format' => 'body',
            ];
            wp_remote_post($endpoint, $options);
        }
    }

    /** 
     * Tell Leaf CRM to disconnect the integration
     */
    public function unsubscribe_leaf_crm()
    {
        $endpoint = Leaf_Crm_Constants::LEAF_UNSUBSCRIBE_URL;
        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        if ($token != null) {
            $payload = [
                'leaf_crm_token' => $token,
                'site_url' => get_bloginfo('url')
            ];
            $options = [
                'body' => wp_json_encode($payload),
                'headers' => ['Content-Type' => 'application/json'],
                'blocking' => false,
                'data_format' => 'body',
            ];
            wp_remote_post($endpoint, $options);
        }
    }
}
