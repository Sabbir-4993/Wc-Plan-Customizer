<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Plan Customizer', 'wc-plan-customizer'),
            __('Plan Customizer', 'wc-plan-customizer'),
            'manage_options',
            'wc-plan-customizer',
            array($this, 'display_admin_page'),
            'dashicons-admin-generic',
            56
        );
    }

    public function display_admin_page() {
        require_once WC_PLAN_CUSTOMIZER_PATH . 'admin/templates/admin-page.php';
    }

    public function enqueue_scripts($hook) {
        if ('toplevel_page_wc-plan-customizer' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-admin',
            WC_PLAN_CUSTOMIZER_URL . 'admin/css/admin.css',
            array(),
            WC_PLAN_CUSTOMIZER_VERSION
        );

        wp_enqueue_script(
            'wc-plan-customizer-admin',
            WC_PLAN_CUSTOMIZER_URL . 'admin/js/admin.js',
            array('jquery'),
            WC_PLAN_CUSTOMIZER_VERSION,
            true
        );
    }
}