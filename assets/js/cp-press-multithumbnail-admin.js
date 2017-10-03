var MediaModal = function (options) {
    'use strict';
    this.settings = {
        calling_selector: false,
        cb: function (attachment) {}
    };
    var that = this,
        frame = wp.media.frames.file_frame;

    this.attachEvents = function attachEvents() {
        jQuery(this.settings.calling_selector).on('click', this.openFrame);
    };

    this.openFrame = function openFrame(e) {
        e.preventDefault();

        // Create the media frame.
        frame = wp.media.frames.file_frame = wp.media({
            title: jQuery(this).data('uploader_title'),
            button: {
                text: jQuery(this).data('uploader_button_text')
            },
            library : {
                type : 'image'
            }
        });

        // Set filterable state to uploaded to get select to show (setting this
        // when creating the frame doesn't work)
        frame.on('toolbar:create:select', function(){
            frame.state().set('filterable', 'uploaded');
        });

        // When an image is selected, run the callback.
        frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            var attachment = frame.state().get('selection').first().toJSON();
            that.settings.cb(attachment);
        });

        frame.on('open activate', function() {
            // Get the link/button/etc that called us
            var $caller = jQuery(that.settings.calling_selector);

            // Select the thumbnail if we have one
            if ($caller.data('thumbnail_id')) {
                var Attachment = wp.media.model.Attachment;
                var selection = frame.state().get('selection');
                selection.add(Attachment.get($caller.data('thumbnail_id')));
            }
        });

        frame.open();
    };

    this.init = function init() {
        this.settings = jQuery.extend(this.settings, options);
        this.attachEvents();
    };
    this.init();

    return this;
};

window.MultiThumbnails = {

    setThumbnailHTML: function(html, id, post_type){
        jQuery('#' + post_type + '-' + id).html(html);
    },

    setThumbnailID: function(thumb_id, id, post_type){
        var field = jQuery('input[value=_' + post_type + '_' + id + '_thumbnail_id]', '#list-table');
        if ( field.size() > 0 ) {
            jQuery('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(thumb_id);
        }
    },

    removeThumbnail: function(id, post_type, nonce){
        jQuery.post(ajaxurl, {
                action:'set-' + post_type + '-' + id + '-thumbnail', post_id: jQuery('#post_ID').val(), thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
            }, function(str){
                if ( str == '0' ) {
                    alert( setPostThumbnailL10n.error );
                } else {
                    MultiThumbnails.setThumbnailHTML(str, id, post_type);
                }
            }
        );
    },


    setAsThumbnail: function(thumb_id, id, post_type, nonce){
        var $link = jQuery('a#set-' + post_type + '-' + id + '-thumbnail');
        $link.data('thumbnail_id', thumb_id);
        $link.text( setPostThumbnailL10n.saving );
        jQuery.post(ajaxurl, {
                action:'set-' + post_type + '-' + id + '-thumbnail', post_id: post_id, thumbnail_id: thumb_id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
            }, function(str){
                var win = window.dialogArguments || opener || parent || top;
                $link.text( setPostThumbnailL10n.setThumbnail );
                if ( str == '0' ) {
                    alert( setPostThumbnailL10n.error );
                } else {
                    $link.show();
                    $link.text( setPostThumbnailL10n.done );
                    $link.fadeOut( 2000, function() {
                        jQuery('tr.' + post_type + '-' + id + '-thumbnail').hide();
                    });
                    win.MultiThumbnails.setThumbnailID(thumb_id, id, post_type);
                    win.MultiThumbnails.setThumbnailHTML(str, id, post_type);
                }
            }
        );
    }
}