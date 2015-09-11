//Attention: function for calling scripts which change the dom structure
//should run before dom animations - VOLO.initDomAnimationScripts
var VOLO = VOLO || {};
VOLO.initDomChangingScripts = function() {
    var homeTeaseFullWidth,
        headerAnimations,
        $window = $(window),
        $document = $(document),
        isMobile = new MobileDetect(window.navigator.userAgent).mobile(),
        $body = $('body');

    if (isMobile) {
        $body.addClass('is-mobile');
    } else {
        $body.addClass('not-mobile');
    }

    homeTeaseFullWidth = new VOLO.FullWindowHeight({
        $window: $window,
        $document: $document,
        targetSelector: '.fullWindowHeight'
    });

    headerAnimations = new VOLO.HeaderAnimations({
        $window: $window,
        $document: $document
    });

    homeTeaseFullWidth.init();
    headerAnimations.init();
};
