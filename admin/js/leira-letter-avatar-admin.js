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
        $('.leira-letter-avatar-color-field').wpColorPicker({
            // you can declare a default color here,
            // or in the data-default-color attribute on the input
            defaultColor: '#fc91ad',
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
            palettes: ['#fc91ad', '#37c5ab', '#fd9a00', '#794fcf', '#19C976']
        });
    });

})(jQuery);
