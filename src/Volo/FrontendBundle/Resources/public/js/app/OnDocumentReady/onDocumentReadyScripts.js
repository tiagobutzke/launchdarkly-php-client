var VOLO = VOLO || {};
VOLO.documentReadyFunction = function() {
    console.log('document:ready');
    window.blazy = new Blazy({
        breakpoints: volo_thumbor_transformations.breakpoints,
        offset: 400
    });

    //should always run before initDomAnimationScripts
    VOLO.initDomChangingScripts();

    //should always run after initDomChangingScripts
    VOLO.initDomAnimationScripts();

    //On document.ready we trigger Turbolinks page:load event
    $(document).trigger('page:load');
};
