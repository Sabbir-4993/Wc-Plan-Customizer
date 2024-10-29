jQuery(document).ready(function($) {
    'use strict';

    // Initialize tooltips if using Bootstrap
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Handle form submission
    $('.plan-customizer-form').on('submit', function(e) {
        // Add form validation if needed
        var $form = $(this);
        var $submitButton = $form.find('input[type="submit"]');

        // Disable submit button to prevent double submission
        $submitButton.prop('disabled', true);

        // Re-enable submit button after 2 seconds
        setTimeout(function() {
            $submitButton.prop('disabled', false);
        }, 2000);
    });

    // Handle dynamic field updates
    $('.plan-customizer-dynamic-field').on('change', function() {
        var $field = $(this);
        var value = $field.val();
        var dependentField = $field.data('dependent-field');

        if (dependentField) {
            var $dependentField = $('#' + dependentField);
            
            if (value === 'custom') {
                $dependentField.closest('.form-field').show();
            } else {
                $dependentField.closest('.form-field').hide();
            }
        }
    });

    // Initialize any dynamic fields on page load
    $('.plan-customizer-dynamic-field').trigger('change');

    // Handle bulk actions
    $('#plan-customizer-bulk-action').on('change', function() {
        var action = $(this).val();
        if (action) {
            // Handle different bulk actions
            console.log('Bulk action selected:', action);
        }
    });
});