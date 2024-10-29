<div id="plan-customizer" class="wc-plan-customizer">
    <div class="plan-customizer-header">
        <h3><?php esc_html_e('Customize Your Plan', 'wc-plan-customizer'); ?></h3>
    </div>
    
    <div class="plan-customizer-body">
        <div class="customizer-field">
            <label for="plan_currency"><?php esc_html_e('Currency', 'wc-plan-customizer'); ?></label>
            <select id="plan_currency" name="plan_currency">
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
            </select>
        </div>

        <div class="customizer-field">
            <label for="plan_validity"><?php esc_html_e('Validity (days)', 'wc-plan-customizer'); ?></label>
            <div class="input-group">
                <input type="number" id="plan_validity" name="plan_validity" value="7" min="1" max="30">
                <span class="unit">days</span>
            </div>
            <input type="range" id="validity_slider" min="1" max="30" value="7" step="1">
            <div class="slider-labels">
                <span>1 day</span>
                <span>15 days</span>
                <span>30 days</span>
            </div>
        </div>

        <div class="customizer-field">
            <label for="plan_internet_data"><?php esc_html_e('Internet Data', 'wc-plan-customizer'); ?></label>
            <div class="input-group">
                <input type="number" id="plan_internet_data" name="plan_internet_data" value="25.0" min="0.5" max="100" step="0.1">
                <select id="data_unit" name="data_unit">
                    <option value="MB">MB</option>
                    <option value="GB" selected>GB</option>
                </select>
            </div>
            <input type="range" id="data_slider" min="500" max="102400" value="25600" step="100">
            <div class="slider-labels">
                <span>500 MB</span>
                <span>50 GB</span>
                <span>100 GB</span>
            </div>
        </div>

        <div class="price-display">
            <span class="currency-symbol">$</span>
            <span id="final_price"><?php echo esc_html($base_price); ?></span>
        </div>

        <input type="hidden" name="plan_price" id="plan_price" value="<?php echo esc_attr($base_price); ?>">
        <button type="submit" class="single_add_to_cart_button button alt">Add to Cart</button>
    </div>
</div>