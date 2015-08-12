/**
 * VOLO.DetectIE
 * Mixin which provides detection of IE 9, 10 and 11
 */
var VOLO = VOLO || {};
VOLO.DetectIE = {
    isIE: function() {
        return new RegExp('/MSIE|Trident|Edge/').test(window.navigator.userAgent) ;
    }
};
