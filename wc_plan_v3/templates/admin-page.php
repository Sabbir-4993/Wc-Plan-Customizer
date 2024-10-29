<div class="wrap">
    <h1><?php esc_html_e('Plan Customizer Orders', 'wc-plan-customizer'); ?></h1>
    
    <div class="tablenav top">
        <div class="alignleft actions">
            <select id="filter-by-date">
                <option value=""><?php esc_html_e('All dates', 'wc-plan-customizer'); ?></option>
                <option value="today"><?php esc_html_e('Today', 'wc-plan-customizer'); ?></option>
                <option value="this-week"><?php esc_html_e('This Week', 'wc-plan-customizer'); ?></option>
                <option value="this-month"><?php esc_html_e('This Month', 'wc-plan-customizer'); ?></option>
            </select>
            <input type="submit" class="button" value="<?php esc_attr_e('Filter', 'wc-plan-customizer'); ?>">
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Order ID', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Customer', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Validity', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Internet Data', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Currency', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Price', 'wc-plan-customizer'); ?></th>
                <th><?php esc_html_e('Date', 'wc-plan-customizer'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'plan_customizer_orders';
            $orders = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

            foreach ($orders as $order) {
                $user_info = get_userdata($order->user_id);
                $order_link = admin_url('post.php?post=' . $order->order_id . '&action=edit');
                ?>
                <tr>
                    <td><a href="<?php echo esc_url($order_link); ?>">#<?php echo esc_html($order->order_id); ?></a></td>
                    <td><?php echo esc_html($user_info ? $user_info->display_name : 'Guest'); ?></td>
                    <td><?php echo esc_html($order->validity); ?> days</td>
                    <td><?php echo esc_html($this->format_data_size($order->internet_data)); ?></td>
                    <td><?php echo esc_html($order->currency); ?></td>
                    <td><?php echo esc_html(number_format($order->final_price, 2)); ?></td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($order->created_at))); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>