<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

class Leaf_Crm_CF7
{
    private function prepare_form_data($fields)
    {
        $form_data = [];
        foreach ($fields as $key => $value) {
            $value = is_array($value) ? implode(", ", $value) : $value;
            $form_data[] = [
                'label' => sanitize_text_field($key),
                'value' => sanitize_text_field($value),
                'type' => 'text',
            ];
        }

        return $form_data;
    }

    /**
     * Format and prepare the raw data from contact form 7 
     */
    private function get_posted_data($cf7)
    {
        $token = Leaf_Crm_Options::get_single_value('leaf_crm_token');
        $website_name = Leaf_Crm_Options::get_single_value('leaf_website');
        if (!isset($cf7->posted_data) && class_exists('WPCF7_Submission') && !empty($token)) {
            $submission = WPCF7_Submission::get_instance();
            if ($submission) {
                return array(
                    'title' => $cf7->title(),
                    'form_data' => $this->prepare_form_data($submission->get_posted_data()),
                    'leaf_crm_token' => $token,
                    'leaf_website' => $website_name,
                    'wp_cf_type' => "contact_form7"
                );
            }
            return (array) $cf7;
        }
        return null;
    }

    /** 
     * This is a wpcf7 callback after the lead is submitted.
     * Calls a non blocking http API request to Leaf with lead data.
     */
    public function submit_cf7_to_leaf($contact_form)
    {
        $webhook_url = Leaf_Crm_Constants::get_webhook_url('cf7');
        $payload = $this->get_posted_data($contact_form);
        
        $Leaf_Crm_api = new Leaf_Crm_API('cf7');
        $Leaf_Crm_api->submit_lead_to_leaf($webhook_url, $payload); 
    }
}
