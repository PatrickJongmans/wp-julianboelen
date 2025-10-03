jQuery(document).ready(function($) {
    'use strict';

    // Media Library Functionality
    // Handle image upload button clicks (both standalone and in arrays)
    $(document).on('click', '.image-select, input[id$="_button"]', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var container = button.closest('.image-field-container');
        var input = container.find('input[type="hidden"]');
        var preview = container.find('.image-preview');
        
        // Fallback for standalone fields (backwards compatibility)
        if (!container.length) {
            var inputId = button.attr('id').replace('_button', '');
            input = $('#' + inputId);
            preview = $('#' + inputId + '_preview');
        }
        
        // Get existing frame or create new one for this field
        var mediaUploader = button.data('frame');
        
        if (!mediaUploader) {
            // Create the media uploader for this specific field
            mediaUploader = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use This Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // Store the frame on the button
            button.data('frame', mediaUploader);
        }
        
        // Clear previous handlers and set new one for this field
        mediaUploader.off('select').on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Set the input value to the image URL
            input.val(attachment.url);
            
            // Update the preview with proper styling
            var imageSize = container.closest('.array-item').length ? 'max-width: 50px;' : 'max-width: 200px;';
            preview.html('<img src="' + attachment.url + '" style="' + imageSize + ' height: auto;" />').addClass('has-image');
            
            // Add remove button if it doesn't exist
            if (!container.find('.remove-image').length) {
                container.find('.image-buttons').append('<button type="button" class="button remove-image">Remove</button>');
            }
        });
        
        // Open the media uploader
        mediaUploader.open();
    });

    // Generic Array Fields Functionality
    // Add new array item
    $(document).on('click', '.add-array-item', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var arrayField = button.closest('.array-field');
        var container = arrayField.find('.array-items-container');
        var template = arrayField.find('.array-item-template').html();
        var currentIndex = container.children('.array-item').length;
        
        if (template) {
            // Replace placeholder index with actual index
            var newItemHtml = template.replace(/\{INDEX\}/g, currentIndex)
                                     .replace(/\{INDEX_PLUS_1\}/g, currentIndex + 1);
            var newItem = $(newItemHtml);
            
            container.append(newItem);
            
            // Add animation
            newItem.hide().slideDown(300);
        }
    });

    // Remove array item
    $(document).on('click', '.remove-array-item', function(e) {
        e.preventDefault();
        
        var item = $(this).closest('.array-item');
        var arrayField = item.closest('.array-field');
        
        // Add animation
        item.slideUp(300, function() {
            item.remove();
            reindexArrayItems(arrayField);
        });
    });

    // Re-index array items after removal (consolidated version)
    function reindexArrayItems(arrayField) {
        var fieldLabel = arrayField.find('.add-array-item').text().replace('Add ', '');
        
        arrayField.find('.array-item').each(function(index) {
            var item = $(this);
            
            // Update array item title
            item.find('.array-item-title').text(fieldLabel + ' ' + (index + 1));
            
            // Update all input names
            item.find('input, select, textarea').each(function() {
                var input = $(this);
                var name = input.attr('name');
                
                if (name && name.indexOf('[') !== -1) {
                    // Update the first index in the name attribute
                    var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    input.attr('name', newName);
                }
            });
        });
    }


    // Image removal functionality (works for both standalone and array images)
    $(document).on('click', '.remove-image', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var container = button.closest('.image-field-container');
        var input = container.find('input[type="hidden"]');
        var preview = container.find('.image-preview');
        
        // Fallback for old ID-based approach
        if (!container.length) {
            var inputId = button.data('input-id');
            input = $('#' + inputId);
            preview = $('#' + inputId + '_preview');
        }
        
        input.val('');
        preview.html('<p>No image selected</p>').removeClass('has-image');
        button.remove(); // Remove the remove button since no image is selected
    });

    // Color picker functionality (if WordPress color picker is available)
    if (typeof $.fn.wpColorPicker !== 'undefined') {
        $('.color-picker').wpColorPicker();
    }

    // Initialize sortable for array items (if jQuery UI sortable is available)
    if (typeof $.fn.sortable !== 'undefined') {
        $('.array-items-container').sortable({
            items: '.array-item',
            handle: '.sort-handle',
            placeholder: 'ui-sortable-placeholder',
            helper: 'clone',
            opacity: 0.8,
            update: function(event, ui) {
                var container = $(this);
                reindexArrayItems(container);
            }
        });
    }

    // Form validation
    $('form').on('submit', function(e) {
        var hasErrors = false;
        
        // Validate required fields
        $(this).find('input[required], textarea[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('error');
                hasErrors = true;
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Validate URLs
        $(this).find('input[type="url"]').each(function() {
            var url = $(this).val().trim();
            if (url && !isValidURL(url)) {
                $(this).addClass('error');
                hasErrors = true;
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fix the errors in the form before submitting.');
        }
    });

    // URL validation helper
    function isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Auto-save functionality (optional)
    var autoSaveTimeout;
    $('input, textarea, select').on('change keyup', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Could implement auto-save here if needed
            console.log('Auto-save triggered');
        }, 3000);
    });
});