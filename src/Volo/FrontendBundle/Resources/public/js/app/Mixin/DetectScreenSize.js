/**
 * VOLO.DetectScreenSizeMixin
 * Mixin which provides screen size detection
 */
var VOLO = VOLO || {};
VOLO.DetectScreenSizeMixin = {
    $window: $(window),

    isBelowMediumScreen: function() {
        return this.$window.width() < VOLO.configuration.smallScreenMaxSize;
    }
};
