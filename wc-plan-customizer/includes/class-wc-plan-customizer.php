
<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer {
    private static $instance = null;
    private $admin;
    private $public;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init();
    }

    private function load_dependencies() {
        $this->admin = new WC_Plan_Customizer_Admin();
        $this->public = new WC_Plan_Customizer_Public();
    }

    private function init() {
        register_activation_hook(WC_PLAN_CUSTOMIZER_PATH . 'wc-plan-customizer.php', array($this, 'activate'));
        register_deactivation_hook(WC_PLAN_CUSTOMIZER_PATH . 'wc-plan-customizer.php', array($this, 'deactivate'));
    }

    public function activate() {
        // Add activation tasks here
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Add deactivation tasks here
        flush_rewrite_rules();
    }
}