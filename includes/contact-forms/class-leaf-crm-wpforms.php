<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

class Leaf_Crm_Wpforms
{
    private function prepare_form_data($fields)
    {
        $form_data = [];
        foreach ($fields as $field) {
            $form_data[] = [
                'label' => sanitize_text_field($field['name']),
                'value' => sanitize_text_field($field['value']),
                'type' => sanitize_text_field($field['type']),
            ];
        }

        return $form_data;
    }

    private function get_posted_data($fields, $form_data)
    {
        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        $website_name = Leaf_Crm_Options::get_single_value('leaf_website');
        if (!empty($fields) && !empty($token)) {
            return array(
                'title' => $form_data['settings']['form_title'],
                'form_data' => $this->prepare_form_data($fields),
                'leaf_crm_token' => $token,
                'leaf_website' => $website_name,
                'wp_cf_type' => "wpforms"
            );
        }
        return null;
    }
    
    /** 
     * This is a wpforms callback after the lead is submitted.
     * Calls a non blocking http API request to Leaf with lead data.
     */
    public function submit_wpforms_to_leaf($fields, $__entry, $form_data, $__entry_id)
    {
        $webhook_url = Leaf_Crm_Constants::get_webhook_url('wpforms');
        $payload = $this->get_posted_data($fields, $form_data);

        $Leaf_Crm_api = new Leaf_Crm_API('wpforms');
        $Leaf_Crm_api->submit_lead_to_leaf($webhook_url, $payload); 
    }
}
