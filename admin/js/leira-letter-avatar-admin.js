(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(function () {
        $('.leira-letter-avatar-color-field').each(function (i, item) {
            item = $(item);
            var palettes = item.data('picker_palettes');
            item.wpColorPicker({
                // you can declare a default color here,
                // or in the data-default-color attribute on the input
                defaultColor: item.data('picker_default'),
                // a callback to fire whenever the color changes to a valid color
                change: function (event, ui) {
                },
                // a callback to fire when the input is emptied or an invalid color
                clear: function () {
                },
                // hide the color picker controls on load
                //hide: true,
                // set  total width
                //width: 200,
                // show a group of common colors beneath the square
                // or, supply an array of colors to customize further
                //palettes:  ['#fc91ad', '#37c5ab', '#fd9a00', '#794fcf', '#19C976']
                palettes: (typeof palettes === 'string' && palettes) ? palettes.split(',') : true
            });
        });


        /**
         * Handle footer rate us link
         */
        $('a.leira-letter-avatar-admin-rating-link').click(function () {
            $.ajax({
                url: wp.ajax.settings.url,
                type: 'post',
                data: {
                    action: 'leira_letter_avatar_footer_rated',
                    nonce: $(this).data('nonce')
                },
                success: function () {

                }
            });
            $(this).parent().text($(this).data('rated'));
        });
    });

})(jQuery);
