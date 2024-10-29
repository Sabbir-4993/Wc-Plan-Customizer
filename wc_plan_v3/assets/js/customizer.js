jQuery(function($) {
    const options = wcPlanCustomizer.options;
    let basePrice = parseFloat(options.base_price);
    let pricePerDay = parseFloat(options.price_per_day);
    let pricePerGB = parseFloat(options.price_per_gb);

    function convertToMB(value, unit) {
        if (unit === 'GB') {
            return value * 1024;
        }
        return parseFloat(value);
    }

    function convertFromMB(mb) {
        if (mb >= 1024) {
            return {
                value: (mb / 1024).toFixed(1),
                unit: 'GB'
            };
        }
        return {
            value: mb.toFixed(1),
            unit: 'MB'
        };
    }

    function updatePrice() {
        const validity = parseInt($('#plan_validity').val()) || 7;
        const dataValue = parseFloat($('#plan_internet_data').val()) || 25;
        const dataUnit = $('#data_unit').val();
        const dataMB = convertToMB(dataValue, dataUnit);
        const currency = $('#plan_currency').val();
        
        const validityPrice = (validity - 7) * pricePerDay;
        const dataPrice = (dataMB / 1024 - 25) * pricePerGB;
        
        const newPrice = basePrice + validityPrice + dataPrice;
        
        const symbol = getCurrencySymbol(currency);
        $('.currency-symbol').text(symbol);
        $('#final_price').text(Math.max(0, newPrice).toFixed(2));
        $('#plan_price').val(Math.max(0, newPrice).toFixed(2));
    }

    function getCurrencySymbol(currency) {
        switch(currency) {
            case 'EUR': return '€';
            case 'GBP': return '£';
            default: return '$';
        }
    }

    function syncDataValues(fromSlider = false) {
        if (fromSlider) {
            const mbValue = parseInt($('#data_slider').val());
            const converted = convertFromMB(mbValue);
            $('#plan_internet_data').val(converted.value);
            $('#data_unit').val(converted.unit);
        } else {
            const inputValue = parseFloat($('#plan_internet_data').val());
            const unit = $('#data_unit').val();
            const mbValue = convertToMB(inputValue, unit);
            $('#data_slider').val(mbValue);
        }
    }

    // Validity controls
    $('#validity_slider').on('input', function() {
        const value = parseInt($(this).val());
        $('#plan_validity').val(value);
        updatePrice();
    });

    $('#plan_validity').on('input', function() {
        const value = parseInt($(this).val()) || 7;
        const clamped = Math.min(Math.max(value, 1), 30);
        $(this).val(clamped);
        $('#validity_slider').val(clamped);
        updatePrice();
    });

    // Internet data controls
    $('#data_slider').on('input', function() {
        syncDataValues(true);
        updatePrice();
    });

    $('#plan_internet_data').on('input', function() {
        const value = parseFloat($(this).val()) || 0.5;
        const unit = $('#data_unit').val();
        const minMB = 500; // 500 MB minimum
        const maxMB = 102400; // 100 GB maximum
        
        const mbValue = convertToMB(value, unit);
        const clampedMB = Math.min(Math.max(mbValue, minMB), maxMB);
        
        const converted = convertFromMB(clampedMB);
        $(this).val(converted.value);
        $('#data_unit').val(converted.unit);
        $('#data_slider').val(clampedMB);
        
        updatePrice();
    });

    $('#data_unit').on('change', function() {
        syncDataValues(false);
        updatePrice();
    });

    // Currency change
    $('#plan_currency').on('change', updatePrice);

    // Initialize values
    function initializeValues() {
        // Set initial validity values
        $('#validity_slider, #plan_validity').val(7);

        // Set initial data values (25 GB = 25600 MB)
        $('#data_slider').val(25600);
        $('#plan_internet_data').val('25.0');
        $('#data_unit').val('GB');

        updatePrice();
    }

    // Initialize on document ready
    $(document).ready(function() {
        initializeValues();
    });
});