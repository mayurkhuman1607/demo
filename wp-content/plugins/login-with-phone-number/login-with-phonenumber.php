<?php
/*
Plugin Name: Login with phone number
Plugin URI: http://drc.com/login-with-phone-number
Description: Login with phone number - sending sms - activate user by phone number - limit pages to login - register and login with ajax - modal
Version: 1.4.653
Author: Hamid Alinia - drc
Author URI: http://drc.com
Text Domain: login-with-phone-number
Domain Path: /languages
*/
require 'gateways/class-lwp-custom-api.php';

class drcLwp
{
    function __construct()
    {
        add_action('init', array(&$this, 'drc_lwp_textdomain'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('wp_ajax_drc_lwp_auth_customer', array(&$this, 'drc_lwp_auth_customer'));
        add_action('wp_ajax_drc_lwp_auth_customer_with_website', array(&$this, 'drc_lwp_auth_customer_with_website'));
        add_action('wp_ajax_drc_lwp_activate_customer', array(&$this, 'drc_lwp_activate_customer'));
        add_action('wp_ajax_drc_lwp_check_credit', array(&$this, 'drc_lwp_check_credit'));
        add_action('wp_ajax_drc_lwp_get_shop', array(&$this, 'drc_lwp_get_shop'));
        add_action('wp_ajax_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('wp_ajax_lwp_verify_domain', array(&$this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_verify_domain', array(&$this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_nopriv_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_nopriv_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_nopriv_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_nopriv_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_nopriv_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('activated_plugin', array(&$this, 'lwp_activation_redirect'));

        add_action('show_user_profile', array(&$this, 'lwp_add_phonenumber_field'));
        add_action('edit_user_profile', array(&$this, 'lwp_add_phonenumber_field'));

        add_action('personal_options_update', array(&$this, 'lwp_update_phonenumber_field'));
        add_action('edit_user_profile_update', array(&$this, 'lwp_update_phonenumber_field'));

        add_action('wp_head', array(&$this, 'lwp_custom_css'));

//        add_action('admin_bar_menu', array(&$this, 'credit_adminbar'), 100);
//        add_action('login_enqueue_scripts', array(&$this, 'admin_custom_css'));


        add_action('rest_api_init', array(&$this, 'lwp_register_rest_route'));
        add_filter('manage_users_columns', array(&$this, 'lwp_modify_user_table'));
        add_filter('manage_users_custom_column', array(&$this, 'lwp_modify_user_table_row'), 10, 3);
        add_filter('manage_users_sortable_columns', array(&$this, 'lwp_make_registered_column_sortable'));
        add_filter('woocommerce_locate_template', array(&$this, 'lwp_addon_woocommerce_login'), 1, 3);


        add_shortcode('drc_lwp', array(&$this, 'shortcode'));
        add_shortcode('drc_lwp_metas', array(&$this, 'drc_lwp_metas'));

    }

    function lwp_add_phonenumber_field($user)
    {
        $phn = get_the_author_meta('phone_number', $user->ID);
        ?>
        <h3><?php esc_html_e('Personal Information', 'login-with-phone-number'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="phone_number"><?php esc_html_e('phone_number', 'login-with-phone-number'); ?></label>
                </th>
                <td>
                    <input type="text"

                           step="1"
                           id="phone_number"
                           name="phone_number"
                           value="<?php echo esc_attr($phn); ?>"
                           class="regular-text"
                    />

                </td>
            </tr>
        </table>
        <?php
    }

    function lwp_update_phonenumber_field($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        $phone_number = sanitize_text_field($_POST['phone_number']);
        update_user_meta($user_id, 'phone_number', $phone_number);
    }

    function lwp_activation_redirect($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            exit(wp_redirect(admin_url('admin.php?page=drc-lwp')));
        }
    }

    function drc_lwp_textdomain()
    {
        $drc_lwp_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
        $drc_lwp_lang_dir = apply_filters('drc_lwp_languages_directory', $drc_lwp_lang_dir);

        load_plugin_textdomain('login-with-phone-number', false, $drc_lwp_lang_dir);


    }

    function admin_init()
    {
        $options = get_option('drc_lwp_settings');
//        print_r($options);
        $style_options = get_option('drc_lwp_settings_styles');
//        print_r($style_options);

        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        if (!isset($style_options['drc_styles_status'])) $style_options['drc_styles_status'] = '1';

        register_setting('drc-lwp', 'drc_lwp_settings', array(&$this, 'settings_validate'));
        register_setting('drc-lwp-styles', 'drc_lwp_settings_styles', array(&$this, 'settings_validate'));
        register_setting('drc-lwp-localization', 'drc_lwp_settings_localization', array(&$this, 'settings_validate'));

        add_settings_section('drc-lwp-styles', '', array(&$this, 'section_intro'), 'drc-lwp-styles');
        add_settings_section('drc-lwp-localization', '', array(&$this, 'section_intro'), 'drc-lwp-localization');
        add_settings_field('drc_styles_status', __('Enable custom styles', 'login-with-phone-number'), array(&$this, 'setting_drc_style_enable_custom_style'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

        if ($style_options['drc_styles_status']) {
//            add_settings_field('drc_styles_title1', 'tyuiuy', array(&$this, 'section_title'), 'drc-lwp-styles');
            add_settings_field('drc_styles_title', __('Primary button', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_background', __('button background color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_background_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_color', __('button border color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_radius', __('button border radius', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_radius'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_width', __('button border width', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_width'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_text_color', __('button text color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_text_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

//            add_settings_section('drc_styles_title2', '', array(&$this, 'section_title'), 'drc-lwp-styles');
            add_settings_field('drc_styles_title2', __('Secondary button', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('drc_styles_button_background2', __('secondary button background color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_background_color2'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_color2', __('secondary button border color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_color2'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_radius2', __('secondary button border radius', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_radius2'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_border_width2', __('secondary button border width', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_border_width2'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_button_text_color2', __('secondary button text color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_button_text_color2'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('drc_styles_title3', __('Inputs', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('drc_styles_input_background', __('input background color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_background_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_input_border_color', __('input border color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_border_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_input_border_radius', __('input border radius', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_border_radius'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_input_border_width', __('input border width', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_border_width'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_input_text_color', __('input text color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_text_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_input_placeholder_color', __('input placeholder color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_input_placeholder_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('drc_styles_title4', __('Box', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_box_background_color', __('box background color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_box_background_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_position_form', __('Enable fix position', 'login-with-phone-number'), array(&$this, 'drc_position_form'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('drc_styles_title5', __('Labels', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_labels_text_color', __('label text color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_labels_text_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_labels_font_size', __('label font size', 'login-with-phone-number'), array(&$this, 'setting_drc_style_labels_font_size'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('drc_styles_title6', __('Titles', 'login-with-phone-number'), array(&$this, 'section_title'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_title_color', __('title color', 'login-with-phone-number'), array(&$this, 'setting_drc_style_title_color'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('drc_styles_title_font_size', __('title font size', 'login-with-phone-number'), array(&$this, 'setting_drc_style_title_font_size'), 'drc-lwp-styles', 'drc-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


        }

        add_settings_section('drc-lwp', '', array(&$this, 'section_intro'), 'drc-lwp');

        add_settings_field('drc_sms_login', __('Enable phone number login', 'login-with-phone-number'), array(&$this, 'setting_drc_sms_login'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);

        $ghgfd = '';
        if ($options['drc_token']) {
            $ghgfd = ' none';
        }
//        add_settings_field('drc_phone_number_ccode', __('Enter your Country Code', 'login-with-phone-number'), array(&$this, 'setting_drc_phone_number'), 'drc-lwp', 'drc-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
//        add_settings_field('drc_phone_number', __('Enter your phone number', 'login-with-phone-number'), array(&$this, 'setting_drc_phone_number'), 'drc-lwp', 'drc-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
        add_settings_field('drc_website_url', __('Enter your website url', 'login-with-phone-number'), array(&$this, 'setting_drc_website_url'), 'drc-lwp', 'drc-lwp', ['class' => 'ilwplabel lwp_website_label related_to_login' . $ghgfd]);
//        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        add_settings_field('drc_token', __('Enter api key', 'login-with-phone-number'), array(&$this, 'setting_drc_token'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel alwaysDisplayNone']);
        add_settings_field('drc_country_codes', __('Country code accepted in front', 'login-with-phone-number'), array(&$this, 'setting_country_code'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);

        if ($options['drc_token']) {

            add_settings_field('drc_sms_shop', __('Buy credit here', 'login-with-phone-number'), array(&$this, 'setting_buy_credit'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login rltll']);
        }
        add_settings_field('drc_use_custom_gateway', __('use custom sms gateway', 'login-with-phone-number'), array(&$this, 'setting_use_custom_gateway'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);
        add_settings_field('drc_default_gateways', __('sms default gateway', 'login-with-phone-number'), array(&$this, 'setting_default_gateways'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_defaultgateway']);

        add_settings_field('drc_firebase_api', __('Firebase api', 'login-with-phone-number'), array(&$this, 'setting_firebase_api'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase']);
        add_settings_field('drc_firebase_config', __('Firebase config', 'login-with-phone-number'), array(&$this, 'setting_firebase_config'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase']);

        add_settings_field('drc_custom_api_url', __('Custom api url', 'login-with-phone-number'), array(&$this, 'setting_custom_api_url'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom']);
        add_settings_field('drc_custom_api_method', __('Custom api method', 'login-with-phone-number'), array(&$this, 'setting_custom_api_method'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom']);
        add_settings_field('drc_custom_api_header', __('Custom api header', 'login-with-phone-number'), array(&$this, 'setting_custom_api_header'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom']);
        add_settings_field('drc_custom_api_body', __('Custom api body', 'login-with-phone-number'), array(&$this, 'setting_custom_api_body'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom']);
        add_settings_field('drc_custom_api_smstext', __('Custom api sms text', 'login-with-phone-number'), array(&$this, 'setting_custom_api_smstext'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom']);

        add_settings_field('drc_lwp_space', __('', 'login-with-phone-number'), array(&$this, 'setting_drc_lwp_space'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel drc_lwp_mgt100']);
        add_settings_field('drc_email_login', __('Enable email login', 'login-with-phone-number'), array(&$this, 'setting_drc_email_login'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_lwp_space2', __('', 'login-with-phone-number'), array(&$this, 'setting_drc_lwp_space'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel drc_lwp_mgt100']);

        add_settings_field('drc_user_registration', __('Enable user registration', 'login-with-phone-number'), array(&$this, 'setting_drc_user_registration'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_password_login', __('Enable password login', 'login-with-phone-number'), array(&$this, 'setting_drc_password_login'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_redirect_url', __('Enter redirect url', 'login-with-phone-number'), array(&$this, 'setting_drc_url_redirect'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_login_message', __('Enter login message', 'login-with-phone-number'), array(&$this, 'setting_drc_login_message'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_use_phone_number_for_username', __('use phone number for username', 'login-with-phone-number'), array(&$this, 'drc_use_phone_number_for_username'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_default_username', __('Default username', 'login-with-phone-number'), array(&$this, 'setting_default_username'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu']);
        add_settings_field('drc_default_nickname', __('Default nickname', 'login-with-phone-number'), array(&$this, 'setting_default_nickname'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu']);
        add_settings_field('drc_enable_timer_on_sending_sms', __('Enable timer', 'login-with-phone-number'), array(&$this, 'drc_enable_timer_on_sending_sms'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);
        add_settings_field('drc_timer_count', __('Timer count', 'login-with-phone-number'), array(&$this, 'setting_timer_count'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_entimer']);
        add_settings_field('drc_enable_accept_terms_and_condition', __('Enable accept term & conditions', 'login-with-phone-number'), array(&$this, 'drc_enable_accept_term_and_conditions'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);
        add_settings_field('drc_term_and_conditions_text', __('Text of term & conditions part', 'login-with-phone-number'), array(&$this, 'setting_term_and_conditions_text'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);


        add_settings_field('drc_lwp_space3', __('', 'login-with-phone-number'), array(&$this, 'setting_drc_lwp_space'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel drc_lwp_mgt100']);
        add_settings_field('instructions', __('Shortcode and Template Tag', 'login-with-phone-number'), array(&$this, 'setting_instructions'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_online_support', __('Enable online support', 'login-with-phone-number'), array(&$this, 'drc_online_support'), 'drc-lwp', 'drc-lwp', ['label_for' => '', 'class' => 'ilwplabel']);


        add_settings_field('drc_localization_status', __('Enable localization', 'login-with-phone-number'), array(&$this, 'setting_drc_localization_enable_custom_localization'), 'drc-lwp-localization', 'drc-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_localization_title_of_login_form', __('Title of login form (with phone number)', 'login-with-phone-number'), array(&$this, 'setting_drc_localization_of_login_form'), 'drc-lwp-localization', 'drc-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_localization_title_of_login_form1', __('Title of login form (with email)', 'login-with-phone-number'), array(&$this, 'setting_drc_localization_of_login_form_email'), 'drc-lwp-localization', 'drc-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('drc_localization_placeholder_of_phonenumber_field', __('Placeholder of phone number field', 'login-with-phone-number'), array(&$this, 'setting_drc_localization_placeholder_of_phonenumber_field'), 'drc-lwp-localization', 'drc-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);

    }

    function admin_menu()
    {

        $icon_url = 'dashicons-smartphone';
        $page_hook = add_menu_page(
            __('login setting', 'login-with-phone-number'),
            __('login setting', 'login-with-phone-number'),
            'manage_options',
            'drc-lwp',
            array(&$this, 'settings_page'),
            $icon_url
        );
        add_submenu_page('drc-lwp', __('Style settings', 'login-with-phone-number'), __('Style Settings', 'login-with-phone-number'), 'manage_options', 'drc-lwp-styles', array(&$this, 'style_settings_page'));
        add_submenu_page('drc-lwp', __('Text & localization', 'login-with-phone-number'), __('Text & localization', 'login-with-phone-number'), 'manage_options', 'drc-lwp-localization', array(&$this, 'localization_settings_page'));

        add_action('admin_print_styles-' . $page_hook, array(&$this, 'admin_custom_css'));
        wp_enqueue_script('drc-lwp-admin-select2-js', plugins_url('/scripts/select2.full.min.js', __FILE__), array('jquery'), true, true);
        wp_enqueue_script('drc-lwp-admin-chat-js', plugins_url('/scripts/chat.js', __FILE__), array('jquery'), true, true);

    }

    function admin_custom_css()
    {
        wp_enqueue_style('drc-lwp-admin', plugins_url('/styles/lwp-admin.css', __FILE__));
        wp_enqueue_style('drc-lwp-admin-select2-style', plugins_url('/styles/select2.min.css', __FILE__));


    }

    function settings_page()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        if (!isset($options['drc_online_support'])) $options['drc_online_support'] = '1';


        ?>
        <div class="wrap">
            <div class="lwp-wrap-left">


                <div id="icon-themes" class="icon32"></div>
                <h2><?php _e('drcLwp Settings', 'login-with-phone-number'); ?></h2>
                <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                    ?>
                    <div id="setting-error-settings_updated" class="updated settings-error">
                        <p><strong><?php _e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                    </div>
                <?php } ?>
                <form action="options.php" method="post" id="iuytfrdghj">
                    <?php settings_fields('drc-lwp'); ?>
                    <?php do_settings_sections('drc-lwp'); ?>

                    <p class="submit">
                        <span id="wkdugchgwfchevg3r4r"></span>
                    </p>
                    <p class="submit">
                        <span id="oihdfvygehv"></span>
                    </p>
                    <p class="submit">
                     
                        <input type="submit" class="button-primary"
                               value="<?php _e('Save Changes', 'login-with-phone-number'); ?>"/></p>
                    ?>
                    <?php
                    if (empty($options['drc_token'])) {
                        ?>
                      
                    <?php } ?>
                </form>
            </div>
            <div class="lwp-wrap-right">
                <a href="https://drc.com/product/login-with-phone-number-in-wordpress/" target="_blank">
                    <img src="<?php echo plugins_url('/images/login-with-phone-number-wordpress-buy-pro-version.png', __FILE__) ?>"/>
                </a>

                <a style="margin-top: 10px;display:block" href="<?php echo esc_url(admin_url('/theme-install.php?theme=nodeeweb')) ?>" target="_blank">
                    <img src="<?php echo plugins_url('/images/nodeeweb-wordpress-theme.png', __FILE__) ?>"/>
                </a>
            </div>

            <?php
            if ($options['drc_online_support'] == '1') {
                ?>
                <script type="text/javascript">window.makecrispactivate = 1;</script>
            <?php } ?>

            <script>
                <?php

                ?>
                jQuery(function ($) {
                    var drc_country_codes = $("#drc_country_codes");
                    var drc_phone_number_ccodeG = '1';
                    $(window).load(function () {

                        $('.loiuyt').click();
                        $('.refreshShop').click();
                        $("#drc_phone_number_ccode").select2();
                        drc_country_codes.select2();

                        <?php
                        if (empty($options['drc_token'])) {
                        ?>
                        $('.authwithwebsite').click();
                        <?php } ?>

                    });

                    var edf = $('#drc_lwp_settings_drc_sms_login');
                    var edf2 = $('#drc_lwp_settings_use_phone_number_for_username');
                    var edf3 = $('#drc_lwp_settings_use_custom_gateway');
                    var edf4 = $('#drc_default_gateways');
                    var edf5 = $('#drc_lwp_settings_enable_timer_on_sending_sms');
                    var drc_body = $('body');
                    var related_to_login = $('.related_to_login');
                    var related_to_upnfu = $('.related_to_upnfu');
                    var related_to_entimer = $('.related_to_entimer');
                    var related_to_defaultgateway = $('.related_to_defaultgateway');
                    var related_to_customgateway = $('.related_to_customgateway');
                    var related_to_twilio = $('.related_to_twilio');
                    var related_to_zenziva = $('.related_to_zenziva');
                    var related_to_infobip = $('.related_to_infobip');
                    var related_to_raygansms = $('.related_to_raygansms');
                    var related_to_smsbharti = $('.related_to_smsbharti');
                    var related_to_mshastra = $('.related_to_mshastra');
                    var related_to_taqnyat = $('.related_to_taqnyat');
                    var related_to_firebase = $('.related_to_firebase');
                    var related_to_custom = $('.related_to_custom');

                    if (edf.is(':checked')) {
                        related_to_login.css('display', 'table-row');
                        // $("#drc_phone_number_ccode").chosen();


                    } else {

                        related_to_login.css('display', 'none');
                    }


                    if (edf2.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_upnfu.css('display', 'none');


                    } else {
                        // console.log('is not checked!');
                        related_to_upnfu.css('display', 'table-row');

                    }
                    if (edf5.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();

                        related_to_entimer.css('display', 'table-row');

                    } else {
                        // console.log('is not checked!');
                        related_to_entimer.css('display', 'none');

                    }

                    if (edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_defaultgateway.css('display', 'table-row');
                        $('.rltll').css('display', 'none');


                    } else {
                        // console.log('is not checked!');
                        related_to_defaultgateway.css('display', 'none');


                    }
                    if (edf4.val() == 'twilio' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_twilio.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_twilio.css('display', 'none');


                    }
                    if (edf4.val() == 'custom' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_customgateway.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_customgateway.css('display', 'none');


                    }
                    if (edf4.val() == 'zenziva' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_zenziva.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_zenziva.css('display', 'none');


                    }
                    if (edf4.val() == 'firebase' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_firebase.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_firebase.css('display', 'none');


                    }
                    if (edf4.val() == 'infobip' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_infobip.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_infobip.css('display', 'none');


                    }
                    if (edf4.val() == 'raygansms' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_raygansms.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_raygansms.css('display', 'none');


                    }
                    if (edf4.val() == 'smsbharti' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_smsbharti.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_smsbharti.css('display', 'none');


                    }
                    if (edf4.val() == 'mshastra' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_mshastra.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_mshastra.css('display', 'none');


                    }
                    if (edf4.val() == 'taqnyat' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_taqnyat.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_taqnyat.css('display', 'none');


                    }
                    if (edf4.val() == 'custom' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#drc_phone_number_ccode").chosen();
                        related_to_custom.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_custom.css('display', 'none');


                    }
                    $('#drc_lwp_settings_drc_sms_login').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                related_to_login.css('display', 'table-row');
                                // $("#drc_phone_number_ccode").chosen();

                            } else {
                                // console.log('change is not checked!');

                                related_to_login.css('display', 'none');
                            }
                        });
                    $('#drc_lwp_settings_use_phone_number_for_username').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#drc_phone_number_ccode").chosen();
                                related_to_upnfu.css('display', 'none');

                            } else {
                                // console.log('change is not checked!');
                                related_to_upnfu.css('display', 'table-row');

                            }
                        });
                    $('#drc_lwp_settings_use_custom_gateway').change(
                        function () {
                            $('#drc_default_gateways').trigger('change');
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#drc_phone_number_ccode").chosen();
                                related_to_defaultgateway.css('display', 'table-row');
                                $('.rltll').css('display', 'none');

                            } else {
                                // console.log('change is not checked!');
                                $('.rltll').css('display', 'table-row');

                                related_to_defaultgateway.css('display', 'none');

                            }
                        });

                    $('#drc_lwp_settings_enable_timer_on_sending_sms').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#drc_phone_number_ccode").chosen();
                                related_to_entimer.css('display', 'table-row');

                            } else {
                                // console.log('change is not checked!');
                                related_to_entimer.css('display', 'none');

                            }
                        });
                    //
                    $('#drc_default_gateways').on('change', function (e) {
                        // console.log('event fired');
                        if (this.value == "custom" && edf3.is(':checked')) {

                            related_to_customgateway.css('display', 'table-row');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');
                            related_to_custom.css('display', 'table-row');


                        }
                        else if (this.value == "firebase" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'table-row');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');
                            related_to_custom.css('display', 'none');


                        }
                        else {

                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');
                            related_to_custom.css('display', 'none');


                        }
                    });
                    drc_body.on('click', '.loiuyt',
                        function () {

                            $.ajax({
                                type: "GET",
                                url: ajaxurl,
                                data: {action: 'drc_lwp_check_credit'}
                            }).done(function (msg) {
                                var arr = JSON.parse(msg);
                                // console.log(arr);
                                $('.creditor .cp').html('<?php _e('Your Credit:', 'login-with-phone-number') ?>' + ' ' + arr['credit'])


                            });

                        });
                    drc_body.on('click', '.refreshShop',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            if (lwp_token) {
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {action: 'drc_lwp_get_shop'}
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        if (arr && arr.products) {
                                            $('.chargeAccount').empty();
                                            for (var j = 0; j < arr.products.length; j++) {
                                                $('.chargeAccount').append('<div class="col-lg-2 col-md-4 col-sm-6">' +
                                                    '<div class="lwp-produ-wrap">' +
                                                    '<div class="lwp-shop-title">' +
                                                    arr.products[j].title + ' ' +
                                                    '</div>' +
                                                    '<div class="lwp-shop-price">' +
                                                    arr.products[j].price +
                                                    '</div>' +
                                                    '<div class="lwp-shop-buy">' +
                                                    '<a target="_blank" href="' + arr.products[j].buy + lwp_token + '/' + arr.products[j].ID + '">' + '<?php _e("Buy", 'login-with-phone-number'); ?>' + '</a>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>'
                                                )

                                            }
                                        }
                                    }

                                });
                            }

                        });
                    drc_body.on('click', '.auth',
                        function () {
                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var drc_phone_number_ccode = $('#drc_phone_number_ccode').val();
                            drc_phone_number_ccodeG = drc_phone_number_ccode;
                            // alert(drc_phone_number_ccode);
                            // return;
                            if (lwp_phone_number) {
                                lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                $('#lwp_phone_number').css('display', 'none');
                                $('#lwp_secod').css('display', 'inherit');
                                $('.i34').css('display', 'inline-block');
                                $('.i35').css('display', 'none');
                                $('.drc_phone_number_ccode_wrap').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                lwp_phone_number = drc_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'drc_lwp_auth_customer',
                                        phone_number: lwp_phone_number,
                                        country_code: drc_phone_number_ccode
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                    }
                                    // $('form#iuytfrdghj').submit();

                                });

                            }
                        });

                    drc_body.on('click', '.authwithwebsite',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            // if(!lwp_token) {
                            var lwp_website_url = $('#lwp_website_url').val();
                            if (lwp_website_url) {
                                // lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                // $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                // $('#lwp_phone_number').css('display', 'none');
                                // $('#lwp_secod').css('display', 'inherit');
                                // $('.i34').css('display', 'inline-block');
                                // $('.i35').css('display', 'none');
                                // $('.drc_phone_number_ccode_wrap').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                // lwp_phone_number = drc_phone_number_ccode + lwp_phone_number;
                                $('.lwp_website_label').fadeOut();

                                setTimeout(() => {
                                    $('.lwploadr').fadeOut();

                                }, 2000)
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'drc_lwp_auth_customer_with_website',
                                        url: lwp_website_url
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                        if (arr && arr['success']) {
                                            if (arr['token']) {
                                                $('#lwp_token').val(arr['token']);
                                                setTimeout(() => {
                                                    $('form#iuytfrdghj').submit();

                                                }, 500)
                                            }
                                        } else {
                                            if (arr['err'] && arr['err']['response'] && arr['err']['response']['request'] && arr['err']['response']['request']['uri'] && arr['err']['response']['request']['uri']['host'] === 'localhost') {
                                                $('.lwpmaintextloader').html('authentication on localhost not accepted. please use with your domain!');

                                            }

                                        }
                                    }

                                    // $('form#iuytfrdghj').submit();

                                });
                                // .((e)=>{
                                //     console.log('e',e);
                                // });

                            }
                            // }
                        });
                    drc_body.on('click', '.lwpchangePhoneNumber',
                        function (e) {
                            e.preventDefault();
                            $('.lwp_phone_number_label').removeClass('none');
                            $('#lwp_phone_number').focus();
                            // $("#drc_phone_number_ccode").chosen();

                        });
                    drc_body.on('click', '.lwp_more_help', function () {
                        createTutorial();
                    });
                    drc_body.on('click', '.lwp_close , .lwp_button', function (e) {
                        e.preventDefault();
                        $('.lwp_modal').remove();
                        $('.lwp_modal_overlay').remove();
                        localStorage.setItem('ldwtutshow', 1);
                    });
                    drc_body.on('click', '.activate',
                        function () {

                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var lwp_secod = $('#lwp_secod').val();
                            var drc_phone_number_ccode = $('#drc_phone_number_ccode').val();

                            if (lwp_phone_number && lwp_secod && drc_phone_number_ccode) {
                                lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                lwp_phone_number = drc_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'drc_lwp_activate_customer', phone_number: lwp_phone_number,
                                        secod: lwp_secod
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                        if (arr['token']) {
                                            $('#lwp_token').val(arr['token']);
                                            //
                                            // drc_country_codes.val([drc_phone_number_ccodeG]); // Select the option with a value of '1'
                                            // drc_country_codes.trigger('change');

                                            // $('#drc_country_codes').val(arr['token']);
                                            setTimeout(() => {
                                                $('form#iuytfrdghj').submit();

                                            }, 500)
                                        }
                                    }
                                });

                            }
                        });
                    var ldwtutshow = localStorage.getItem('ldwtutshow');
                    if (ldwtutshow === null) {
                        // localStorage.setItem('ldwtutshow', 1);
                        // Show popup here
                        // $('#myModal').modal('show');
                        // console.log('set here');
                        createTutorial();
                    }

                    function createTutorial() {
                        var wrap = $('.wrap');
                        wrap.prepend('<div class="lwp_modal_overlay"></div>')
                            .prepend('<div class="lwp_modal">' +
                                '<div class="lwp_modal_header">' +
                                '<div class="lwp_l"></div>' +
                                '<div class="lwp_r"><button class="lwp_close">x</button></div>' +
                                '</div>' +
                                '<div class="lwp_modal_body">' +
                                '<ul>' +
                                '<li>' + '<?php _e("1. create a page and name it login or register or what ever", 'login-with-phone-number') ?>' + '</li>' +
                                '<li>' + '<?php _e("2. copy this shortcode <code>[drc_lwp]</code> and paste in the page you created at step 1", 'login-with-phone-number') ?>' + '</li>' +
                                '<li>' + '<?php _e("3. now, that is your login page. check your login page with other device or browser that you are not logged in!", 'login-with-phone-number') ?>' +
                                '</li>' +
                                '<li>' +
                                '<?php _e("for more information visit: ", 'login-with-phone-number') ?>' + '<a target="_blank" href="https://drc.com/product/login-with-phone-number-in-wordpress/?lang=en">drc</a>' +
                                '</li>' +
                                '</ul>' +
                                '</div>' +
                                '<div class="lwp_modal_footer">' +
                                '<button class="lwp_button"><?php _e("got it ", 'login-with-phone-number') ?></button>' +
                                '</div>' +
                                '</div>');

                    }
                });
            </script>
        </div>
        <?php
    }

    function lwp_custom_css()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_status'])) $options['drc_styles_status'] = '1';

        //first button
        if (!isset($options['drc_styles_button_background'])) $options['drc_styles_button_background'] = '#009b9a';
        if (!isset($options['drc_styles_button_border_color'])) $options['drc_styles_button_border_color'] = '#009b9a';
        if (!isset($options['drc_styles_button_text_color'])) $options['drc_styles_button_text_color'] = '#ffffff';
        if (!isset($options['drc_styles_button_border_radius'])) $options['drc_styles_button_border_radius'] = 'inherit';
        if (!isset($options['drc_styles_button_border_width'])) $options['drc_styles_button_border_width'] = 'inherit';

        //secondary button
        if (!isset($options['drc_styles_button_background2'])) $options['drc_styles_button_background2'] = '#009b9a';
        if (!isset($options['drc_styles_button_border_color2'])) $options['drc_styles_button_border_color2'] = '#009b9a';
        if (!isset($options['drc_styles_button_text_color2'])) $options['drc_styles_button_text_color2'] = '#ffffff';
        if (!isset($options['drc_styles_button_border_radius2'])) $options['drc_styles_button_border_radius2'] = 'inherit';
        if (!isset($options['drc_styles_button_border_width2'])) $options['drc_styles_button_border_width2'] = 'inherit';

        //input
        if (!isset($options['drc_styles_input_background'])) $options['drc_styles_input_background'] = 'inherit';
        if (!isset($options['drc_styles_input_border_color'])) $options['drc_styles_input_border_color'] = '#009b9a';
        if (!isset($options['drc_styles_input_text_color'])) $options['drc_styles_input_text_color'] = '#000000';
        if (!isset($options['drc_styles_input_placeholder_color'])) $options['drc_styles_input_placeholder_color'] = '#000000';
        if (!isset($options['drc_styles_input_border_radius'])) $options['drc_styles_input_border_radius'] = 'inherit';
        if (!isset($options['drc_styles_input_border_width'])) $options['drc_styles_input_border_width'] = '1px';

        //box
        if (!isset($options['drc_styles_box_background_color'])) $options['drc_styles_box_background_color'] = '#ffffff';

        //Labels
        if (!isset($options['drc_styles_labels_text_color'])) $options['drc_styles_labels_text_color'] = '#000000';
        if (!isset($options['drc_styles_labels_font_size'])) $options['drc_styles_labels_font_size'] = 'inherit';

        //title
        if (!isset($options['drc_styles_title_color'])) $options['drc_styles_title_color'] = '#000000';
        if (!isset($options['drc_styles_title_font_size'])) $options['drc_styles_title_font_size'] = 'inherit';

    }

    function style_settings_page()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        if (!isset($options['drc_online_support'])) $options['drc_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Style settings', 'login-with-phone-number'); ?></h2>
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                ?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php _e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('drc-lwp-styles'); ?>
                <?php do_settings_sections('drc-lwp-styles'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'login-with-phone-number'); ?>"/></p>

            </form>


        </div>
        <?php
    }

    function localization_settings_page()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        if (!isset($options['drc_online_support'])) $options['drc_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Localization settings', 'login-with-phone-number'); ?></h2>
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                ?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php _e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('drc-lwp-localization'); ?>
                <?php do_settings_sections('drc-lwp-localization'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'login-with-phone-number'); ?>"/></p>

            </form>


        </div>
        <?php
    }

    function section_intro()
    {
        ?>

        <?php

    }

    function section_title()
    {
        ?>
        <!--        jhgjk-->

        <?php

    }

    function setting_drc_lwp_space()
    {
        echo '<div class="drc_lwp_mgt50"></div>';
    }

    function setting_drc_email_login()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_email_login'])) $options['drc_email_login'] = '1';
        $display = 'inherit';
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="drc_lwp_settings[drc_email_login]" value="0" />
		<label><input type="checkbox" name="drc_lwp_settings[drc_email_login]" value="1"' . (($options['drc_email_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with email', 'login-with-phone-number') . '</label>';

    }

    function setting_drc_style_enable_custom_style()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_status'])) $options['drc_styles_status'] = '1';
        else $options['drc_styles_status'] = sanitize_text_field($options['drc_styles_status']);

        echo '<input  type="hidden" name="drc_lwp_settings_styles[drc_styles_status]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_drc_styles_status" name="drc_lwp_settings_styles[drc_styles_status]" value="1"' . (($options['drc_styles_status']) ? ' checked="checked"' : '') . ' />' . __('enable custom styles', 'login-with-phone-number') . '</label>';

    }


    function setting_drc_style_button_background_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_background'])) $options['drc_styles_button_background'] = '#009b9a';
        else $options['drc_styles_button_background'] = sanitize_text_field($options['drc_styles_button_background']);


        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_background]" class="regular-text" value="' . esc_attr($options['drc_styles_button_background']) . '" />
		<p class="description">' . __('button background color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_color'])) $options['drc_styles_button_border_color'] = '#009b9a';
        else $options['drc_styles_button_border_color'] = sanitize_text_field($options['drc_styles_button_border_color']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_border_color]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_color']) . '" />
		<p class="description">' . __('button border color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_radius()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_radius'])) $options['drc_styles_button_border_radius'] = 'inherit';
        else $options['drc_styles_button_border_radius'] = sanitize_text_field($options['drc_styles_button_border_radius']);

        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_button_border_radius]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_radius']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_width()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_width'])) $options['drc_styles_button_border_width'] = 'inherit';
        else $options['drc_styles_button_border_width'] = sanitize_text_field($options['drc_styles_button_border_width']);

        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_button_border_width]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_width']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_text_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_text_color'])) $options['drc_styles_button_text_color'] = '#ffffff';
        else $options['drc_styles_button_text_color'] = sanitize_text_field($options['drc_styles_button_text_color']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_text_color]" class="regular-text" value="' . esc_attr($options['drc_styles_button_text_color']) . '" />
		<p class="description">' . __('button text color', 'login-with-phone-number') . '</p>';
    }


    function setting_drc_style_button_background_color2()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_background2'])) $options['drc_styles_button_background2'] = '#009b9a';
        else $options['drc_styles_button_background2'] = sanitize_text_field($options['drc_styles_button_background2']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_background2]" class="regular-text" value="' . esc_attr($options['drc_styles_button_background2']) . '" />
		<p class="description">' . __('secondary button background color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_color2()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_color2'])) $options['drc_styles_button_border_color2'] = '#009b9a';
        else $options['drc_styles_button_border_color2'] = sanitize_text_field($options['drc_styles_button_border_color2']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_border_color2]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_color2']) . '" />
		<p class="description">' . __('secondary button border color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_radius2()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_radius2'])) $options['drc_styles_button_border_radius2'] = 'inherit';
        else $options['drc_styles_button_border_radius2'] = sanitize_text_field($options['drc_styles_button_border_radius2']);

        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_button_border_radius2]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_radius2']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_border_width2()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_border_width2'])) $options['drc_styles_button_border_width2'] = 'inherit';
        else $options['drc_styles_button_border_width2'] = sanitize_text_field($options['drc_styles_button_border_width2']);
        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_button_border_width2]" class="regular-text" value="' . esc_attr($options['drc_styles_button_border_width2']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_button_text_color2()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_button_text_color2'])) $options['drc_styles_button_text_color2'] = '#ffffff';
        else $options['drc_styles_button_text_color2'] = sanitize_text_field($options['drc_styles_button_text_color2']);
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_button_text_color2]" class="regular-text" value="' . esc_attr($options['drc_styles_button_text_color2']) . '" />
		<p class="description">' . __('secondary button text color', 'login-with-phone-number') . '</p>';
    }


    function setting_drc_style_input_background_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_background'])) $options['drc_styles_input_background'] = '#009b9a';
        else $options['drc_styles_input_background'] = sanitize_text_field($options['drc_styles_input_background']);
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_input_background]" class="regular-text" value="' . esc_attr($options['drc_styles_input_background']) . '" />
		<p class="description">' . __('input background color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_input_border_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_border_color'])) $options['drc_styles_input_border_color'] = '#009b9a';
        else $options['drc_styles_input_border_color'] = sanitize_text_field($options['drc_styles_input_border_color']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_input_border_color]" class="regular-text" value="' . esc_attr($options['drc_styles_input_border_color']) . '" />
		<p class="description">' . __('input border color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_input_border_radius()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_border_radius'])) $options['drc_styles_input_border_radius'] = 'inherit';
        else $options['drc_styles_input_border_radius'] = sanitize_text_field($options['drc_styles_input_border_radius']);
        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_input_border_radius]" class="regular-text" value="' . esc_attr($options['drc_styles_input_border_radius']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_input_border_width()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_border_width'])) $options['drc_styles_input_border_width'] = '1px';
        else $options['drc_styles_input_border_width'] = sanitize_text_field($options['drc_styles_input_border_width']);

        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_input_border_width]" class="regular-text" value="' . esc_attr($options['drc_styles_input_border_width']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_input_text_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_text_color'])) $options['drc_styles_input_text_color'] = '#000000';
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_input_text_color]" class="regular-text" value="' . esc_attr($options['drc_styles_input_text_color']) . '" />
		<p class="description">' . __('input text color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_input_placeholder_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_input_placeholder_color'])) $options['drc_styles_input_placeholder_color'] = '#000000';
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_input_placeholder_color]" class="regular-text" value="' . esc_attr($options['drc_styles_input_placeholder_color']) . '" />
		<p class="description">' . __('input placeholder color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_box_background_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_box_background_color'])) $options['drc_styles_box_background_color'] = '#ffffff';
        else $options['drc_styles_box_background_color'] = sanitize_text_field($options['drc_styles_box_background_color']);
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_box_background_color]" class="regular-text" value="' . esc_attr($options['drc_styles_box_background_color']) . '" />
		<p class="description">' . __('box background color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_labels_font_size()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_labels_font_size'])) $options['drc_styles_labels_font_size'] = 'inherit';
        else $options['drc_styles_labels_font_size'] = sanitize_text_field($options['drc_styles_labels_font_size']);

        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_labels_font_size]" class="regular-text" value="' . esc_attr($options['drc_styles_labels_font_size']) . '" />
		<p class="description">' . __('13px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_labels_text_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_labels_text_color'])) $options['drc_styles_labels_text_color'] = '#000000';
        else $options['drc_styles_labels_text_color'] = sanitize_text_field($options['drc_styles_labels_text_color']);

        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_labels_text_color]" class="regular-text" value="' . esc_attr($options['drc_styles_labels_text_color']) . '" />
		<p class="description">' . __('label text color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_title_color()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_title_color'])) $options['drc_styles_title_color'] = '#000000';
        else $options['drc_styles_title_color'] = sanitize_text_field($options['drc_styles_title_color']);
        echo '<input type="color" name="drc_lwp_settings_styles[drc_styles_title_color]" class="regular-text" value="' . esc_attr($options['drc_styles_title_color']) . '" />
		<p class="description">' . __('label text color', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_style_title_font_size()
    {
        $options = get_option('drc_lwp_settings_styles');
        if (!isset($options['drc_styles_title_font_size'])) $options['drc_styles_title_font_size'] = 'inherit';
        else $options['drc_styles_title_font_size'] = sanitize_text_field($options['drc_styles_title_font_size']);
        echo '<input type="text" name="drc_lwp_settings_styles[drc_styles_title_font_size]" class="regular-text" value="' . esc_attr($options['drc_styles_title_font_size']) . '" />
		<p class="description">' . __('20px', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_localization_enable_custom_localization()
    {
        $options = get_option('drc_lwp_settings_localization');
        if (!isset($options['drc_localization_status'])) $options['drc_localization_status'] = '0';
        echo '<input  type="hidden" name="drc_lwp_settings_localization[drc_localization_status]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_localization_status" name="drc_lwp_settings_localization[drc_localization_status]" value="1"' . (($options['drc_localization_status']) ? ' checked="checked"' : '') . ' />' . __('enable localization', 'login-with-phone-number') . '</label>';

    }

    function setting_drc_localization_of_login_form()
    {
        $options = get_option('drc_lwp_settings_localization');
        if (!isset($options['drc_localization_title_of_login_form'])) $options['drc_localization_title_of_login_form'] = 'Login / register';
        else $options['drc_localization_title_of_login_form'] = sanitize_text_field($options['drc_localization_title_of_login_form']);


        echo '<input type="text" name="drc_lwp_settings_localization[drc_localization_title_of_login_form]" class="regular-text" value="' . esc_attr($options['drc_localization_title_of_login_form']) . '" />
		<p class="description">' . __('Login / register', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_localization_of_login_form_email()
    {
        $options = get_option('drc_lwp_settings_localization');
        if (!isset($options['drc_localization_title_of_login_form_email'])) $options['drc_localization_title_of_login_form_email'] = 'Login / register';
        else $options['drc_localization_title_of_login_form_email'] = sanitize_text_field($options['drc_localization_title_of_login_form_email']);


        echo '<input type="text" name="drc_lwp_settings_localization[drc_localization_title_of_login_form_email]" class="regular-text" value="' . esc_attr($options['drc_localization_title_of_login_form_email']) . '" />
		<p class="description">' . __('Login / register', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_localization_placeholder_of_phonenumber_field()
    {
        $options = get_option('drc_lwp_settings_localization');
        if (!isset($options['drc_localization_placeholder_of_phonenumber_field'])) $options['drc_localization_placeholder_of_phonenumber_field'] = '9*********';
        else $options['drc_localization_placeholder_of_phonenumber_field'] = sanitize_text_field($options['drc_localization_placeholder_of_phonenumber_field']);

        echo '<input type="text" name="drc_lwp_settings_localization[drc_localization_placeholder_of_phonenumber_field]" class="regular-text" value="' . esc_attr($options['drc_localization_placeholder_of_phonenumber_field']) . '" />
		<p class="description">' . __('9*********', 'login-with-phone-number') . '</p>';
    }

    function setting_drc_sms_login()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_sms_login'])) $options['drc_sms_login'] = '1';
        $display = 'inherit';
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="drc_lwp_settings[drc_sms_login]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_drc_sms_login" name="drc_lwp_settings[drc_sms_login]" value="1"' . (($options['drc_sms_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with phone number', 'login-with-phone-number') . '</label>';

    }

    function setting_drc_user_registration()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_user_registration'])) $options['drc_user_registration'] = '1';

        echo '<input type="hidden" name="drc_lwp_settings[drc_user_registration]" value="0" />
		<label><input type="checkbox" name="drc_lwp_settings[drc_user_registration]" value="1"' . (($options['drc_user_registration']) ? ' checked="checked"' : '') . ' />' . __('I want to enable registration', 'login-with-phone-number') . '</label>';

    }

    function setting_drc_password_login()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
        $display = 'inherit';
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<input type="hidden" name="drc_lwp_settings[drc_password_login]" value="0" />
		<label><input type="checkbox" name="drc_lwp_settings[drc_password_login]" value="1"' . (($options['drc_password_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with password too', 'login-with-phone-number') . '</label>';

    }

    function drc_position_form()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_position_form'])) $options['drc_position_form'] = '0';

        echo '<input type="hidden" name="drc_lwp_settings[drc_position_form]" value="0" />
		<label><input type="checkbox" name="drc_lwp_settings[drc_position_form]" value="1"' . (($options['drc_position_form']) ? ' checked="checked"' : '') . ' />' . __('I want form shows on page in fix position', 'login-with-phone-number') . '</label>';

    }

    function drc_online_support()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_online_support'])) $options['drc_online_support'] = '1';

        echo '<input type="hidden" name="drc_lwp_settings[drc_online_support]" value="0" />
		<label><input type="checkbox" name="drc_lwp_settings[drc_online_support]" value="1"' . (($options['drc_online_support']) ? ' checked="checked"' : '') . ' />' . __('I want online support be active', 'login-with-phone-number') . '</label>';

    }

    function setting_use_custom_gateway()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_use_custom_gateway'])) $options['drc_use_custom_gateway'] = '1';

        echo '<input type="hidden" name="drc_lwp_settings[drc_use_custom_gateway]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_use_custom_gateway" name="drc_lwp_settings[drc_use_custom_gateway]" value="1"' . (($options['drc_use_custom_gateway']) ? ' checked="checked"' : '') . ' />' . __('I want to use custom gateways', 'login-with-phone-number') . '</label>';

    }

    function setting_default_gateways()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_gateways'])) $options['drc_default_gateways'] = 'firebase';
        $gateways = [
            ["value" => "firebase", "label" => __("Firebase (Google)",'login-with-phone-number')],
            ["value" => "custom", "label" => __("Custom (Config Your Gateway)",'login-with-phone-number')],
        ];

        ?>
        <select name="drc_lwp_settings[drc_default_gateways]" id="drc_default_gateways">
            <?php
            foreach ($gateways as $gateway) {
                $rr = false;
//                if(is_array($options['drc_default_gateways']))
                if (($gateway["value"] == $options['drc_default_gateways'])) {
                    $rr = true;
                }
                echo '<option value="' . $gateway["value"] . '" ' . ($rr ? ' selected="selected"' : '') . '>' . $gateway['label'] . '</option>';
            }
            ?>
        </select>
        <?php

    }

    function setting_twilio_account_sid()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_twilio_account_sid'])) $options['drc_twilio_account_sid'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_twilio_account_sid]" class="regular-text" value="' . esc_attr($options['drc_twilio_account_sid']) . '" />
		<p class="description">' . __('enter your Twilio account SID', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_auth_token()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_twilio_auth_token'])) $options['drc_twilio_auth_token'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_twilio_auth_token]" class="regular-text" value="' . esc_attr($options['drc_twilio_auth_token']) . '" />
		<p class="description">' . __('enter your Twilio auth token', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_phone_number()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_twilio_phone_number'])) $options['drc_twilio_phone_number'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_twilio_phone_number]" class="regular-text" value="' . esc_attr($options['drc_twilio_phone_number']) . '" />
		<p class="description">' . __('enter your Twilio phone number', 'login-with-phone-number') . '</p>';
    }

    function setting_zenziva_user_key()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_zenziva_user_key'])) $options['drc_zenziva_user_key'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_zenziva_user_key]" class="regular-text" value="' . esc_attr($options['drc_zenziva_user_key']) . '" />
		<p class="description">' . __('enter your Zenziva user key', 'login-with-phone-number') . '</p>';
    }

    function setting_zenziva_pass_key()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_zenziva_pass_key'])) $options['drc_zenziva_pass_key'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_zenziva_pass_key]" class="regular-text" value="' . esc_attr($options['drc_zenziva_pass_key']) . '" />
		<p class="description">' . __('enter your Zenziva pass key', 'login-with-phone-number') . '</p>';
    }

    function setting_infobip_user()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_infobip_user'])) $options['drc_infobip_user'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_infobip_user]" class="regular-text" value="' . esc_attr($options['drc_infobip_user']) . '" />
		<p class="description">' . __('enter your Infobip pass key', 'login-with-phone-number') . '</p>';
    }


    function setting_infobip_password()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_infobip_password'])) $options['drc_infobip_password'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_infobip_password]" class="regular-text" value="' . esc_attr($options['drc_infobip_password']) . '" />
		<p class="description">' . __('enter your Infobip pass key', 'login-with-phone-number') . '</p>';
    }

    function setting_infobip_sender()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_infobip_sender'])) $options['drc_infobip_sender'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_infobip_sender]" class="regular-text" value="' . esc_attr($options['drc_infobip_sender']) . '" />
		<p class="description">' . __('enter your Infobip sender', 'login-with-phone-number') . '</p>';
    }


    function setting_firebase_api()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_firebase_api'])) $options['drc_firebase_api'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_firebase_api]" class="regular-text" value="' . esc_attr($options['drc_firebase_api']) . '" />
		<p class="description">' . __('enter Firebase api', 'login-with-phone-number') . ' - <a  href="https://drc.com/support/login-with-phone-number-wordpress/send-10000-sms-free-with-firebase-in-plugin-login-with-phone-number-wordpress/" target="_blank">' . __('Firebase config help - documentation', 'login-with-phone-number') . '</a></p>';
    }

    function setting_firebase_config()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_firebase_config'])) $options['drc_firebase_config'] = '';
        else $options['drc_firebase_config'] = sanitize_textarea_field($options['drc_firebase_config']);

        echo '<textarea name="drc_lwp_settings[drc_firebase_config]" class="regular-text">' . esc_attr($options['drc_firebase_config']) . '</textarea>
		<p class="description">' . __('enter Firebase config', 'login-with-phone-number') . '</p>';
    }

    function setting_custom_api_url()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_custom_api_url'])) $options['drc_custom_api_url'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_custom_api_url]" class="regular-text" value="' . esc_attr($options['drc_custom_api_url']) . '" />
		<p class="description">' . __('enter custom url', 'login-with-phone-number') . ' - <a  href="https://drc.com/support/login-with-phone-number-wordpress/send-10000-sms-free-with-firebase-in-plugin-login-with-phone-number-wordpress/" target="_blank">' . __('Custom config help - documentation', 'login-with-phone-number') . '</a></p>';
    }

    function setting_custom_api_method()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_custom_api_method'])) $options['drc_custom_api_method'] = '';
        else $options['drc_custom_api_method'] = sanitize_textarea_field($options['drc_custom_api_method']);
//        print_r($options['drc_custom_api_method']);
        ?>
        <select name="drc_lwp_settings[drc_custom_api_method]" id="drc_custom_api_method">
            <?php
            foreach (['GET', 'POST'] as $gateway) {
                $rr = false;
//                if(is_array($options['drc_default_gateways']))
                if (($gateway == $options['drc_custom_api_method'])) {
                    $rr = true;
                }
                echo '<option value="' . esc_attr($gateway) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($gateway) . '</option>';
            }
            ?>
        </select>
        <?php
        echo '<p class="description">' . __('enter request method', 'login-with-phone-number') . '</p>';
    }

    function setting_custom_api_header()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_custom_api_header'])) $options['drc_custom_api_header'] = '';
        else $options['drc_custom_api_header'] = sanitize_textarea_field($options['drc_custom_api_header']);

        echo '<textarea name="drc_lwp_settings[drc_custom_api_header]" class="regular-text">' . esc_attr($options['drc_custom_api_header']) . '</textarea>
		<p class="description">' . __('enter header of request in json', 'login-with-phone-number') . '</p>';
    }


    function setting_custom_api_body()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_custom_api_body'])) $options['drc_custom_api_body'] = '';
        else $options['drc_custom_api_body'] = sanitize_textarea_field($options['drc_custom_api_body']);

        echo '<textarea name="drc_lwp_settings[drc_custom_api_body]" class="regular-text">' . esc_attr($options['drc_custom_api_body']) . '</textarea>
		<p class="description">' . __('enter body of request in json', 'login-with-phone-number') . '</p>';
    }

    function setting_custom_api_smstext()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_custom_api_smstext'])) $options['drc_custom_api_smstext'] = '';
        else $options['drc_custom_api_smstext'] = sanitize_textarea_field($options['drc_custom_api_smstext']);

        echo '<textarea name="drc_lwp_settings[drc_custom_api_smstext]" class="regular-text">' . esc_attr($options['drc_custom_api_smstext']) . '</textarea>
		<p class="description">' . __('enter smstext , you can use ${code}', 'login-with-phone-number') . '</p>';
    }

    function setting_raygansms_username()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_raygansms_username'])) $options['drc_raygansms_username'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_raygansms_username]" class="regular-text" value="' . esc_attr($options['drc_raygansms_username']) . '" />
		<p class="description">' . __('enter your Raygansms username', 'login-with-phone-number') . '</p>';
    }

    function setting_raygansms_password()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_raygansms_password'])) $options['drc_raygansms_password'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_raygansms_password]" class="regular-text" value="' . esc_attr($options['drc_raygansms_password']) . '" />
		<p class="description">' . __('enter your Raygansms password', 'login-with-phone-number') . '</p>';
    }

    function setting_raygansms_phonenumber()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_raygansms_phonenumber'])) $options['drc_raygansms_phonenumber'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_raygansms_phonenumber]" class="regular-text" value="' . esc_attr($options['drc_raygansms_phonenumber']) . '" />
		<p class="description">' . __('enter your Raygansms phone number', 'login-with-phone-number') . '</p>';
    }


    function setting_smsbharti_api_key()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_smsbharti_api_key'])) $options['drc_smsbharti_api_key'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_smsbharti_api_key]" class="regular-text" value="' . esc_attr($options['drc_smsbharti_api_key']) . '" />
		<p class="description">' . __('enter your smsbharti api key', 'login-with-phone-number') . '</p>';
    }

    function setting_smsbharti_from()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_smsbharti_from'])) $options['drc_smsbharti_from'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_smsbharti_from]" class="regular-text" value="' . esc_attr($options['drc_smsbharti_from']) . '" />
		<p class="description">' . __('enter your smsbharti from', 'login-with-phone-number') . '</p>';
    }

    function setting_smsbharti_template_id()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_smsbharti_template_id'])) $options['drc_smsbharti_template_id'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_smsbharti_template_id]" class="regular-text" value="' . esc_attr($options['drc_smsbharti_template_id']) . '" />
		<p class="description">' . __('enter your smsbharti template id', 'login-with-phone-number') . '</p>';
    }

    function setting_smsbharti_routeid()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_smsbharti_routeid'])) $options['drc_smsbharti_routeid'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_smsbharti_routeid]" class="regular-text" value="' . esc_attr($options['drc_smsbharti_routeid']) . '" />
		<p class="description">' . __('enter your smsbharti route id', 'login-with-phone-number') . '</p>';
    }


    function setting_mshastra_user()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_mshastra_user'])) $options['drc_mshastra_user'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_mshastra_user]" class="regular-text" value="' . esc_attr($options['drc_mshastra_user']) . '" />
		<p class="description">' . __('enter your mshastra username', 'login-with-phone-number') . '</p>';
    }

    function setting_mshastra_pwd()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_mshastra_pwd'])) $options['drc_mshastra_pwd'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_mshastra_pwd]" class="regular-text" value="' . esc_attr($options['drc_mshastra_pwd']) . '" />
		<p class="description">' . __('enter your mshastra password', 'login-with-phone-number') . '</p>';
    }

    function setting_mshastra_senderid()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_mshastra_senderid'])) $options['drc_mshastra_senderid'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_mshastra_senderid]" class="regular-text" value="' . esc_attr($options['drc_mshastra_senderid']) . '" />
		<p class="description">' . __('enter your mshastra sender ID', 'login-with-phone-number') . '</p>';
    }
//    function setting_custom_gateway_url()
//    {
//
//        $options = get_option('drc_lwp_settings');
//        if (!isset($options['drc_custom_gateway_url'])) $options['drc_custom_gateway_url'] = '';
//
//        echo '<input type="text" name="drc_lwp_settings[drc_custom_gateway_url]" class="regular-text" value="' . esc_attr($options['drc_custom_gateway_url']) . '" />
//		<p class="description">' . __('enter your sms gateway url', 'login-with-phone-number') . '</p>';
//    }
//    function setting_custom_gateway_username()
//    {
//
//        $options = get_option('drc_lwp_settings');
//        if (!isset($options['drc_custom_gateway_username'])) $options['drc_custom_gateway_username'] = '';
//
//        echo '<input type="text" name="drc_lwp_settings[drc_custom_gateway_username]" class="regular-text" value="' . esc_attr($options['drc_custom_gateway_username']) . '" />
//		<p class="description">' . __('enter your sms gateway username', 'login-with-phone-number') . '</p>';
//    }
//
//    function setting_custom_gateway_password()
//    {
//
//        $options = get_option('drc_lwp_settings');
//        if (!isset($options['drc_custom_gateway_password'])) $options['drc_custom_gateway_password'] = '';
//
//        echo '<input type="text" name="drc_lwp_settings[drc_custom_gateway_password]" class="regular-text" value="' . esc_attr($options['drc_custom_gateway_password']) . '" />
//		<p class="description">' . __('enter your sms gateway password', 'login-with-phone-number') . '</p>';
//    }


    function setting_taqnyat_sender_number()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_taqnyat_sendernumber'])) $options['drc_taqnyat_sendernumber'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_taqnyat_sendernumber]" class="regular-text" value="' . esc_attr($options['drc_taqnyat_sendernumber']) . '" />
		<p class="description">' . __('enter your taqnyat sender number', 'login-with-phone-number') . '</p>';
    }

    function setting_taqnyat_api_key()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_taqnyat_api_key'])) $options['drc_taqnyat_api_key'] = '';

        echo '<input type="text" name="drc_lwp_settings[drc_taqnyat_api_key]" class="regular-text" value="' . esc_attr($options['drc_taqnyat_api_key']) . '" />
		<p class="description">' . __('enter your taqnyat api key', 'login-with-phone-number') . '</p>';
    }

    function drc_use_phone_number_for_username()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_use_phone_number_for_username'])) $options['drc_use_phone_number_for_username'] = '0';

        echo '<input type="hidden" name="drc_lwp_settings[drc_use_phone_number_for_username]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_use_phone_number_for_username" name="drc_lwp_settings[drc_use_phone_number_for_username]" value="1"' . (($options['drc_use_phone_number_for_username']) ? ' checked="checked"' : '') . ' />' . __('I want to set phone number as username and nickname', 'login-with-phone-number') . '</label>';

    }

    function drc_enable_timer_on_sending_sms()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_enable_timer_on_sending_sms'])) $options['drc_enable_timer_on_sending_sms'] = '1';

        echo '<input type="hidden" name="drc_lwp_settings[drc_enable_timer_on_sending_sms]" value="0" />
		<label><input type="checkbox" id="drc_lwp_settings_enable_timer_on_sending_sms" name="drc_lwp_settings[drc_enable_timer_on_sending_sms]" value="1"' . (($options['drc_enable_timer_on_sending_sms']) ? ' checked="checked"' : '') . ' />' . __('I want to enable timer after user entered phone number and clicked on submit', 'login-with-phone-number') . '</label>';

    }


    function setting_timer_count()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_timer_count'])) $options['drc_timer_count'] = '60';


        echo '<input id="lwp_timer_count" type="text" name="drc_lwp_settings[drc_timer_count]" class="regular-text" value="' . esc_attr($options['drc_timer_count']) . '" />
		<p class="description">' . __('Timer count', 'login-with-phone-number') . '</p>';

    }

    function drc_enable_accept_term_and_conditions()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_enable_accept_terms_and_condition'])) $options['drc_enable_accept_terms_and_condition'] = '1';

        echo '<input type="hidden" name="drc_lwp_settings[drc_enable_accept_terms_and_condition]" value="0" />
		<label><input type="checkbox" id="drc_enable_accept_terms_and_condition" name="drc_lwp_settings[drc_enable_accept_terms_and_condition]" value="1"' . (($options['drc_enable_accept_terms_and_condition']) ? ' checked="checked"' : '') . ' />' . __('I want to show some terms & conditions for user to accept it, when he/she wants to register ', 'login-with-phone-number') . '</label>';

    }

    function setting_term_and_conditions_text()
    {

        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_term_and_conditions_text'])) $options['drc_term_and_conditions_text'] = __('By submitting, you agree to the <a href="#">Terms and Privacy Policy</a>','login-with-phone-number');
        else $options['drc_term_and_conditions_text'] = ($options['drc_term_and_conditions_text']);
        echo '<textarea name="drc_lwp_settings[drc_term_and_conditions_text]" class="regular-text">' . esc_attr($options['drc_term_and_conditions_text']) . '</textarea>
		<p class="description">' . __('enter term and condition accepting text', 'login-with-phone-number') . '</p>';
    }

    function credit_adminbar()
    {
        global $wp_admin_bar, $melipayamak;
        if (!is_super_admin() || !is_admin_bar_showing())
            return;

        $credit = '0';
        ?>

 <?php
    }

    function setting_drc_phone_number()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!isset($options['drc_phone_number_ccode'])) $options['drc_phone_number_ccode'] = '';
        ?>
        <div class="drc_phone_number_ccode_wrap">
            <select name="drc_lwp_settings[drc_phone_number_ccode]" id="drc_phone_number_ccode"
                    data-placeholder="<?php _e('Choose a country...', 'login-with-phone-number'); ?>">
                <?php
                $country_codes = $this->get_country_code_options();

                foreach ($country_codes as $country) {
                    echo '<option value="' . esc_attr($country["value"]) . '" ' . (($options['drc_phone_number_ccode'] == $country["value"]) ? ' selected="selected"' : '') . ' >+' . esc_html($country['value']) . ' - ' . esc_html($country["code"]) . '</option>';
                }
                ?>
            </select>
            <?php
            echo '<input placeholder="Ex: 9120539945" type="text" name="drc_lwp_settings[drc_phone_number]" id="lwp_phone_number" class="regular-text" value="' . esc_attr($options['drc_phone_number']) . '" />';
            ?>
        </div>
        <?php
        echo '<input type="text" name="drc_lwp_settings[drc_secod]" id="lwp_secod" class="regular-text" style="display:none" value="" placeholder="_ _ _ _ _ _" />';
        ?>
        <button type="button" class="button-primary auth i35"
                value="<?php _e('Authenticate', 'login-with-phone-number'); ?>"><?php _e('activate sms login', 'login-with-phone-number'); ?></button>
        <button type="button" class="button-primary activate i34" style="display: none"
                value="<?php _e('Activate', 'login-with-phone-number'); ?>"><?php _e('activate account', 'login-with-phone-number'); ?></button>

        <?php
    }

    function setting_drc_website_url()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_website_url'])) $options['drc_website_url'] = $this->settings_get_site_url();
        ?>
        <div class="drc_website_url_wrap">
            <?php
            echo '<input placeholder="Ex: example.com" type="text" name="drc_lwp_settings[drc_website_url]" id="lwp_website_url" class="regular-text" value="' . esc_attr($options['drc_website_url']) . '" />';
            ?>
        </div>

        <button type="button" class="button-primary authwithwebsite i35"
                value="<?php _e('Authenticate', 'login-with-phone-number'); ?>"><?php _e('activate sms login', 'login-with-phone-number'); ?></button>

        <?php
    }

    function setting_drc_token()
    {
        $options = get_option('drc_lwp_settings');
        $display = 'inherit';
        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="drc_lwp_settings[drc_token]" class="regular-text" value="' . esc_attr($options['drc_token']) . '" />
		<p class="description">' . __('enter api key', 'login-with-phone-number') . '</p>';

    }

    function settings_get_site_url()
    {
        $url = get_site_url();
        $disallowed = array('http://', 'https://', 'https://www.', 'http://www.', 'www.');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;

    }

    function setting_drc_url_redirect()
    {
        $options = get_option('drc_lwp_settings');
        $display = 'inherit';
        if (!isset($options['drc_redirect_url'])) $options['drc_redirect_url'] = '';
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="drc_lwp_settings[drc_redirect_url]" class="regular-text" value="' . esc_attr($options['drc_redirect_url']) . '" />
		<p class="description">' . __('enter redirect url', 'login-with-phone-number') . '</p>';

    }

    function setting_drc_login_message()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_login_message'])) $options['drc_login_message'] = 'Welcome, You are logged in...';
        echo '<input id="lwp_token" type="text" name="drc_lwp_settings[drc_login_message]" class="regular-text" value="' . esc_attr($options['drc_login_message']) . '" />
		<p class="description">' . __('enter login message', 'login-with-phone-number') . '</p>';

    }

    function get_country_code_options()
    {

        $json_countries = '[["Afghanistan (‫افغانستان‬‎)", "af", "93"], ["Albania (Shqipëri)", "al", "355"], ["Algeria (‫الجزائر‬‎)", "dz", "213"], ["American Samoa", "as", "1684"], ["Andorra", "ad", "376"], ["Angola", "ao", "244"], ["Anguilla", "ai", "1264"], ["Antigua and Barbuda", "ag", "1268"], ["Argentina", "ar", "54"], ["Armenia (Հայաստան)", "am", "374"], ["Aruba", "aw", "297"], ["Australia", "au", "61", 0], ["Austria (Österreich)", "at", "43"], ["Azerbaijan (Azərbaycan)", "az", "994"], ["Bahamas", "bs", "1242"], ["Bahrain (‫البحرين‬‎)", "bh", "973"], ["Bangladesh (বাংলাদেশ)", "bd", "880"], ["Barbados", "bb", "1246"], ["Belarus (Беларусь)", "by", "375"], ["Belgium (België)", "be", "32"], ["Belize", "bz", "501"], ["Benin (Bénin)", "bj", "229"], ["Bermuda", "bm", "1441"], ["Bhutan (འབྲུག)", "bt", "975"], ["Bolivia", "bo", "591"], ["Bosnia and Herzegovina (Босна и Херцеговина)", "ba", "387"], ["Botswana", "bw", "267"], ["Brazil (Brasil)", "br", "55"], ["British Indian Ocean Territory", "io", "246"], ["British Virgin Islands", "vg", "1284"], ["Brunei", "bn", "673"], ["Bulgaria (България)", "bg", "359"], ["Burkina Faso", "bf", "226"], ["Burundi (Uburundi)", "bi", "257"], ["Cambodia (កម្ពុជា)", "kh", "855"], ["Cameroon (Cameroun)", "cm", "237"], ["Canada", "ca", "1", 1, ["204", "226", "236", "249", "250", "289", "306", "343", "365", "387", "403", "416", "418", "431", "437", "438", "450", "506", "514", "519", "548", "579", "581", "587", "604", "613", "639", "647", "672", "705", "709", "742", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905"]], ["Cape Verde (Kabu Verdi)", "cv", "238"], ["Caribbean Netherlands", "bq", "599", 1], ["Cayman Islands", "ky", "1345"], ["Central African Republic (République centrafricaine)", "cf", "236"], ["Chad (Tchad)", "td", "235"], ["Chile", "cl", "56"], ["China (中国)", "cn", "86"], ["Christmas Island", "cx", "61", 2], ["Cocos (Keeling) Islands", "cc", "61", 1], ["Colombia", "co", "57"], ["Comoros (‫جزر القمر‬‎)", "km", "269"], ["Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)", "cd", "243"], ["Congo (Republic) (Congo-Brazzaville)", "cg", "242"], ["Cook Islands", "ck", "682"], ["Costa Rica", "cr", "506"], ["Côte d’Ivoire", "ci", "225"], ["Croatia (Hrvatska)", "hr", "385"], ["Cuba", "cu", "53"], ["Curaçao", "cw", "599", 0], ["Cyprus (Κύπρος)", "cy", "357"], ["Czech Republic (Česká republika)", "cz", "420"], ["Denmark (Danmark)", "dk", "45"], ["Djibouti", "dj", "253"], ["Dominica", "dm", "1767"], ["Dominican Republic (República Dominicana)", "do", "1", 2, ["809", "829", "849"]], ["Ecuador", "ec", "593"], ["Egypt (‫مصر‬‎)", "eg", "20"], ["El Salvador", "sv", "503"], ["Equatorial Guinea (Guinea Ecuatorial)", "gq", "240"], ["Eritrea", "er", "291"], ["Estonia (Eesti)", "ee", "372"], ["Ethiopia", "et", "251"], ["Falkland Islands (Islas Malvinas)", "fk", "500"], ["Faroe Islands (Føroyar)", "fo", "298"], ["Fiji", "fj", "679"], ["Finland (Suomi)", "fi", "358", 0], ["France", "fr", "33"], ["French Guiana (Guyane française)", "gf", "594"], ["French Polynesia (Polynésie française)", "pf", "689"], ["Gabon", "ga", "241"], ["Gambia", "gm", "220"], ["Georgia (საქართველო)", "ge", "995"], ["Germany (Deutschland)", "de", "49"], ["Ghana (Gaana)", "gh", "233"], ["Gibraltar", "gi", "350"], ["Greece (Ελλάδα)", "gr", "30"], ["Greenland (Kalaallit Nunaat)", "gl", "299"], ["Grenada", "gd", "1473"], ["Guadeloupe", "gp", "590", 0], ["Guam", "gu", "1671"], ["Guatemala", "gt", "502"], ["Guernsey", "gg", "44", 1], ["Guinea (Guinée)", "gn", "224"], ["Guinea-Bissau (Guiné Bissau)", "gw", "245"], ["Guyana", "gy", "592"], ["Haiti", "ht", "509"], ["Honduras", "hn", "504"], ["Hong Kong (香港)", "hk", "852"], ["Hungary (Magyarország)", "hu", "36"], ["Iceland (Ísland)", "is", "354"], ["India (भारत)", "in", "91"], ["Indonesia", "id", "62"], ["Iran (‫ایران‬‎)", "ir", "98"], ["Iraq (‫العراق‬‎)", "iq", "964"], ["Ireland", "ie", "353"], ["Isle of Man", "im", "44", 2], ["Israel (‫ישראל‬‎)", "il", "972"], ["Italy (Italia)", "it", "39", 0], ["Jamaica", "jm", "1", 4, ["876", "658"]], ["Japan (日本)", "jp", "81"], ["Jersey", "je", "44", 3], ["Jordan (‫الأردن‬‎)", "jo", "962"], ["Kazakhstan (Казахстан)", "kz", "7", 1], ["Kenya", "ke", "254"], ["Kiribati", "ki", "686"], ["Kosovo", "xk", "383"], ["Kuwait (‫الكويت‬‎)", "kw", "965"], ["Kyrgyzstan (Кыргызстан)", "kg", "996"], ["Laos (ລາວ)", "la", "856"], ["Latvia (Latvija)", "lv", "371"], ["Lebanon (‫لبنان‬‎)", "lb", "961"], ["Lesotho", "ls", "266"], ["Liberia", "lr", "231"], ["Libya (‫ليبيا‬‎)", "ly", "218"], ["Liechtenstein", "li", "423"], ["Lithuania (Lietuva)", "lt", "370"], ["Luxembourg", "lu", "352"], ["Macau (澳門)", "mo", "853"], ["Macedonia (FYROM) (Македонија)", "mk", "389"], ["Madagascar (Madagasikara)", "mg", "261"], ["Malawi", "mw", "265"], ["Malaysia", "my", "60"], ["Maldives", "mv", "960"], ["Mali", "ml", "223"], ["Malta", "mt", "356"], ["Marshall Islands", "mh", "692"], ["Martinique", "mq", "596"], ["Mauritania (‫موريتانيا‬‎)", "mr", "222"], ["Mauritius (Moris)", "mu", "230"], ["Mayotte", "yt", "262", 1], ["Mexico (México)", "mx", "52"], ["Micronesia", "fm", "691"], ["Moldova (Republica Moldova)", "md", "373"], ["Monaco", "mc", "377"], ["Mongolia (Монгол)", "mn", "976"], ["Montenegro (Crna Gora)", "me", "382"], ["Montserrat", "ms", "1664"], ["Morocco (‫المغرب‬‎)", "ma", "212", 0], ["Mozambique (Moçambique)", "mz", "258"], ["Myanmar (Burma) (မြန်မာ)", "mm", "95"], ["Namibia (Namibië)", "na", "264"], ["Nauru", "nr", "674"], ["Nepal (नेपाल)", "np", "977"], ["Netherlands (Nederland)", "nl", "31"], ["New Caledonia (Nouvelle-Calédonie)", "nc", "687"], ["New Zealand", "nz", "64"], ["Nicaragua", "ni", "505"], ["Niger (Nijar)", "ne", "227"], ["Nigeria", "ng", "234"], ["Niue", "nu", "683"], ["Norfolk Island", "nf", "672"], ["North Korea (조선 민주주의 인민 공화국)", "kp", "850"], ["Northern Mariana Islands", "mp", "1670"], ["Norway (Norge)", "no", "47", 0], ["Oman (‫عُمان‬‎)", "om", "968"], ["Pakistan (‫پاکستان‬‎)", "pk", "92"], ["Palau", "pw", "680"], ["Palestine (‫فلسطين‬‎)", "ps", "970"], ["Panama (Panamá)", "pa", "507"], ["Papua New Guinea", "pg", "675"], ["Paraguay", "py", "595"], ["Peru (Perú)", "pe", "51"], ["Philippines", "ph", "63"], ["Poland (Polska)", "pl", "48"], ["Portugal", "pt", "351"], ["Puerto Rico", "pr", "1", 3, ["787", "939"]], ["Qatar (‫قطر‬‎)", "qa", "974"], ["Réunion (La Réunion)", "re", "262", 0], ["Romania (România)", "ro", "40"], ["Russia (Россия)", "ru", "7", 0], ["Rwanda", "rw", "250"], ["Saint Barthélemy", "bl", "590", 1], ["Saint Helena", "sh", "290"], ["Saint Kitts and Nevis", "kn", "1869"], ["Saint Lucia", "lc", "1758"], ["Saint Martin (Saint-Martin (partie française))", "mf", "590", 2], ["Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "pm", "508"], ["Saint Vincent and the Grenadines", "vc", "1784"], ["Samoa", "ws", "685"], ["San Marino", "sm", "378"], ["São Tomé and Príncipe (São Tomé e Príncipe)", "st", "239"], ["Saudi Arabia (‫المملكة العربية السعودية‬‎)", "sa", "966"], ["Senegal (Sénégal)", "sn", "221"], ["Serbia (Србија)", "rs", "381"], ["Seychelles", "sc", "248"], ["Sierra Leone", "sl", "232"], ["Singapore", "sg", "65"], ["Sint Maarten", "sx", "1721"], ["Slovakia (Slovensko)", "sk", "421"], ["Slovenia (Slovenija)", "si", "386"], ["Solomon Islands", "sb", "677"], ["Somalia (Soomaaliya)", "so", "252"], ["South Africa", "za", "27"], ["South Korea (대한민국)", "kr", "82"], ["South Sudan (‫جنوب السودان‬‎)", "ss", "211"], ["Spain (España)", "es", "34"], ["Sri Lanka (ශ්‍රී ලංකාව)", "lk", "94"], ["Sudan (‫السودان‬‎)", "sd", "249"], ["Suriname", "sr", "597"], ["Svalbard and Jan Mayen", "sj", "47", 1], ["Swaziland", "sz", "268"], ["Sweden (Sverige)", "se", "46"], ["Switzerland (Schweiz)", "ch", "41"], ["Syria (‫سوريا‬‎)", "sy", "963"], ["Taiwan (台灣)", "tw", "886"], ["Tajikistan", "tj", "992"], ["Tanzania", "tz", "255"], ["Thailand (ไทย)", "th", "66"], ["Timor-Leste", "tl", "670"], ["Togo", "tg", "228"], ["Tokelau", "tk", "690"], ["Tonga", "to", "676"], ["Trinidad and Tobago", "tt", "1868"], ["Tunisia (‫تونس‬‎)", "tn", "216"], ["Turkey (Türkiye)", "tr", "90"], ["Turkmenistan", "tm", "993"], ["Turks and Caicos Islands", "tc", "1649"], ["Tuvalu", "tv", "688"], ["U.S. Virgin Islands", "vi", "1340"], ["Uganda", "ug", "256"], ["Ukraine (Україна)", "ua", "380"], ["United Arab Emirates (‫الإمارات العربية المتحدة‬‎)", "ae", "971"], ["United Kingdom", "gb", "44", 0], ["United States", "us", "1", 0], ["Uruguay", "uy", "598"], ["Uzbekistan (Oʻzbekiston)", "uz", "998"], ["Vanuatu", "vu", "678"], ["Vatican City (Città del Vaticano)", "va", "39", 1], ["Venezuela", "ve", "58"], ["Vietnam (Việt Nam)", "vn", "84"], ["Wallis and Futuna (Wallis-et-Futuna)", "wf", "681"], ["Western Sahara (‫الصحراء الغربية‬‎)", "eh", "212", 1], ["Yemen (‫اليمن‬‎)", "ye", "967"], ["Zambia", "zm", "260"], ["Zimbabwe", "zw", "263"], ["Åland Islands", "ax", "358", 1]]';
        $countries = json_decode($json_countries);
        $retrun_array = array();

        foreach ($countries as $country) {
            $option = array(
                'label' => $country[0] . ' [+' . $country[2] . ']',
                'value' => $country[2],
                'code' => $country[1],
                'is_placeholder' => false,
            );
            array_push($retrun_array, $option);
        }

        return $retrun_array;
    }

    function setting_instructions()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        $display = 'inherit';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        echo '<div> <p>' . __('make a page and name it login, put the shortcode inside it, now you have a login page!', 'login-with-phone-number') . '</p>
		<p><code>[drc_lwp]</code></p>';
        echo '<div> <p>' . __('For showing metas of user for example in profile page, like: showing phone number, username, email, nicename', 'login-with-phone-number') . '</p>
		<p><code>[drc_lwp_metas nicename="false" username="false" phone_number="true" email="false"]</code></p>
		<p><a href="https://drc.com/product/login-with-phone-number-in-wordpress/" target="_blank" class="lwp_more_help">' . __('Need more help?', 'login-with-phone-number') . '</a></p>
		</div>';
    }

    function setting_country_code()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_country_codes'])) $options['drc_country_codes'] = ["93"];
        $country_codes = $this->get_country_code_options();
        ?>
        <select name="drc_lwp_settings[drc_country_codes][]" id="drc_country_codes" multiple>
            <?php
            foreach ($country_codes as $country) {
                $rr = in_array($country["value"], $options['drc_country_codes']);
                echo '<option value="' . esc_attr($country["value"]) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($country['label']) . '</option>';
            }
            ?>
        </select>
        <?php

    }

    function setting_default_username()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_username'])) $options['drc_default_username'] = 'user';

        echo '<input id="lwp_default_username" type="text" name="drc_lwp_settings[drc_default_username]" class="regular-text" value="' . esc_attr($options['drc_default_username']) . '" />
		<p class="description">' . __('Default username', 'login-with-phone-number') . '</p>';

    }

    function setting_default_nickname()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_nickname'])) $options['drc_default_nickname'] = 'user';


        echo '<input id="lwp_default_nickname" type="text" name="drc_lwp_settings[drc_default_nickname]" class="regular-text" value="' . esc_attr($options['drc_default_nickname']) . '" />
		<p class="description">' . __('Default nickname', 'login-with-phone-number') . '</p>';

    }


    function setting_buy_credit()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        if (!isset($options['drc_website_url'])) $options['drc_website_url'] = '';
        if (!isset($options['drc_phone_number_ccode'])) $options['drc_phone_number_ccode'] = '';
        $display = 'inherit';
        if (!$options['drc_phone_number']) {
            $display = 'none';
        }
        ?>

        <div class="creditor">
            <button type="button" class="button-primary loiuyt"
                    value="<?php _e('Check credit', 'login-with-phone-number'); ?>"><?php _e('Check credit', 'login-with-phone-number'); ?></button>
            <span class="cp"></span>

            <button type="button" class="button-primary refreshShop"
                    value="<?php _e('Refresh', 'login-with-phone-number'); ?>"><?php _e('Refresh', 'login-with-phone-number'); ?></button>
            <span class="df">
                <?php echo esc_url($options['drc_website_url']); ?>

            </span>
        </div>


        <div class="chargeAccount">

        </div>
        <?php
    }

    function settings_validate($input)
    {

        return $input;
    }

    function removePhpComments($str, $preserveWhiteSpace = true)
    {
        $commentTokens = [
            \T_COMMENT,
            \T_DOC_COMMENT,
        ];
        $tokens = token_get_all($str);


        if (true === $preserveWhiteSpace) {
            $lines = explode(PHP_EOL, $str);
        }


        $s = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    if (true === $preserveWhiteSpace) {
                        $comment = $token[1];
                        $lineNb = $token[2];
                        $firstLine = $lines[$lineNb - 1];
                        $p = explode(PHP_EOL, $comment);
                        $nbLineComments = count($p);
                        if ($nbLineComments < 1) {
                            $nbLineComments = 1;
                        }
                        $firstCommentLine = array_shift($p);

                        $isStandAlone = (trim($firstLine) === trim($firstCommentLine));

                        if (false === $isStandAlone) {
                            if (2 === $nbLineComments) {
                                $s .= PHP_EOL;
                            }

                            continue; // just remove inline comments
                        }

                        // stand alone case
                        $s .= str_repeat(PHP_EOL, $nbLineComments - 1);
                    }
                    continue;
                }
                $token = $token[1];
            }

            $s .= $token;
        }
        return $s;
    }

    function enqueue_scripts()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_redirect_url'])) $options['drc_redirect_url'] = home_url();
        if (!isset($options['drc_default_gateways'])) $options['drc_default_gateways'] = 'firebase';
        if (!isset($options['drc_use_custom_gateway'])) $options['drc_use_custom_gateway'] = '1';
        if (!isset($options['drc_firebase_api'])) $options['drc_firebase_api'] = '';
        if (!isset($options['drc_firebase_config'])) $options['drc_firebase_config'] = '';
        if (!isset($options['drc_enable_timer_on_sending_sms'])) $options['drc_enable_timer_on_sending_sms'] = '1';
        if (!isset($options['drc_timer_count'])) $options['drc_timer_count'] = '60';
//        if (!isset($options['drc_default_gateways'])) $options['drc_default_gateways'] = '';
        $localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecturl' => $options['drc_redirect_url'],
            'UserId' => 0,
            'loadingmessage' => __('please wait...', 'login-with-phone-number'),
            'timer' => $options['drc_enable_timer_on_sending_sms'],
            'timer_count' => $options['drc_timer_count'],
        );

        wp_enqueue_style('drc-lwp', plugins_url('/styles/login-with-phonenumber.css', __FILE__));

        wp_enqueue_script('drc-lwp-validate-script', plugins_url('/scripts/jquery.validate.js', __FILE__), array('jquery'));


        wp_enqueue_script('drc-lwp', plugins_url('/scripts/login-with-phonenumber.js', __FILE__), array('jquery'));


        if ($options['drc_use_custom_gateway'] == '1' && $options['drc_default_gateways'] === 'firebase') {
            wp_enqueue_script('lwp-firebase', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-app.js', array(), false, true);
            wp_enqueue_script('lwp-firebase-auth', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-auth.js', array(), false, true);
            wp_enqueue_script('lwp-firebase-sender', plugins_url('/scripts/firebase-sender.js', __FILE__), array('jquery'));

            $localize['firebase_api'] = $options['drc_firebase_api'];
        }
        wp_localize_script('drc-lwp', 'drc_lwp', $localize);
        if ($options['drc_use_custom_gateway'] == '1' && $options['drc_default_gateways'] === 'firebase') {

            wp_add_inline_script('drc-lwp', '' . htmlspecialchars_decode($options['drc_firebase_config']));
        }

    }

    function drc_lwp_metas($vals)
    {

        $atts = shortcode_atts(array(
            'email' => false,
            'phone_number' => true,
            'username' => false,
            'nicename' => false

        ), $vals);
        ob_start();
        $user = wp_get_current_user();
        if (!isset($atts['username'])) $atts['username'] = false;
        if (!isset($atts['nicename'])) $atts['nicename'] = false;
        if (!isset($atts['email'])) $atts['email'] = false;
        if (!isset($atts['phone_number'])) $atts['phone_number'] = true;
        if ($atts['username'] == 'true') {
            echo '<div class="lwp user_login">' . esc_html($user->user_login) . '</div>';
        }
        if ($atts['nicename'] == 'true') {
            echo '<div class="lwp user_nicename">' . esc_html($user->user_nicename) . '</div>';

        }
        if ($atts['email'] == 'true') {
            echo '<div class="lwp user_email">' . esc_html($user->user_email) . '</div>';

        }
        if ($atts['phone_number'] == 'true') {
            echo '<div class="lwp user_email">' . esc_html(get_user_meta($user->ID, 'phone_number', true)) . '</div>';
        }
        return ob_get_clean();
    }

    function shortcode($atts)
    {

        extract(shortcode_atts(array(
            'redirect_url' => ''
        ), $atts));
        ob_start();
        $options = get_option('drc_lwp_settings');
        $localizationoptions = get_option('drc_lwp_settings_localization');
        if (!isset($options['drc_sms_login'])) $options['zz_sms_login'] = '1';
        if (!isset($options['drc_enable_accept_terms_and_condition'])) $options['drc_enable_accept_terms_and_condition'] = '1';
        if (!isset($options['drc_term_and_conditions_text'])) $options['drc_term_and_conditions_text'] = '';
        if (!isset($options['drc_email_login'])) $options['drc_email_login'] = '1';
        if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
        if (!isset($options['drc_redirect_url'])) $options['drc_redirect_url'] = '';
        if (!isset($options['drc_login_message'])) $options['drc_login_message'] = 'Welcome, You are logged in...';
        if (!isset($options['drc_country_codes'])) $options['drc_country_codes'] = [];
        if (!isset($options['drc_position_form'])) $options['drc_position_form'] = '0';
        if (!isset($localizationoptions['drc_localization_placeholder_of_phonenumber_field'])) $localizationoptions['drc_localization_placeholder_of_phonenumber_field'] = '';
        if (!isset($localizationoptions['drc_localization_title_of_login_form'])) $localizationoptions['drc_localization_title_of_login_form'] = '';
        if (!isset($localizationoptions['drc_localization_title_of_login_form_email'])) $localizationoptions['drc_localization_title_of_login_form_email'] = '';

        $class = '';
        if ($options['drc_position_form'] == '1') {
            $class = 'lw-sticky';
        }
        $is_user_logged_in = is_user_logged_in();
        if (!$is_user_logged_in) {
            ?>
            <a id="show_login" class="show_login"
               style="display: none"
               data-sticky="<?php echo esc_attr($options['drc_position_form']); ?>"><?php echo __('login', 'login-with-phone-number'); ?></a>
            <div class="lwp_forms_login <?php echo esc_attr($class); ?>">
                <?php
                if ($options['drc_sms_login']) {
                    if ($options['drc_email_login'] && $options['drc_sms_login']) {
                        $cclass = 'display:none';
                    } else if (!$options['drc_email_login'] && $options['drc_sms_login']) {
                        $cclass = 'display:block';
                    }
                    ?>
                    <form id="lwp_login" class="ajax-auth" action="login" style="<?php echo $cclass; ?>" method="post">

                        <div class="lh1"><?php echo isset($localizationoptions['drc_localization_status']) ? esc_html($localizationoptions['drc_localization_title_of_login_form']) : (__('Login / register', 'login-with-phone-number')); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <div class="lwp-form-box">
                            <label class="lwp_labels"
                                   for="lwp_username"><?php echo __('Phone number', 'login-with-phone-number'); ?></label>
                            <?php
                            //                    $country_codes = $this->get_country_code_options();
                            ?>
                            <div class="lwp-form-box-bottom">
                                <div class="lwp_country_codes_wrap">
                                    <select id="lwp_country_codes">
                                        <?php
                                        foreach ($options['drc_country_codes'] as $country) {
//                            $rr=in_array($country["value"],$options['drc_country_codes']);
                                            echo '<option value="' . esc_attr($country) . '">+' . esc_html($country) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="number" class="required lwp_username the_lwp_input" name="lwp_username"
                                       placeholder="<?php echo ($localizationoptions['drc_localization_placeholder_of_phonenumber_field']) ? sanitize_text_field($localizationoptions['drc_localization_placeholder_of_phonenumber_field']) : (__('9*********', 'login-with-phone-number')); ?>">
                            </div>
                        </div>
                        <?php if ($options['drc_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">
                                <input class="required lwp_check_box" type="checkbox" name="lwp_accept_terms"
                                       checked="checked">
                                <span class="accept_terms_and_conditions_text"><?php echo esc_html($options['drc_term_and_conditions_text']); ?></span>
                            </div>
                        <?php } ?>
                        <button class="submit_button auth_phoneNumber" type="submit">
                            <?php echo __('Submit', 'login-with-phone-number'); ?>
                        </button>
                        <?php
                        if ($options['drc_email_login']) {
                            ?>
                            <button class="submit_button auth_with_email secondaryccolor" type="button">
                                <?php echo __('Login with email', 'login-with-phone-number'); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
                <?php
                if ($options['drc_email_login']) {
//                    if($options['drc_email_login'] && $options['drc_sms_login']){
                    $ecclass = 'display:block';
//                    }
                    ?>
                    <form id="lwp_login_email" class="ajax-auth" action="loginemail" style="<?php echo $ecclass; ?>"
                          method="post">

                        <div class="lh1"><?php echo isset($localizationoptions['drc_localization_status']) ? esc_html($localizationoptions['drc_localization_title_of_login_form_email']) : (__('Login / register', 'login-with-phone-number')); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Your email:', 'login-with-phone-number'); ?></label>
                        <input type="email" class="required lwp_email the_lwp_input" name="lwp_email"
                               placeholder="<?php echo __('Please enter your email', 'login-with-phone-number'); ?>">
                        <?php if ($options['drc_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">
                                <input class="required lwp_check_box lwp_accept_terms_email" type="checkbox"
                                       name="lwp_accept_terms_email" checked="checked">
                                <span class="accept_terms_and_conditions_text"><?php echo esc_html($options['drc_term_and_conditions_text']); ?></span>
                            </div>
                        <?php } ?>
                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Submit', 'login-with-phone-number'); ?>
                        </button>
                        <?php
                        if ($options['drc_sms_login']) {
                            ?>
                            <button class="submit_button auth_with_phoneNumber secondaryccolor" type="button">
                                <?php echo esc_attr_x('Login with phone number','Button Label', 'login-with-phone-number'); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>

                <form id="lwp_activate" class="ajax-auth" action="activate" method="post">
                    <div class="lh1"><?php echo __('Activation', 'login-with-phone-number'); ?></div>
                    <p class="status"></p>
                    <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                    <div class="lwp_top_activation">
                        <div class="lwp_timer"></div>


                    </div>
                    <label class="lwp_labels"
                           for="lwp_scode"><?php echo __('Security code', 'login-with-phone-number'); ?></label>
                    <input type="text" class="required lwp_scode" name="lwp_scode" placeholder="ـ ـ ـ ـ ـ ـ">

                    <button class="submit_button auth_secCode">
                        <?php echo __('Activate', 'login-with-phone-number'); ?>
                    </button>
                    <button class="submit_button lwp_didnt_r_c lwp_disable" type="button">
                        <?php echo __('Send code again', 'login-with-phone-number'); ?>
                    </button>
                    <hr class="lwp_line"/>
                    <div class="lwp_bottom_activation">

                        <a class="lwp_change_pn" href="#">
                            <?php echo __('Change phone number?', 'login-with-phone-number'); ?>
                        </a>
                        <a class="lwp_change_el" href="#">
                            <?php echo __('Change email?', 'login-with-phone-number'); ?>
                        </a>
                    </div>


                    <a class="close" href="">(x)</a>
                </form>

                <?php
                if ($options['drc_password_login']) {
                    ?>
                    <form id="lwp_update_password" class="ajax-auth" action="update_password" method="post">

                        <div class="lh1"><?php echo __('Update password', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Enter new password:', 'login-with-phone-number'); ?></label>
                        <input type="password" class="required lwp_up_password" name="lwp_up_password"
                               placeholder="<?php echo __('Please choose a password', 'login-with-phone-number'); ?>">

                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Update', 'login-with-phone-number'); ?>
                        </button>
                        <a class="close" href="">(x)</a>
                    </form>
                    <form id="lwp_enter_password" class="ajax-auth" action="enter_password" method="post">

                        <div class="lh1"><?php echo __('Enter password', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Your password:', 'login-with-phone-number'); ?></label>
                        <input type="password" class="required lwp_auth_password" name="lwp_auth_password"
                               placeholder="<?php echo __('Please enter your password', 'login-with-phone-number'); ?>">

                        <button class="submit_button login_with_pass" type="submit">
                            <?php echo __('Login', 'login-with-phone-number'); ?>
                        </button>
                        <button class="submit_button forgot_password" type="button">
                            <?php echo __('Forgot password', 'login-with-phone-number'); ?>
                        </button>
                        <hr class="lwp_line"/>
                        <div class="lwp_bottom_activation">

                            <a class="lwp_change_pn" href="#">
                                <?php echo __('Change phone number?', 'login-with-phone-number'); ?>
                            </a>
                            <a class="lwp_change_el" href="#">
                                <?php echo __('Change email?', 'login-with-phone-number'); ?>
                            </a>
                        </div>

                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
            </div>
            <?php
        } else {
            if ($options['drc_redirect_url'])
                wp_redirect(esc_url($options['drc_redirect_url']));
            else if ($options['drc_login_message'])
                echo esc_html($options['drc_login_message']);
        }
        return ob_get_clean();
    }

    function phone_number_exist($phone_number)
    {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'phone_number',
                    'value' => $phone_number,
                    'compare' => '='
                )
            )
        );

        $member_arr = get_users($args);
        if ($member_arr && $member_arr[0])
            return $member_arr[0]->ID;
        else
            return 0;

    }

    function lwp_ajax_login()
    {
        $usesrname = sanitize_text_field($_GET['username']);
        $options = get_option('drc_lwp_settings');

        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $usesrname) == "") {
            $phone_number = ltrim($usesrname, '0');
            $phone_number = substr($phone_number, 0, 15);
//echo $phone_number;
//die();
            if (strlen($phone_number) < 10) {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', 'login-with-phone-number')
                ]);
                die();
            }
            $username_exists = $this->phone_number_exist($phone_number);
//            $registration = get_site_option('registration');
            if (!isset($options['drc_user_registration'])) $options['drc_user_registration'] = '1';
            $registration = $options['drc_user_registration'];
            $is_multisite = is_multisite();
            if ($is_multisite) {
                if ($registration == '0' && !$username_exists) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $usesrname,
                        'registeration' => $registration,
                        'is_multisite' => $is_multisite,
                        'username_exists' => $username_exists,
                        'message' => __('users can not register!', 'login-with-phone-number')
                    ]);
                    die();
                }
            } else {
                if (!$username_exists) {

                    if ($registration == '0') {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $usesrname,
                            'registeration' => $registration,
                            'is_multisite' => $is_multisite,
                            'username_exists' => $username_exists,
                            'message' => __('users can not register!', 'login-with-phone-number')
                        ]);
                        die();
                    }
                }
            }
            $userRegisteredNow = false;
            if (!$username_exists) {
                $info = array();
                $info['user_login'] = $this->generate_username($phone_number);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    if (in_array('empty_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __($user_register->get_error_message('empty_user_login'))
                        ]);
                        die();
                    } elseif (in_array('existing_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This username is already registered.', 'login-with-phone-number')
                        ]);
                        die();
                    } elseif (in_array('existing_user_email', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This email address is already registered.', 'login-with-phone-number')
                        ]);
                        die();
                    }
                    die();
                } else {
                    add_user_meta($user_register, 'phone_number', sanitize_user($phone_number));
                    update_user_meta($user_register, '_billing_phone', sanitize_user($phone_number));
                    update_user_meta($user_register, 'billing_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, '_shipping_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, 'shipping_phone', sanitize_user($phone_number));
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $username_exists = $user_register;

                }


            }
            $showPass = false;
            $log = '';


//            $options = get_option('drc_lwp_settings');
            if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
            $options['drc_password_login'] = (bool)(int)$options['drc_password_login'];
            if (!$options['drc_password_login']) {
                $log = $this->lwp_generate_token($username_exists, $phone_number);

            } else {
                if (!$userRegisteredNow) {
                    $showPass = true;
                } else {
                    $log = $this->lwp_generate_token($username_exists, $phone_number);
                }
            }
            echo json_encode([
                'success' => true,
                'ID' => $username_exists,
                'phone_number' => $phone_number,
                'showPass' => $showPass,
//                '$userRegisteredNow' => $userRegisteredNow,
//                '$userRegisteredNow1' => $options['drc_password_login'],
                'authWithPass' => (bool)(int)$options['drc_password_login'],
                'message' => __('Sms sent successfully!', 'login-with-phone-number'),
                'log' => $log
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'phone_number' => $usesrname,
                'message' => __('phone number is wrong!', 'login-with-phone-number')
            ]);
            die();
        }
    }

    function lwp_verify_domain()
    {

        echo json_encode([
            'success' => true
        ]);
        die();
    }

    function lwp_forgot_password()
    {
        $log = '';
        if (!isset($_GET['ID'])) $_GET['ID'] = null;
        $ID = sanitize_text_field($_GET['ID']);

        if (!isset($_GET['email'])) $_GET['email'] = '';
        $email = sanitize_email($_GET['email']);

        if (!isset($_GET['phone_number'])) $_GET['phone_number'] = '';
        $phone_number = sanitize_text_field($_GET['phone_number']);


//        $_GET['ID'] = (esc_html($_GET['ID']));
//        $_GET['email'] = (esc_html($_GET['email']));
//        $_GET['phone_number'] = (esc_html($_GET['phone_number']));
        if (!is_numeric($ID)) {
            echo json_encode([
                'success' => false,
                'message' => __('Please enter correct user ID', 'login-with-phone-number')
            ]);
            die();
        }
        if (isset($phone_number) && $phone_number != '' && !is_numeric($phone_number)) {
            echo json_encode([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('Please enter correct phone number', 'login-with-phone-number')
            ]);
            die();
        }
        if (isset($email) && $email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => __('Email is wrong!', 'login-with-phone-number')
            ]);
            die();
        }
        $user = get_user_by('ID', $ID);

        if (is_wp_error($user)) {
            echo json_encode([
                'success' => false,
                'message' => __('User not found!', 'login-with-phone-number')
            ]);
            die();
        }
        if ($email != '' && $ID) {
            $log = $this->lwp_generate_token($ID, $email, true);

        }
        if ($phone_number != '' && $ID != '') {
            $log = $this->lwp_generate_token($ID, $phone_number);

//
        }
        update_user_meta($ID, 'updatedPass', '0');

        echo json_encode([
            'success' => true,
            'ID' => $ID,
            'log' => $log,
            'message' => __('Update password', 'login-with-phone-number')
        ]);
        die();
    }

    function lwp_enter_password_action()
    {

        $ID = sanitize_text_field($_GET['ID']);
        $email = sanitize_email($_GET['email']);
        $password = sanitize_text_field($_GET['password']);
        if ($email != '') {
            $user = get_user_by('email', $email);

        }
        if ($ID != '') {
            $user = get_user_by('ID', $ID);

        }
        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            echo json_encode([
                'success' => false,
                'ID' => $user->ID,
                'err' => $user->get_error_message(),
                'message' => __('Password is incorrect!', 'login-with-phone-number')
            ]);
            die();
        } else {

            echo json_encode([
                'success' => true,
                'ID' => $user->ID,
                'message' => __('Redirecting...', 'login-with-phone-number')
            ]);

            die();
        }
    }

    function lwp_update_password_action()
    {
        $user = wp_get_current_user();
        $password = sanitize_text_field($_GET['password']);
        if ($user) {
            wp_clear_auth_cookie();
            wp_update_user([
                'ID' => $user->ID,
                'user_pass' => $password
            ]);
            update_user_meta($user->ID, 'updatedPass', 1);
            wp_set_current_user($user->ID); // Set the current user detail
            wp_set_auth_cookie($user->ID); // Set auth details in cookie
            echo json_encode([
                'success' => true,
                'message' => __('Password set successfully! redirecting...', 'login-with-phone-number')
            ]);

            die();
        } else {

            echo json_encode([
                'success' => false,
                'message' => __('User not found', 'login-with-phone-number')
            ]);

            die();
        }
    }

    function lwp_ajax_login_with_email()

    {
        $email = sanitize_email( $_GET['email'] );
        $userRegisteredNow = false;

        $options = get_option('drc_lwp_settings');

        if (!isset($options['drc_user_registration'])) $options['drc_user_registration'] = '1';
        $registration = $options['drc_user_registration'];


        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_exists = email_exists($email);
            if (!$email_exists) {
                if ($registration == '0') {
                    echo json_encode([
                        'success' => false,
                        'email' => $email,
                        'registeration' => $registration,
                        'email_exists' => $email_exists,
                        'message' => __('users can not register!', 'login-with-phone-number')
                    ]);
                    die();
                }
                $info = array();
                $info['user_email'] = sanitize_user($email);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $info['user_login'] = $this->generate_username($email);
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    echo json_encode([
                        'success' => false,
                        'email' => $email,
                        '$email_exists' => $email_exists,
                        '$error' => $error,
                        'message' => __('This email address is already registered.', 'login-with-phone-number')
                    ]);

                    die();
                } else {
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $email_exists = $user_register;
                }


            }
//            $user = get_user_by('ID', $email_exists);
//            $password = $user->data->user_pass;
            $log = '';
            $showPass = false;
            if (!$userRegisteredNow) {
                $showPass = true;
            } else {
                $log = $this->lwp_generate_token($email_exists, $email, true);
            }
//            $options = get_option('drc_lwp_settings');
            if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
            $options['drc_password_login'] = (bool)(int)$options['drc_password_login'];
            if (!$options['drc_password_login']) {
                $log = $this->lwp_generate_token($email_exists, $email, true);


            }
            echo json_encode([
                'success' => true,
                'ID' => $email_exists,
                'log' => $log,
//                '$user' => $user,
                'showPass' => $showPass,
                'authWithPass' => (bool)(int)$options['drc_password_login'],

                'email' => $email,
                'message' => __('Email sent successfully!', 'login-with-phone-number')
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'email' => $email,
                'message' => __('email is wrong!', 'login-with-phone-number')
            ]);
            die();
        }
    }

    function lwp_rest_api_stn_auth_customer($data)
    {

        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $data['accode']) == "") {
            $accode = ltrim($data['accode'], '0');
            $accode = substr($accode, 0, 15);
            return [

                'success' => true
            ];
        } else {
            return [
                'success' => false
            ];
        }


    }

    function lwp_register_rest_route()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_token'])) $options['drc_token'] = '';

//        if (empty($options['drc_token'])) {

        register_rest_route('authorizelwp', '/(?P<accode>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array(&$this, 'lwp_rest_api_stn_auth_customer'),
            'permission_callback' => '__return_true'
        ));

//        }
    }


    function lwp_generate_token($user_id, $contact, $send_email = false)
    {
        $six_digit_random_number = mt_rand(100000, 999999);
        update_user_meta($user_id, 'activation_code', $six_digit_random_number);
        if ($send_email) {
            $wp_mail = wp_mail($contact, 'activation code', __('your activation code: ', 'login-with-phone-number') . $six_digit_random_number);
            return $wp_mail;
        } else {
            return $this->send_sms($contact, $six_digit_random_number);
        }
    }

    function generate_username($defU = '')
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_username'])) $options['drc_default_username'] = 'user';
        if (!isset($options['drc_use_phone_number_for_username'])) $options['drc_use_phone_number_for_username'] = '0';
        if ($options['drc_use_phone_number_for_username'] == '0') {
            $ulogin = $options['drc_default_username'];

        } else {
            $ulogin = $defU;
        }

        // make user_login unique so WP will not return error
        $check = username_exists($ulogin);
        if (!empty($check)) {
            $suffix = 2;
            while (!empty($check)) {
                $alt_ulogin = $ulogin . '-' . $suffix;
                $check = username_exists($alt_ulogin);
                $suffix++;
            }
            $ulogin = $alt_ulogin;
        }

        return $ulogin;
    }

    function generate_nickname()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_nickname'])) $options['drc_default_nickname'] = 'user';


        return $options['drc_default_nickname'];
    }

    function send_sms($phone_number, $code)
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_use_custom_gateway'])) $options['drc_use_custom_gateway'] = '1';
        if (!isset($options['drc_default_gateways'])) $options['drc_default_gateways'] = 'firebase';
        if ($options['drc_use_custom_gateway'] == '1') {
            if ($options['drc_default_gateways'] == 'zenziva') {
                $zenziva = new LWP_Zenziva_Api();
                return $zenziva->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'infobip') {
                $infobip = new LWP_Infobip_Api();
                return $infobip->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'raygansms') {
                $raygansms = new LWP_Raygansms_Api();
                return $raygansms->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'smsbharti') {
                $smsbharti = new LWP_Smsbharti_Api();
                return $smsbharti->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'twilio') {
                $twilio = new LWP_Twilio_Api();
                return $twilio->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'mshastra') {
                $mshastra = new LWP_Mshastra_Api();
                return $mshastra->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'taqnyat') {
                $taqnyat = new LWP_Taqnyat_Api();
                return $taqnyat->lwp_send_sms($phone_number, $code);
            } else if ($options['drc_default_gateways'] == 'custom') {
                $custom = new LWP_CUSTOM_Api();
                return $custom->lwp_send_sms($phone_number, $code);
            } else {
                return true;
            }
        } else {
//        $smsUrl = "https://zoomiroom.com/customer/sms/" . $options['drc_token'] . "/" . $phone_number . "/" . $code;
            $response = wp_safe_remote_post("https://zoomiroom.com/customer/sms/", [
                'timeout' => 60,
                'redirection' => 1,
                'blocking' => true,
                'headers' => array('Content-Type' => 'application/json',
                    'token' => $options['drc_token']),
                'body' => wp_json_encode([
                    'phoneNumber' => $phone_number,
                    'message' => $code
                ])
            ]);
            $body = wp_remote_retrieve_body($response);
            return $body;
        }
//        $response = wp_remote_get($smsUrl);
//        wp_remote_retrieve_body($response);

    }

    function lwp_ajax_register()
    {
        $options = get_option('drc_lwp_settings');
        if (!isset($options['drc_default_gateways'])) $options['drc_default_gateways'] = 'firebase';
        if (!isset($options['drc_use_custom_gateway'])) $options['drc_use_custom_gateway'] = '1';

        if (isset($_GET['phone_number'])) {
            $phoneNumber = sanitize_text_field($_GET['phone_number']);
            if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $phoneNumber) == "") {
                $phone_number = ltrim($phoneNumber, '0');
                $phone_number = substr($phone_number, 0, 15);

                if ($phone_number < 10) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('phone number is wrong!', 'login-with-phone-number')
                    ]);
                    die();
                }
            }
            $username_exists = $this->phone_number_exist($phone_number);
        } else if (isset($_GET['email'])) {
            $email=sanitize_email($_GET['email']);
            $username_exists = email_exists($email);
        } else {
            echo json_encode([
                'success' => false,
                'message' => __('phone number is wrong!', 'login-with-phone-number')
            ]);
            die();
        }
        if ($username_exists) {
            $activation_code = get_user_meta($username_exists, 'activation_code', true);
            $secod = sanitize_text_field($_GET['secod']);
            $verificationId = sanitize_text_field($_GET['verificationId']);
            if ($options['drc_use_custom_gateway'] == '1' && $options['drc_default_gateways'] == 'firebase' && isset($_GET['phone_number'])) {
                $response = $this->drc_lwp_activate_through_firebase($verificationId, $secod);
                if ($response->error && $response->error->code == 400) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'firebase' => $response->error,
                        'message' => __('entered code is wrong!', 'login-with-phone-number')
                    ]);
                    die();
                } else {
//                if($response=='true') {
                    $user = get_user_by('ID', $username_exists);
                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
                        $options['drc_password_login'] = (bool)(int)$options['drc_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);

                        echo json_encode(array('success' => true, 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['drc_password_login']));

                    } else {
                        echo json_encode(array('success' => false, 'loggedin' => false, 'message' => __('wrong', 'login-with-phone-number')));

                    }

                    die();
                }
            } else {
                if ($activation_code == $secod) {
                    // First get the user details
                    $user = get_user_by('ID', $username_exists);

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['drc_password_login'])) $options['drc_password_login'] = '1';
                        $options['drc_password_login'] = (bool)(int)$options['drc_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);

                        echo json_encode(array('success' => true, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['drc_password_login']));

                    } else {
                        echo json_encode(array('success' => false, 'loggedin' => false, 'message' => __('wrong', 'login-with-phone-number')));

                    }

                    die();

                } else {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('entered code is wrong!', 'login-with-phone-number')
                    ]);
                    die();

                }
            }
        } else {

            echo json_encode([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('user does not exist!', 'login-with-phone-number')
            ]);
            die();

        }
    }

    function auth_user_login($user_login, $password, $login)
    {
        $info = array();
        $info['user_login'] = $user_login;
        $info['user_password'] = $password;
        $info['remember'] = true;

        // From false to '' since v 4.9
        $user_signon = wp_signon($info, '');
        if (is_wp_error($user_signon)) {
            echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.', 'login-with-phone-number')));
        } else {
            wp_set_current_user($user_signon->ID);
            echo json_encode(array('loggedin' => true, 'message' => __($login . ' successful, redirecting...', 'login-with-phone-number')));
        }

        die();
    }

    function drc_lwp_auth_customer()
    {
        $options = get_option('drc_lwp_settings');

        if (!isset($options['drc_phone_number'])) $options['drc_phone_number'] = '';
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $country_code = sanitize_text_field($_GET['country_code']);
        $url = get_site_url();
        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/authcustomerforsms", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'phoneNumber' => $phone_number,
                'countryCode' => $country_code,
                'websiteUrl' => $url
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        echo esc_html($body);
        die();
    }

    function drc_lwp_auth_customer_with_website()
    {
//        $options = get_option('drc_lwp_settings');

//        if (!isset($options['drc_website_url'])) $options['drc_website_url'] = $this->settings_get_site_url();
        $url = sanitize_text_field($_GET['url']);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/authcustomerwithdomain", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'websiteUrl' => $url,
                'restUrl' => get_rest_url(null, 'authorizelwp')
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        echo esc_html($body);

        die();
    }

    function drc_lwp_activate_through_firebase($sessionInfo, $code)
    {
        $options = get_option('drc_lwp_settings');

        if (!isset($options['drc_firebase_api'])) $options['drc_firebase_api'] = '';

        $response = wp_safe_remote_post("https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key=" . $options['drc_firebase_api'], [
            'timeout' => 60,
            'redirection' => 4,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'code' => $code,
                'sessionInfo' => $sessionInfo
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    function drc_lwp_check_credit()
    {
        $options = get_option('drc_lwp_settings');

        if (!isset($options['drc_token'])) $options['drc_token'] = '';
        $drc_token = $options['drc_token'];
//        $url = "https://drc.com/wp-json/check-credit/$drc_token";
//        $response = wp_remote_get($url);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/checkCredit", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json',
                'token' => $drc_token)
        ]);
        $body = wp_remote_retrieve_body($response);

        echo esc_html($body);



        die();
    }

    function drc_lwp_get_shop()
    {
//        $url = "https://drc.com/wp-json/all-products/0";
//        $response = wp_remote_get($url);
        $lan = get_bloginfo("language");
        $response = wp_safe_remote_post("https://zoomiroom.com/customer/post/smsproducts", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json',
                'lan' => $lan)
        ]);
        $body = wp_remote_retrieve_body($response);

//        $body = wp_remote_retrieve_body($response);


//        echo $body;

        echo esc_html($body);

        die();
    }

    function drc_lwp_activate_customer()
    {
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $secod = sanitize_text_field($_GET['secod']);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/activateCustomer", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'phoneNumber' => $phone_number,
                'activationCode' => $secod
            ])
        ]);
        $body = wp_remote_retrieve_body($response);

//        echo $body;
        echo esc_html($body);


        die();
    }

    function lwp_modify_user_table($column)
    {
        $column['phone_number'] = __('Phone number', 'login-with-phone-number');
        $column['activation_code'] = __('Activation code', 'login-with-phone-number');
        $column['registered_date'] = __('Registered date', 'login-with-phone-number');

        return $column;
    }


    function lwp_modify_user_table_row($val, $column_name, $user_id)
    {
        $udata = get_userdata($user_id);
        switch ($column_name) {
            case 'phone_number' :
                return get_the_author_meta('phone_number', $user_id);
            case 'activation_code' :
                return get_the_author_meta('activation_code', $user_id);
            case 'registered_date' :
                return $udata->user_registered;
            default:
        }
        return $val;
    }

    function lwp_addon_woocommerce_login($template, $template_name, $template_path)
    {
        global $woocommerce;
        $_template = $template;
        if (!$template_path) $template_path = $woocommerce->template_url;
        $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/template/woocommerce/';
        // Look within passed path within the theme - this is priority
        $template = locate_template(array($template_path . $template_name, $template_name));
        if (!$template && file_exists($plugin_path . $template_name)) $template = $plugin_path . $template_name;
        if (!$template) $template = $_template;
        return $template;
    }


    function lwp_make_registered_column_sortable($columns)
    {
        return wp_parse_args(array('registered_date' => 'registered'), $columns);
    }


}

global $drc_lwp;
$drc_lwp = new drcLwp();

/**
 * Template Tag
 */
function drc_lwp()
{

}



