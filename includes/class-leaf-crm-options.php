<?php

/**
 * Leaf Plugin Options
 *
 * This class manage all of the options made by Leaf CRM plugin,
 * all the option values are stored in `wp_options` table
 * https://codex.wordpress.org/Database_Description#Table:_wp_options
 *
 * @since      1.0.0
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 * @author     Leaf CRM Team <developer@leaf.my>
 */
class Leaf_Crm_Options
{
    /**
     * Save fields into on `wp_options`,
     * @param array $fields payload to be saved into `wp_options`
     */
    public static function set_values($fields)
    {
        $allowed_keys = array_column(Leaf_Crm_Constants::SUPPORTED_INTEGRATIONS, 'key');
        array_push($allowed_keys, Leaf_Crm_Constants::LEAF_WEBSITE_NAME, Leaf_Crm_Constants::LEAF_TOKEN_NAME);
        $filtered_values = array_intersect_key($fields, array_flip($allowed_keys));
        update_option(Leaf_Crm_Constants::WP_OPTION_NAME, $filtered_values);
        return wp_send_json_success();
    }

    /**
     * Get all value from `leaf_crm_options` on `wp_options`
     * @return array
     */
    public static function get_values()
    {
        $options = get_option(Leaf_Crm_Constants::WP_OPTION_NAME);
        return $options ? $options : array();
    }

    /**
     * Get single value from `leaf_crm_options` on `wp_options`
     * @param string $key option field identifier, e.g elementor_form
     */
    public static function get_single_value($key)
    {
        $options = self::get_values();
        return isset($options[$key]) ? $options[$key] : null;
    }

    public static function get_integrations()
    {
        function check_status($left, $right)
        {
            return $right['status'] - $left['status'];
        }

        function add_detail($integration)
        {
            $key = $integration['key'];
            $integration['enabled'] = Leaf_Crm_Options::get_single_value($key);
            $integration['status'] = Leaf_Crm_Integration_Status::get_status($integration);
            $integration['icon'] = esc_url(Leaf_Crm_Constants::get_icon_url($integration['icon']));

            // Recheck to make sure the integration is disabled when status is not activated
            $is_connected = $integration['status'] > Leaf_Crm_Integration_Status::ACTIVATED;
            $integration['enabled'] = $integration['enabled'] && $is_connected;
            return $integration;
        }

        $result = array_map('add_detail', Leaf_Crm_Constants::SUPPORTED_INTEGRATIONS);
        return $result;
    }

    /**
     * Save values from WP Admin
     */
    public function save_options_handler()
    {
        // Sanitize $_POST inputs
        $submission = array_map('sanitize_text_field', $_POST);
        // Validate the inputs
        $validation_errors = self::validate_submission($submission);

        // if validation fails, throw error
        if (!empty($validation_errors)) {
            // if error, do not save to database, return error message
            return wp_send_json_error($validation_errors, 422);
        }

        $existing_token = self::get_single_value(Leaf_Crm_Constants::LEAF_TOKEN_NAME);
        if ($existing_token) return self::set_values($submission);

        // Check if there are existing Leaf tokens. If no, automatically enable all integrations
        $integrations = self::get_integrations();
        $installed = array_filter($integrations, function ($integration) {
            return $integration['status'] >= Leaf_Crm_Integration_Status::INSTALLED;
        });

        foreach ($installed as $integration) {
            $key = $integration['key'];
            $submission[$key] = true;
        }

        return self::set_values($submission);
    }

    public static function validate_submission($submission)
    {
        // Define the validation rules for each input
        $validation_rules = [
            'leaf_crm_token' => ['required', 'string', 'regex:/^(\bleaf-)([a-zA-Z\d]{8})$/'],
            'leaf_website' => ['string', 'max:120'],
            'site_url' => ['required', 'url'],
            'cf7' => ['boolean'],
            'wpforms' => ['boolean'],
            'forminator_form' => ['boolean'],
            'ninja_form' => ['boolean'],
            // Add validation rules for other inputs if needed
        ];

        $field_names = [
            'leaf_crm_token' => 'Leaf CRM Token',
            'leaf_website' => 'Website Name',
            'site_url' => 'Website URL',
            'cf7' => 'Contact Form 7',
            'wpforms' => 'WPForms',
            'forminator_form' => 'Forminator',
            'ninja_form' => 'Ninja Forms',
        ];

        // Perform input validation
        $validation_errors = [];
        foreach ($validation_rules as $field => $rules) {
            if (!isset($submission[$field])) {
                continue; // Skip validation if the field is not submitted
            }

            foreach ($rules as $rule) {
                if ($rule === 'required' && empty($submission[$field])) {
                    $validation_errors[$field] = "$field_names[$field] is required.";
                } elseif ($rule === 'string' && !is_string($submission[$field])) {
                    $validation_errors[$field] = "$field_names[$field] must be a string.";
                } elseif ($rule === 'url' && !filter_var($submission[$field], FILTER_VALIDATE_URL)) {
                    $validation_errors[$field] = "$field_names[$field] must be a valid URL.";
                } elseif ($rule === 'boolean' && ($submission[$field] !== 'true' && $submission[$field] !== '1')) {
                    $validation_errors[$field] = "$field_names[$field] must be true.";
                } elseif (strpos($rule, 'regex:') === 0) {
                    $pattern = substr($rule, strlen('regex:'));
                    if (!preg_match($pattern, $submission[$field])) {
                        $validation_errors[$field] = "$field_names[$field] format is invalid.";
                    }
                } elseif (strpos($rule, 'max:') === 0) {
                    $max_length = (int) substr($rule, strlen('max:'));
                    if (!empty($submission[$field]) && strlen($submission[$field]) > $max_length) {
                        $validation_errors[$field] = "$field_names[$field] must not exceed $max_length characters.";
                    }
                }
            }
        }

        return $validation_errors;
    }
}
