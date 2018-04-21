<?php

class MPSLLicense {

    public function __construct(){
        $this->addActions();
    }

    function renderPage() {
        global $mpsl_settings;

        $license = get_option('edd_mpsl_license_key');

        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            add_settings_error(
                'mpslLicense',
                esc_attr('settings_updated'),
                __('Settings saved.', 'motopress-slider'),
                'updated'
            );
        }

        if ($license) {
            $licenseData = $this->checkLicense($license);
        }
        ?>
        <div class="wrap">
            <?php screen_icon('options-general'); ?>
            <h2><?php _e('Slider License', 'motopress-slider'); ?></h2>
            <i><?php _e("The License Key is required in order to get automatic plugin updates and support. You can manage your License Key in your personal account. <a href='https://motopress.zendesk.com/hc/en-us/articles/202812996-How-to-use-your-personal-MotoPress-account' target='blank'>Learn more</a>.", 'motopress-slider'); ?></i>
            <?php settings_errors('mpslLicense', false); ?>
            <form action="" method="POST" autocomplete="off">
                <?php wp_nonce_field('edd_mpsl_nonce', 'edd_mpsl_nonce'); ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php echo __('License Key', 'motopress-slider') . " (" . $mpsl_settings['license_type'] . ")"; ?>
                        </th>
                        <td>
                            <input id="edd_mpsl_license_key" name="edd_mpsl_license_key" type="password"
                                   class="regular-text" value="<?php esc_attr_e($license); ?>"/>
                            <?php if ($license) { ?>
                                <i style="display:block;"><?php echo str_repeat("&#8226;", 20) . substr($license, -7); ?></i>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if ($license) { ?>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <?php _e('Status', 'motopress-slider'); ?>
                            </th>
                            <td>
                                <?php
                                if ($licenseData) {
                                    switch($licenseData->license) {
                                        case 'inactive' :
                                        case 'site_inactive' :
                                            _e('Inactive', 'motopress-slider');
                                            break;
                                        case 'valid' :
											if ($licenseData->expires !== 'lifetime') {
												$date = ($licenseData->expires) ? new DateTime($licenseData->expires) : false;
												$expires = ($date) ? ' ' . $date->format('d.m.Y') : '';
												echo __('Valid until', 'motopress-slider') . $expires;
											} else {
												echo __('Valid (Lifetime)', 'motopress-slider');
											}
                                            break;
                                        case 'disabled' :
                                            _e('Disabled', 'motopress-slider');
                                            break;
                                        case 'expired' :
                                            _e('Expired', 'motopress-slider');
                                            break;
                                        case 'invalid' :
                                            _e('Invalid', 'motopress-slider');
                                            break;
                                        case 'item_name_mismatch' :
                                            _e("Your License Key does not match the installed plugin. <a href='https://motopress.zendesk.com/hc/en-us/articles/202957243-What-to-do-if-the-license-key-doesn-t-correspond-with-the-plugin-license' target='_blank'>How to fix this.</a>", 'motopress-slider');
                                            break;
                                        case 'invalid_item_id' :
		                                    _e('Product ID is not valid', 'motopress-slider');
		                                    break;
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if (isset($licenseData->license) && in_array($licenseData->license, array('inactive', 'site_inactive', 'valid', 'expired'))) { ?>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <?php _e('Action', 'motopress-slider'); ?>
                            </th>
                            <td>
                                <?php
                                if ($licenseData) {
                                    if ($licenseData->license === 'inactive' || $licenseData->license === 'site_inactive') {
                                        wp_nonce_field('edd_mpsl_nonce', 'edd_mpsl_nonce'); ?>
                                        <input type="submit" class="button-secondary" name="edd_license_activate"
                                               value="<?php _e('Activate License', 'motopress-slider'); ?>"/>
                                    <?php
                                    } elseif ($licenseData->license === 'valid') {
                                        wp_nonce_field('edd_mpsl_nonce', 'edd_mpsl_nonce'); ?>
                                        <input type="submit" class="button-secondary" name="edd_license_deactivate"
                                               value="<?php _e('Deactivate License', 'motopress-slider'); ?>"/>
                                    <?php
                                    } elseif ($licenseData->license === 'expired') { ?>
                                        <a href="<?php echo $mpsl_settings['renew_url']; ?>"
                                           class="button-secondary"
                                           target="_blank"><?php _e('Renew License', 'motopress-slider'); ?></a>
                                    <?php
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
                <?php submit_button(__('Save', 'motopress-slider')); ?>
            </form>
        </div>
    <?php
    }

    // check a license key
    private function checkLicense($license) {
        global $mpsl_settings;

	    $apiParams = array(
		    'edd_action' => 'check_license',
		    'license'    => $license,
		    'item_id'    => $mpsl_settings['edd_mpsl_item_id'],
		    'url'        => home_url(),
	    );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($apiParams, $mpsl_settings['edd_mpsl_store_url']), array('timeout' => 15, 'sslverify' => false));

        if (is_wp_error($response)) {
            return false;
        }

        $licenseData = json_decode(wp_remote_retrieve_body($response));

        return $licenseData;
    }

    public function save() {
        global $mpsl_settings;

        if (!empty($_POST)) {
            $queryArgs = array('page' => $_GET['page']);

            if (isset($_POST['edd_mpsl_license_key'])) {
                if (!check_admin_referer('edd_mpsl_nonce', 'edd_mpsl_nonce')) {
                    return;
                }
                $licenseKey = trim($_POST['edd_mpsl_license_key']);
                self::setLicenseKey($licenseKey);
            }

            //activate
            if (isset($_POST['edd_license_activate'])) {
                if (!check_admin_referer('edd_mpsl_nonce', 'edd_mpsl_nonce')) {
                    return; // get out if we didn't click the Activate button
                }
                $licenseData = self::activateLicense();

                if ($licenseData === false)
                    return false;

                if (!$licenseData->success && $licenseData->error === 'item_name_mismatch') {
                    $queryArgs['item-name-mismatch'] = 'true';
                }
            }

            //deactivate
            if (isset($_POST['edd_license_deactivate'])) {
                // run a quick security check
                if (!check_admin_referer('edd_mpsl_nonce', 'edd_mpsl_nonce')) {
                    return; // get out if we didn't click the Activate button
                }
                // retrieve the license from the database
                $licenseData = self::deactivateLicense();

                if ($licenseData === false)
                    return false;
            }

            $queryArgs['settings-updated'] = 'true';
            wp_redirect(add_query_arg($queryArgs, get_admin_url() . 'admin.php'));
        }
    }

    static public function setLicenseKey($licenseKey){
        $oldLicenseKey = get_option('edd_mpsl_license_key');
        if ($oldLicenseKey && $oldLicenseKey !== $licenseKey) {
            delete_option('edd_mpsl_license_status'); // new license has been entered, so must reactivate
        }
        if (!empty($licenseKey)) {
            update_option('edd_mpsl_license_key', $licenseKey);
        } else {
            delete_option('edd_mpsl_license_key');
        }
    }

    static public function activateLicense() {
        global $mpsl_settings;
        $licenseKey = get_option('edd_mpsl_license_key');

	    // data to send in our API request
	    $apiParams = array(
		    'edd_action' => 'activate_license',
		    'license'    => $licenseKey,
		    'item_id'    => $mpsl_settings['edd_mpsl_item_id'],
		    'url'        => home_url(),
	    );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($apiParams, $mpsl_settings['edd_mpsl_store_url']), array('timeout' => 15, 'sslverify' => false));

        // make sure the response came back okay
        if (is_wp_error($response)) {
            return false;
        }

        // decode the license data
        $licenseData = json_decode(wp_remote_retrieve_body($response));

        // $licenseData->license will be either "active" or "inactive"
        update_option('edd_mpsl_license_status', $licenseData->license);

        return $licenseData;
    }

    static public function deactivateLicense() {
        global $mpsl_settings;
        $licenseKey = get_option('edd_mpsl_license_key');

        // data to send in our API request
        $apiParams = array(
	        'edd_action' => 'deactivate_license',
	        'license'    => $licenseKey,
	        'item_id'    => $mpsl_settings['edd_mpsl_item_id'],
	        'url'        => home_url(),
        );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($apiParams, $mpsl_settings['edd_mpsl_store_url']), array('timeout' => 15, 'sslverify' => false));

        // make sure the response came back okay
        if (is_wp_error($response)) {
            return false;
        }

        // decode the license data
        $licenseData = json_decode(wp_remote_retrieve_body($response));

        // $license_data->license will be either "deactivated" or "failed"
        if ($licenseData->license == 'deactivated') {
            delete_option('edd_mpsl_license_status');
        }

        return $licenseData;
    }

    static public function setAndActivateLicenseKey($licenseKey){
        self::setLicenseKey($licenseKey);
        self::activateLicense();
    }

    public function licenseNotice() {
        global $pagenow, $mpsl_settings;
        $isHideLicensePage = apply_filters('mpsl_hide_license_page', false);
        $isDisableUpdater = apply_filters('mpsl_disable_updater', false);
        if ($pagenow === 'plugins.php' && is_main_site() && !$isHideLicensePage && !$isDisableUpdater) {
            $isHideLicenseNotice = get_option('mpsl_hide_license_notice', false);
            if (!$isHideLicenseNotice) {
                $license = get_option('edd_mpsl_license_key');
                if ($license) {
                    $licenseData = $this->checkLicense($license);
                }
                $dismissActionName = 'mpsl_dismiss_license_notice';
                if (!$license || !isset($licenseData->license) || $licenseData->license !== 'valid') {
                    ?>
                    <div class="error">
                        <a id="mpsl-dismiss-license-notice" href="javascript:void(0);" style="float: right;padding-top: 9px; text-decoration: none;">
                            <?php _e("Dismiss ", 'motopress-slider'); ?><strong>X</strong>
                        </a>
                        <p>
                        <?php _e(sprintf(
                            "<b>%s:</b> Your License Key is not active. Please, <a href='%s'>activate your License Key</a> to get plugin updates",
                            $mpsl_settings['product_name'],
                            admin_url('admin.php?page=motopress-slider-license')
                        ), 'motopress-slider'); ?>
                        </p>
                    </div>
                    <script type="text/javascript">
                        (function($){
                            var dismissBtn = $('#mpsl-dismiss-license-notice');
                            dismissBtn.one('click', function(){
                                $.ajax({
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        action: '<?php echo $dismissActionName; ?>',
                                        nonce: '<?php echo wp_create_nonce('wp_ajax_' . $dismissActionName);?>',
                                    }
                                });
                                dismissBtn.closest('div.error').remove();
                            });
                        })(jQuery);
                    </script>
                    <?php
                }
            }
        }
    }

    function licenseNoticeDismiss(){
        mpslVerifyNonce();
        update_option('mpsl_hide_license_notice', true);
    }


    private function addActions() {
        /*add_action('admin_notices', array($this, 'licenseNotice'));
        add_action('wp_ajax_mpsl_dismiss_license_notice', array($this, 'licenseNoticeDismiss'));
        if (is_multisite()) add_action('network_admin_notices', array($this, 'licenseNotice'));*/
    }

    public function addMenu(){
        global $mpsl_settings;
        $licenseHook = add_submenu_page($mpsl_settings['plugin_name'], __('License', 'motopress-slider'), __('License', 'motopress-slider'), 'manage_options', 'motopress-slider-license', array($this, 'renderPage'));
        add_action('load-' . $licenseHook, array($this, 'save'));
    }
}