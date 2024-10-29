<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'plan-customizer-orders',
            __('Plan Settings', 'wc-plan-customizer'),
            __('Settings', 'wc-plan-customizer'),
            'manage_options',
            'plan-customizer-settings',
            array($this, 'render_settings_page')
        );
    }

    public function init_settings() {
        register_setting('wc_plan_customizer_settings', 'wc_plan_customizer_options');

        add_settings_section(
            'pricing_section',
            __('Pricing Settings', 'wc-plan-customizer'),
            null,
            'wc_plan_customizer_settings'
        );

        $this->add_settings_fields();
    }

    private function add_settings_fields() {
        $fields = array(
            array(
                'id' => 'base_price',
                'title' => __('Base Price', 'wc-plan-customizer'),
                'type' => 'number',
                'desc' => __('Starting price for the plan', 'wc-plan-customizer'),
                'default' => 10
            ),
            array(
                'id' => 'price_per_gb',
                'title' => __('Price per GB', 'wc-plan-customizer'),
                'type' => 'number',
                'desc' => __('Price increment per GB', 'wc-plan-customizer'),
                'default' => 2,
                'step' => 0.5
            ),
            array(
                'id' => 'price_per_day',
                'title' => __('Price per Day', 'wc-plan-customizer'),
                'type' => 'number',
                'desc' => __('Price increment per day', 'wc-plan-customizer'),
                'default' => 1,
                'step' => 0.5
            ),
            array(
                'id' => 'min_data',
                'title' => __('Minimum Data (MB)', 'wc-plan-customizer'),
                'type' => 'number',
                'desc' => __('Minimum data allowed (in MB)', 'wc-plan-customizer'),
                'default' => 500
            ),
            array(
                'id' => 'max_data',
                'title' => __('Maximum Data (GB)', 'wc-plan-customizer'),
                'type' => 'number',
                'desc' => __('Maximum data allowed (in GB)', 'wc-plan-customizer'),
                'default' => 100
            )
        );

        foreach ($fields as $field) {
            add_settings_field(
                'wc_plan_customizer_' . $field['id'],
                $field['title'],
                array($this, 'render_field'),
                'wc_plan_customizer_settings',
                'pricing_section',
                $field
            );
        }
    }

    public function render_field($field) {
        $options = get_option('wc_plan_customizer_options', array());
        $value = isset($options[$field['id']]) ? $options[$field['id']] : $field['default'];
        $step = isset($field['step']) ? $field['step'] : 1;
        
        echo '<input type="number" step="' . esc_attr($step) . '" id="wc_plan_customizer_' . esc_attr($field['id']) . '" 
              name="wc_plan_customizer_options[' . esc_attr($field['id']) . ']" 
              value="' . esc_attr($value) . '" class="regular-text" />';
        
        if (isset($field['desc'])) {
            echo '<p class="description">' . esc_html($field['desc']) . '</p>';
        }
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('wc_plan_customizer_settings');
                do_settings_sections('wc_plan_customizer_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}