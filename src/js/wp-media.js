jQuery(document).ready(function ($) {
    $('.settings-media-browse').on('click', function (event) {
        event.preventDefault();

        var self = $(this);

        var file_frame = wp.media.frames.file_frame = wp.media({
            title: self.data('uploader_title'),
            button: {
                text: self.data('uploader_button_text'),
            },
            multiple: false
        });

        file_frame.on('select', function () {
            attachment = file_frame.state().get('selection').first().toJSON();
            self.prev('.settings-media-url').val(attachment.url).change();
        });

        file_frame.open();
    });
});
