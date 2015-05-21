var Flipper = function(container) {
    'use strict';
    var flipper = container.find('.flipper');

    if (flipper.length) {
        $(container).mouseover(function() {
            if (!flipper.hasClass('flipper--flipped')) {
                flipper.addClass('flipper--flipped');
            }
        })
            .mouseout(function() {
                if (flipper.hasClass('flipper--flipped')) {
                    flipper.removeClass('flipper--flipped');
                }
            });
    }
};
