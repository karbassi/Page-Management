/**
 * 
 * jQuery Notification System
 * 
 * Version 1.0
 *  
 * Change Log:
 *   1.0 
 *      - Initial Release
 * To Do:
 *   - Sticky Notifications
 *
 * Copyright (c) 2009 Ali Karbassi
 * Dual licensed under the MIT (MIT-LICENSE.txt) 
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 */
jQuery.addNotification = function(options) {
    var defaults = {
        text: '',
        delay: 400,
        pause: 3000,
        opacity: 0.8,
        id: "notification"
    };

    var settings = $.extend({},
    defaults, options);

    // Remove any remaining ones first.
    $("#" + settings.id).remove();

    $('body').append('<div id="' + settings.id + '">' + settings.text + '</div>');

    $("#" + settings.id)
        .click(function() { $(this).remove(); })
        .css("width", $(window).width())
        .css("top", $(window).scrollTop() + "px")
        .slideDown(settings.delay)
        .animate({ opacity: settings.opacity }, settings.pause)
        .slideUp(settings.delay, function() { $(this).remove(); });
};