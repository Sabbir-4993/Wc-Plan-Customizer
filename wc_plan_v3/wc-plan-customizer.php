<?php
/**
 * Plugin Name: WooCommerce Plan Customizer
 * Description: Custom plan configurator for WooCommerce with admin dashboard
 * Version: 1.0.0
 * Author: Sabbir Ahmed
 * Text Domain: WC-plan-customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/admin/class-settings.php';

class WC_Plan_Customizer_Plugin {
    private $settings;

    public function __construct() {
        $this->settings = new WC_Plan_Customizer_Settings();

        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('woocommerce_before_add_to_cart_button', array($this, 'display_customizer'));
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 3);
        add_filter('woocommerce_get_item_data', array($this, 'display_cart_item_data'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_order_item_meta'), 10, 4);
        add_action('woocommerce_before_calculate_totals', array($this, 'calculate_custom_price'));
        
        // Add shortcode support
        add_shortcode('plan_customizer', array($this, 'shortcode_output'));
        
        // Create custom table on plugin activation
        register_activation_hook(__FILE__, array($this, 'create_custom_table'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Plan Customizer', 'wc-plan-customizer'),
            __('Plan Customizer', 'wc-plan-customizer'),
            'manage_options',
            'plan-customizer-orders',
            array($this, 'render_admin_page'),
            'dashicons-cart',
            56
        );
    }

    public function render_admin_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }

    public function format_data_size($size_in_gb) {
        if ($size_in_gb >= 1) {
            return number_format($size_in_gb, 1) . ' GB';
        }
        return number_format($size_in_gb * 1024, 0) . ' MB';
    }

    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>WooCommerce Plan Customizer requires WooCommerce to be installed and active.</p></div>';
            });
            return;
        }
    }

    public function create_custom_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'plan_customizer_orders';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            validity int(11) NOT NULL,
            internet_data decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL,
            final_price decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'wc-plan-customizer',
            plugins_url('assets/css/style.css', __FILE__),
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'wc-plan-customizer',
            plugins_url('assets/js/customizer.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );

        $options = get_option('wc_plan_customizer_options', array(
            'base_price' => 10,
            'price_per_gb' => 2,
            'price_per_day' => 1,
            'min_data' => 500,
            'max_data' => 100
        ));

        wp_localize_script('wc-plan-customizer', 'wcPlanCustomizer', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wc-plan-customizer'),
            'options' => $options
        ));
    }

    public function shortcode_output($atts) {
        $options = get_option('wc_plan_customizer_options', array());
        $base_price = isset($options['base_price']) ? $options['base_price'] : 10;

        $atts = shortcode_atts(array(
            'base_price' => $base_price,
        ), $atts, 'plan_customizer');

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/customizer.php';
        return ob_get_clean();
    }

    public function display_customizer() {
        global $product;
        
        if (!$product->is_type('simple')) {
            return;
        }
        
        $options = get_option('wc_plan_customizer_options', array());
        $base_price = isset($options['base_price']) ? $options['base_price'] : $product->get_price();
        include plugin_dir_path(__FILE__) . 'templates/customizer.php';
    }

    public function add_cart_item_data($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['plan_validity']) && isset($_POST['plan_internet_data'])) {
            $cart_item_data['plan_customizer'] = array(
                'validity' => intval($_POST['plan_validity']),
                'internet_data' => floatval($_POST['plan_internet_data']),
                'currency' => sanitize_text_field($_POST['plan_currency']),
                'price' => floatval($_POST['plan_price'])
            );
        }
        return $cart_item_data;
    }

    public function display_cart_item_data($item_data, $cart_item) {
        if (isset($cart_item['plan_customizer'])) {
            $item_data[] = array(
                'key' => __('Validity', 'wc-plan-customizer'),
                'value' => $cart_item['plan_customizer']['validity'] . ' days'
            );
            $item_data[] = array(
                'key' => __('Internet Data', 'wc-plan-customizer'),
                'value' => $this->format_data_size($cart_item['plan_customizer']['internet_data'])
            );
        }
        return $item_data;
    }

    public function add_order_item_meta($item, $cart_item_key, $values, $order) {
        if (isset($values['plan_customizer'])) {
            $item->add_meta_data(__('Validity', 'wc-plan-customizer'), $values['plan_customizer']['validity'] . ' days');
            $item->add_meta_data(__('Internet Data', 'wc-plan-customizer'), $this->format_data_size($values['plan_customizer']['internet_data']));
            
            // Save to custom table
            global $wpdb;
            $table_name = $wpdb->prefix . 'plan_customizer_orders';
            
            $wpdb->insert(
                $table_name,
                array(
                    'order_id' => $order->get_id(),
                    'user_id' => get_current_user_id(),
                    'validity' => $values['plan_customizer']['validity'],
                    'internet_data' => $values['plan_customizer']['internet_data'],
                    'currency' => $values['plan_customizer']['currency'],
                    'final_price' => $values['plan_customizer']['price']
                ),
                array('%d', '%d', '%d', '%f', '%s', '%f')
            );
        }
    }

    public function calculate_custom_price($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        foreach ($cart->get_cart() as $cart_item) {
            if (isset($cart_item['plan_customizer'])) {
                $cart_item['data']->set_price($cart_item['plan_customizer']['price']);
            }
        }
    }
}

new WC_Plan_Customizer_Plugin();