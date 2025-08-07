/**
 * Smart Order Notes - Admin JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    // Initialize when DOM is ready
    $(document).ready(function() {
        initSmartOrderNotes();
    });

    /**
     * Initialize Smart Order Notes functionality
     */
    function initSmartOrderNotes() {
        // Handle template selection and preview
        handleTemplateSelection();

        // Handle template insertion
        handleTemplateInsertion();

        // Handle note type auto-selection
        handleNoteTypeSelection();

        // Initialize accessibility features
        initAccessibility();
    }

    /**
     * Handle template selection and preview functionality
     */
    function handleTemplateSelection() {
        $(document).on('change', '#sonotes_template_select', function() {
            var $select = $(this);
            var $selected = $select.find('option:selected');
            var content = $selected.data('content');
            var type = $selected.data('type');
            var $preview = $('#sonotes_preview');
            var $previewContent = $preview.find('.sonotes-preview-content');

            if (content) {
                // Show and update preview
                $previewContent.text(content);
                $preview.slideDown(200);

                // Auto-select corresponding note type
                if (type) {
                    $('input[name="sonotes_note_type"][value="' + type + '"]').prop('checked', true);
                    updateNoteTypeDescription(type);
                }

                // Enable insert button
                $('#sonotes_insert_btn').prop('disabled', false);

            } else {
                // Hide preview and disable button
                $preview.slideUp(200);
                $('#sonotes_insert_btn').prop('disabled', true);
            }
        });
    }

    /**
     * Handle template insertion into order notes
     */
    function handleTemplateInsertion() {
        $(document).on('click', '#sonotes_insert_btn', function(e) {
            e.preventDefault();

            var $button = $(this);
            var content = $('#sonotes_template_select option:selected').data('content');
            var noteType = $('input[name="sonotes_note_type"]:checked').val();

            // Validate selection
            if (!content) {
                showNotification('error', sonotes_i18n.select_template_first);
                return;
            }

            // Set loading state
            setLoadingState($button, true);

            // Try to find and populate the note field
            var inserted = insertIntoNoteField(content, noteType);

            if (inserted) {
                // Success feedback
                showSuccessFeedback($button);

                // Optional: Clear selection after successful insert
                // $('#sonotes_template_select').val('').trigger('change');

            } else {
                // Error feedback
                showNotification('error', sonotes_i18n.note_field_not_found);
                setLoadingState($button, false);
            }
        });
    }

    /**
     * Insert content into the order note field
     */
    function insertIntoNoteField(content, noteType) {
        // Try different selectors for different WooCommerce versions
        var selectors = [
            '#add_order_note',                    // Classic WC
            'textarea[name="order_note"]',        // New WC
            '.wc-order-add-note textarea',        // HPOS
            '#order_note',                        // Alternative
            '.order-notes-field textarea'         // Custom themes
        ];

        var $noteField = null;

        // Find the first available note field
        for (var i = 0; i < selectors.length; i++) {
            $noteField = $(selectors[i]);
            if ($noteField.length > 0 && $noteField.is(':visible')) {
                break;
            }
        }

        if (!$noteField || $noteField.length === 0) {
            return false;
        }

        // Insert content
        $noteField.val(content).focus();

        // Try to set note type if dropdown exists
        setNoteType(noteType);

        // Trigger events for compatibility
        $noteField.trigger('change').trigger('input');

        return true;
    }

    /**
     * Set the note type in WooCommerce dropdown
     */
    function setNoteType(noteType) {
        var $noteTypeSelect = $('#order_note_type, select[name="order_note_type"]');

        if ($noteTypeSelect.length > 0) {
            var selectValue = noteType === 'customer' ? 'customer' : '';
            $noteTypeSelect.val(selectValue).trigger('change');
        }
    }

    /**
     * Handle note type selection changes
     */
    function handleNoteTypeSelection() {
        $(document).on('change', 'input[name="sonotes_note_type"]', function() {
            var type = $(this).val();
            updateNoteTypeDescription(type);
        });
    }

    /**
     * Update note type description
     */
    function updateNoteTypeDescription(type) {
        var $description = $('.sonotes-note-type-selector').next('.description');
        if ($description.length === 0) return;

        var text = type === 'customer'
            ? sonotes_i18n.customer_note_desc || 'Customer notes will be sent via email to the customer.'
            : sonotes_i18n.private_note_desc || 'Private notes are only visible to staff members.';

        $description.text(text);
    }

    /**
     * Show success feedback
     */
    function showSuccessFeedback($button) {
        var originalText = $button.text();

        $button
            .addClass('button-success')
            .text(sonotes_i18n.inserted || 'Inserted!');

        setTimeout(function() {
            $button
                .removeClass('button-success')
                .text(originalText);
            setLoadingState($button, false);
        }, 2000);
    }

    /**
     * Set loading state for button
     */
    function setLoadingState($element, isLoading) {
        if (isLoading) {
            $element.addClass('sonotes-loading').prop('disabled', true);
        } else {
            $element.removeClass('sonotes-loading').prop('disabled', false);
        }
    }

    /**
     * Show notification message
     */
    function showNotification(type, message) {
        // Try to use WordPress admin notices if available
        var $noticeArea = $('.wrap h1').first();

        if ($noticeArea.length === 0) {
            // Fallback to alert for critical messages
            alert(message);
            return;
        }

        var noticeClass = type === 'error' ? 'notice-error' : 'notice-success';
        var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
            '</button>' +
        '</div>');

        $notice.insertAfter($noticeArea);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);

        // Handle manual dismiss
        $notice.on('click', '.notice-dismiss', function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        });
    }

    /**
     * Initialize accessibility features
     */
    function initAccessibility() {
        // Add ARIA labels and descriptions
        $('#sonotes_template_select').attr('aria-describedby', 'sonotes-template-desc');

        // Add hidden description for screen readers
        if ($('#sonotes-template-desc').length === 0) {
            $('<div id="sonotes-template-desc" class="screen-reader-text">' +
                (sonotes_i18n.template_desc || 'Select a template to insert into order notes') +
            '</div>').insertAfter('#sonotes_template_select');
        }

        // Enhance keyboard navigation
        $(document).on('keydown', '#sonotes_template_select', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                $(this).trigger('change');
            }
        });

        $(document).on('keydown', '#sonotes_insert_btn', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
    }

    /**
     * Debug logging (only in development)
     */
    function debugLog(message, data) {
        if (window.console && window.console.log && sonotes_ajax.debug) {
            console.log('[Smart Order Notes] ' + message, data || '');
        }
    }

    // Export for potential extension
    window.SmartOrderNotes = {
        insertIntoNoteField: insertIntoNoteField,
        showNotification: showNotification,
        debugLog: debugLog
    };

})(jQuery);

// Localization object (will be populated by wp_localize_script)
var sonotes_i18n = sonotes_i18n || {
    select_template_first: 'Please select a template first.',
    note_field_not_found: 'Could not find the order note field. Please add the note manually.',
    inserted: 'Inserted!',
    customer_note_desc: 'Customer notes will be sent via email to the customer.',
    private_note_desc: 'Private notes are only visible to staff members.',
    template_desc: 'Select a template to insert into order notes'
};
