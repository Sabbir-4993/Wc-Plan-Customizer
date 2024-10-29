<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer_Public {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('woocommerce_before_add_to_cart_form', array($this, 'display_plan_customizer'));
        add_shortcode('wc_plan_customizer', array($this, 'plan_customizer_shortcode'));
    }

    public function enqueue_scripts() {
        if (!is_product() && !has_shortcode(get_post()->post_content, 'wc_plan_customizer')) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-public',
            WC_PLAN_CUSTOMIZER_URL . 'public/css/public.css',
            array(),
            WC_PLAN_CUSTOMIZER_VERSION
        );

        wp_enqueue_script(
            'wc-plan-customizer-public',
            WC_PLAN_CUSTOMIZER_URL . 'public/js/public.js',
            array('jquery'),
            WC_PLAN_CUSTOMIZER_VERSION,
            true
        );
    }

    public function display_plan_customizer() {
        if (!is_product()) {
            return;
        }

        require_once WC_PLAN_CUSTOMIZER_PATH . 'public/templates/plan-customizer.php';
    }

    public function plan_customizer_shortcode($atts) {
        // Extract shortcode attributes
        $atts = shortcode_atts(array(
            'title' => __('Subscription Plans', 'wc-plan-customizer'),
            'columns' => 3
        ), $atts);

        // Start output buffering
        ob_start();

        // Get all subscription products
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'subscription',
                ),
            ),
        );

        $subscription_products = new WP_Query($args);

        if ($subscription_products->have_posts()) :
            ?>
            <div class="wc-plan-customizer-grid columns-<?php echo esc_attr($atts['columns']); ?>">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <div class="plan-grid">
                    <?php
                    while ($subscription_products->have_posts()) : $subscription_products->the_post();
                        global $product;
                        ?>
                        <div class="plan-item">
                            <h3><?php echo esc_html($product->get_name()); ?></h3>
                            <div class="plan-price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            <div class="plan-description">
                                <?php echo wp_kses_post($product->get_short_description()); ?>
                            </div>
                            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="button">
                                <?php _e('Select Plan', 'wc-plan-customizer'); ?>
                            </a>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php
        endif;

        return ob_get_clean();
    }
}