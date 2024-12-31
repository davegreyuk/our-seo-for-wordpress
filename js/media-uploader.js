jQuery(document).ready(function ($) {
    $('.upload-button, .seo-wp-upload-image').on('click', function (e) {
        e.preventDefault();

        // Retrieve the target input field ID or selector
        const target = $(this).data('target');
        if (!target) {
            console.error('Missing data-target attribute for upload button.');
            return;
        }

        // Initialize the WordPress media frame
        const frame = wp.media({
            title: 'Select or Upload Image',
            button: {
                text: 'Use Image',
            },
            multiple: false,
        });

        // Handle image selection
        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            $(target).val(attachment.url); // Update the target input field with the image URL
        });

        frame.open();
    });

    $('.seo-upload-button').on('click', function () {
        const target = $(this).data('target');
        const fileFrame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use This Image'
            },
            multiple: false
        });

        fileFrame.on('select', function () {
            const attachment = fileFrame.state().get('selection').first().toJSON();
            $(target).val(attachment.url);
        });

        fileFrame.open();
    });

});
