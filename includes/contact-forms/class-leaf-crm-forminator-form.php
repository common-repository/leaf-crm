<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

class Leaf_Crm_Forminator_Form
{
    private function prepare_form_data($form_id)
    {
        $entry_response = Forminator_Form_Entry_Model::get_latest_entry_by_form_id($form_id);
        $entry_data = $entry_response->{'meta_data'};
        $form_wrappers = Forminator_API::get_form_wrappers($form_id);

        $form_data = array_map(function($wrapper) use ($entry_data) {
            $getField = $wrapper["fields"][0];
            $type = (!empty($getField)) ? $getField["type"] : 'text';
            $label = (!empty($getField)) ? $getField["field_label"] : 'Label';
            $key = (!empty($getField)) ? $getField["element_id"] : '';
            $value = isset($entry_data[$key]) ? $entry_data[$key]['value'] : '';

            return [
                'label' => sanitize_text_field($label),
                'value' => sanitize_text_field($value),
                'type' => sanitize_text_field($type),
            ];
        }, $form_wrappers);

        return $form_data;
    }

    private function get_posted_data($form_id)
    {
        $form = Forminator_API::get_form($form_id);
        $form_settings = $form->{'settings'};
        $form_name = $form_settings["formName"];

        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        $website_name = Leaf_Crm_Options::get_single_value('leaf_website');
        $form_data = $this->prepare_form_data($form_id);

        if (!empty($form_data) && !empty($token)) {
            return array(
                'title' => $form_name,
                'form_data' => $form_data,
                'leaf_crm_token' => $token,
                'leaf_website' => $website_name,
                'wp_cf_type' => "forminator_form"
            );
        }
        return null;
    }

    /** 
     * This is the callback after the lead is submitted.
     * Calls a non blocking http API request to Leaf with lead data.
     */
    public function submit_forminator_to_leaf($form_id, $response)
    {
        if (!$response) return;
        if (!is_array($response)) return;
        if (!$response['success']) return;

        $webhook_url = Leaf_Crm_Constants::get_webhook_url('forminator_form');
        $payload = $this->get_posted_data($form_id);

        $Leaf_Crm_api = new Leaf_Crm_API('forminator_form');
        $Leaf_Crm_api->submit_lead_to_leaf($webhook_url, $payload); 
    }
}
