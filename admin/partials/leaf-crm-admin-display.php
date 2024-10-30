<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

/**
 * Detect plugin. For use in Admin area only.
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.leaf.my
 * @since      1.0.0
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/admin/partials
 */
function render_leaf_crm_admin_page()
{

    $leaf_options = Leaf_Crm_Options::get_values();
    $locale = strstr(get_locale(), '_', true);

    $wp_settings = array(
        "wordpressSiteUrl" => esc_url(get_bloginfo('url')),
        "wordpressAjaxUrl" => esc_url(admin_url('admin-ajax.php')),
        "leafSubscribeUrl" => esc_url(Leaf_Crm_Constants::LEAF_SUBSCRIBE_URL),
        "formActionName" => esc_html(Leaf_Crm_Constants::WP_SAVE_HOOK_NAME),
        "availableIntegrations" => Leaf_Crm_Options::get_integrations(), // icon urls have been escaped in this function
        "leafToken" => isset($leaf_options[Leaf_Crm_Constants::LEAF_TOKEN_NAME]) ? esc_html($leaf_options[Leaf_Crm_Constants::LEAF_TOKEN_NAME]) : '',
        "leafWebsite" => isset($leaf_options[Leaf_Crm_Constants::LEAF_WEBSITE_NAME]) ? esc_html($leaf_options[Leaf_Crm_Constants::LEAF_WEBSITE_NAME]) : '',
        "Status" => Leaf_Crm_Integration_Status::to_array()
    );

?>

    <div class="container">
        <div class="card mx-auto border-0 shadow-none">
            <form id="leaf-integration-form" method="post" novalidate="novalidate">
                <div class="card-body w-500px">

                    <div class="text-center mb-2">
                        <img src=" <?php echo esc_url(Leaf_Crm_Constants::get_icon_url('wordpress-leaf-integration.png')); ?>" class="wp-leaf-logo" alt="<?php esc_html_e('Leaf WordPress Integration', 'leaf-crm') ?>">
                    </div>

                    <div class="text-dark fs-2 fw-bold mb-4 text-center"><?php esc_html_e('Send Leads to Leaf CRM', 'leaf-crm') ?></div>

                    <!--begin::Input group-->
                    <div class="fv-row mb-4">
                        <!--begin::Label-->
                        <label for="name" class="text-dark fs-7 fw-normal mb-2"><?php esc_html_e('Leaf CRM Token', 'leaf-crm') ?></label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control rounded-1 py-2 px-6 fs-7" name="<?php echo esc_html(Leaf_Crm_Constants::LEAF_TOKEN_NAME); ?>" id="<?php echo esc_html(Leaf_Crm_Constants::LEAF_TOKEN_NAME); ?>" type="text" value="" required>
                        <span class="fs-8 text-muted"><?php esc_html_e('Get the integration token after you have added the WordPress lead source to your Campaign in Leaf CRM', 'leaf-crm') ?></span>

                        <div class="invalid-feedback"><span id="<?php echo esc_html(Leaf_Crm_Constants::LEAF_TOKEN_NAME); ?>_error"></span></div>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->

                    <?php
                    if ($wp_settings['leafToken'] != null) {
                    ?>
                        <!--begin::Input group-->
                        <div class="fv-row mb-4">
                            <!--begin::Label-->
                            <label for="name" class="text-dark fs-7 fw-normal mb-2"><?php esc_html_e('Website Name (Optional)', 'leaf-crm') ?></label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control rounded-1 py-2 px-6 fs-7" name="<?php echo esc_html(Leaf_Crm_Constants::LEAF_WEBSITE_NAME); ?>" id="<?php echo esc_html(Leaf_Crm_Constants::LEAF_WEBSITE_NAME); ?>" type="text" value="">
                            <span class="fs-8 text-muted"><?php esc_html_e('This Website Name will be displayed under the Lead Source information in the Leaf app', 'leaf-crm') ?></span>

                            <div class="invalid-feedback"><span id="<?php echo esc_html(Leaf_Crm_Constants::LEAF_WEBSITE_NAME); ?>_error"></span></div>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    <?php
                    }
                    ?>

                    <div class="text-dark fs-7 fw-normal"><?php esc_html_e('Contacts Form', 'leaf-crm') ?></div>
                    <span class="fs-8 text-muted">
                        <?php
                        if ($wp_settings['leafToken'] != null) {
                            esc_html_e('Choose which Contacts Form plugins you want to integrate with Leaf', 'leaf-crm');
                        } else {
                            esc_html_e('Leaf CRM works with multiple Contacts Form plugins. Simply enter your Leaf CRM Token above and click SAVE to turn on the integration!', 'leaf-crm');
                        }
                        ?>
                    </span>
                    <!-- List out all supported WordPress Contact Forms -->
                    <div id="list-integrations" class="mb-4 mt-3"></div>

                    <div class="d-grid gap-2">
                        <button type="button" id="save_integration" class="btn btn-gradient btn-active-gradient min-w-150px min-h-40px rounded-1 w-100 rounded fw-boldest fs-6 px-10 py-2" disabled>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-label text-uppercase"><?php esc_html_e('Save', 'leaf-crm') ?></span>
                            <span class="btn-loading-label d-none text-uppercase"><?php esc_html_e('Loading...', 'leaf-crm') ?></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        toastr.options.toastClass = 'toastr';

        const wpSettingsData = <?php echo wp_json_encode($wp_settings, JSON_HEX_TAG); ?>;

        jQuery(document).ready(function() {

            // populate saved data
            jQuery('#<?php echo esc_html(Leaf_Crm_Constants::LEAF_TOKEN_NAME); ?>').val(wpSettingsData.leafToken);
            jQuery('#<?php echo esc_html(Leaf_Crm_Constants::LEAF_WEBSITE_NAME); ?>').val(wpSettingsData.leafWebsite);

            listIntegrations();

            const integrationForm = jQuery('#leaf-integration-form');

            jQuery('#leaf-integration-form input').on('keyup', function() {
                enableBtn('#save_integration', true)
            });

            jQuery('#save_integration').click(function() {
                event.preventDefault();
                subscribeLeaf();
            });

            function preparePayload() {
                var formData = integrationForm.serializeArray();
                formData.push({
                    name: "site_url",
                    value: wpSettingsData.wordpressSiteUrl
                }, {
                    name: "lang",
                    value: "<?php echo esc_html($locale); ?>"
                });

                return formData;
            }

            function subscribeLeaf() {
                toggleBtnSpinner('#save_integration', true)
                integrationForm.find('.is-invalid').removeClass('is-invalid');
                jQuery.ajax({
                    type: "POST",
                    url: wpSettingsData.leafSubscribeUrl,
                    data: preparePayload(),
                    success: function(data) {
                        saveIntoWP();
                        toastr.success(data?.responseJSON?.message || "<?php esc_html_e('Your settings have been updated', 'leaf-crm') ?>")

                        if (wpSettingsData.leafToken == null || wpSettingsData.leafToken == '') {
                            window.location.reload();
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.error(jqXHR?.responseJSON?.errorMessage || "<?php esc_html_e('Failed to update your settings. Please try again later.', 'leaf-crm') ?>");
                        if (jqXHR?.responseJSON?.errors) {
                            for (const [key, value] of Object.entries(jqXHR.responseJSON.errors)) {
                                jQuery("#" + key).addClass("is-invalid");
                                jQuery("#" + key + "_error").text(value);

                                toastr.error(value);
                            }
                        }
                    },
                    complete: function(data) {
                        toggleBtnSpinner('#save_integration', false);
                        enableBtn('#save_integration', false);
                    }
                });
            }

            function saveIntoWP(showToaster = false) {
                if (showToaster) {
                    toggleBtnSpinner('#save_integration', true);
                }

                var formData = preparePayload();
                formData.push({
                    name: "action",
                    value: wpSettingsData.formActionName
                });

                jQuery.ajax({
                    type: "POST",
                    url: wpSettingsData.wordpressAjaxUrl,
                    data: formData,
                    success: function(data) {
                        if (showToaster) {
                            toastr.success(data?.responseJSON?.message || "<?php esc_html_e('Your settings have been updated', 'leaf-crm') ?>")
                            toggleBtnSpinner('#save_integration', false);
                            enableBtn('#save_integration', false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.error(jqXHR?.responseJSON?.errorMessage || "<?php esc_html_e('Failed to update your settings. Please try again later.', 'leaf-crm') ?>");
                        if (jqXHR?.responseJSON?.data) {
                            for (const [key, value] of Object.entries(jqXHR.responseJSON.data)) {
                                jQuery("#" + key).addClass("is-invalid");
                                jQuery("#" + key + "_error").text(value);

                                toastr.error(value);
                            }
                        }
                    },
                    complete: function(data) {
                        toggleBtnSpinner('#save_integration', false);
                        enableBtn('#save_integration', false);
                    }
                });
            }

            function listIntegrations() {
                jQuery('#list-integrations').empty();

                const forms = wpSettingsData.availableIntegrations;

                Object.keys(forms).forEach(key => {
                    var isEnable = (forms[key].enabled) ? 'checked' : '';

                    var output = '';
                    if (wpSettingsData.leafToken != null && wpSettingsData.leafToken != '') {
                        var output = `
                    
                            <div class="shadow-sm p-3 mb-3 bg-body rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <img src="` + forms[key].icon + `" class="wp-cf-plugin-icon me-2"/>
                                        <label class="form-check-label" for="form_` + forms[key].key + `">` + forms[key].name + `</label>
                                    </div>
                                    <div class="form-check form-switch form-check-solid integration-item">
                                        <input class="form-check-input enable_integration" type="checkbox" name="` + forms[key].key + `" id="form_` + forms[key].key + `" value="true" ` + isEnable + `>
                                        
                                    </div>
                                </div>
                                <div class="text-danger">
                                    <small id="form_` + forms[key].key + `_error"></small>
                                </div>
                            </div>

                        `;
                    } else {
                        var output = `
                            <span class="badge text-bg-light mb-2"><img src="` + forms[key].icon + `" class="wp-cf-plugin-icon-sm me-2"/> ` + forms[key].name + `</span>
                        `;
                    }


                    jQuery('#list-integrations').append(output);
                });
            }

            jQuery(document).on('click', '.enable_integration', function() {
                const index = jQuery('.enable_integration').index(this);
                const forms = wpSettingsData.availableIntegrations;
                const status = forms[index].status;

                if (status == wpSettingsData.Status.NotExist || status == wpSettingsData.Status.Installed) {
                    const errorMsg = 'This plugin is currently not activated.'
                    jQuery("#form_" + forms[index].key + "_error").text(errorMsg);

                    setTimeout(() => {
                        jQuery(this).prop("checked", false);
                    }, 200)
                } else {
                    saveIntoWP(true); // save settings into DB

                    jQuery("#form_" + forms[index].key + "_error").text("");
                }
            });

            function toggleBtnSpinner(selector, state) {
                const btn = jQuery(selector);

                if (state) {
                    btn.prop('disabled', true);
                    btn.find('.spinner-border').removeClass('d-none');
                    btn.find('.btn-loading-label').removeClass('d-none');
                    btn.find('.btn-label').addClass('d-none');
                } else {
                    btn.prop('disabled', false);
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.btn-loading-label').addClass('d-none');
                    btn.find('.btn-label').removeClass('d-none');
                }
            }

            function enableBtn(selector, state) {
                const btn = jQuery(selector);
                btn.prop('disabled', !state);
            }
        });
    </script>

<?php
}

//<!-- This file should primarily consist of HTML with a little bit of PHP. -->
