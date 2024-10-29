<?php
/**
 * Plugin Name: WC Plan Customizer
 * Description: A WooCommerce plugin for customizing subscription plans
 * Version: 1.0.0
 * Author: sabbir Ahmed
 * Author URI: https://mrsabbir.com
 * Text Domain: Wc-Plan-Customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer_Plugin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Plan Customizer', 'wc-plan-customizer'),
            __('Plan Customizer', 'wc-plan-customizer'),
            'manage_options',
            'wc-plan-customizer',
            array($this, 'admin_page_content'),
            'dashicons-admin-generic',
            56
        );
    }

    public function admin_page_content() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="plan-customizer-admin-content">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('wc_plan_customizer_options');
                    do_settings_sections('wc_plan_customizer_options');
                    ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Enable Plan Customizer', 'wc-plan-customizer'); ?></th>
                            <td>
                                <input type="checkbox" name="wc_plan_customizer_enabled" value="1" <?php checked(get_option('wc_plan_customizer_enabled'), 1); ?>>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('wc_plan_customizer_options', 'wc_plan_customizer_enabled');
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_wc-plan-customizer' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-admin',
            plugins_url('assets/css/admin.css', __FILE__),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'wc-plan-customizer-admin',
            plugins_url('assets/js/admin.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
    }

    public function enqueue_frontend_scripts() {
        if (!is_product()) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-frontend',
            plugins_url('assets/css/frontend.css', __FILE__),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'wc-plan-customizer-frontend',
            plugins_url('assets/js/frontend.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
    }

    public function activate() {
        // Activation code here
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Deactivation code here
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function wc_plan_customizer_init() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            ?>
            <div class="error">
                <p><?php _e('WC Plan Customizer requires WooCommerce to be installed and activated.', 'wc-plan-customizer'); ?></p>
            </div>
            <?php
        });
        return;
    }

    // Initialize the plugin
    WC_Plan_Customizer_Plugin::get_instance();
}

add_action('plugins_loaded', 'wc_plan_customizer_init');

// Register activation and deactivation hooks
register_activation_hook(__FILE__, array('WC_Plan_Customizer_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('WC_Plan_Customizer_Plugin', 'deactivate'));