<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

class Leaf_Crm_Ninja_Form
{
    private function prepare_form_data($fields)
    {
        $form_data = [];
        foreach ($fields as $key => $field) {
            if ($field['type'] != 'submit' || $field['value'] != '') {
                $form_data[] = [
                    'label' => sanitize_text_field($field['label']),
                    'value' => sanitize_text_field($field['value']),
                    'type' => sanitize_text_field($field['type']),
                ];
            }
        }

        return $form_data;
    }

    private function get_posted_data($form_data)
    {
        $form_title = $form_data['settings']['title'];
        $form_fields = $form_data['fields'];

        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        $website_name = Leaf_Crm_Options::get_single_value('leaf_website');
        if (!empty($form_fields) && !empty($token)) {
            return array(
                'title' => $form_title,
                'form_data' => $this->prepare_form_data($form_fields),
                'leaf_crm_token' => $token,
                'leaf_website' => $website_name,
                'wp_cf_type' => "ninja_form"
            );
        }
        return null;
    }

    /** 
     * This is ninja form callback after the lead is submitted.
     * Calls a non blocking http API request to Leaf with lead data.
     */
    public function submit_ninja_form_to_leaf($form_data)
    {
        $webhook_url = Leaf_Crm_Constants::get_webhook_url('ninja_form');
        $payload = $this->get_posted_data($form_data);

        $Leaf_Crm_api = new Leaf_Crm_API('ninja_form');
        $Leaf_Crm_api->submit_lead_to_leaf($webhook_url, $payload); 
    }
}
