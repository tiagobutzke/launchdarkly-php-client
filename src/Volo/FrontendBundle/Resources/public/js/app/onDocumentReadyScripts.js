var VOLO = VOLO || {};
VOLO.documentReadyFunction = function() {
    console.log('document:ready');
    window.blazy = new Blazy({
        breakpoints: volo_thumbor_transformations.breakpoints,
        offset: 400
    });

    var plateAnimation,
        deliveryTimeAnimation,
        cityFlipperAnimation,
        calledActionAnimation,
        plzSelectionAnimation,
        homeTeaseFullWidth,
        headerAnimations;

    //Attention: this script should go first
    //initialize it before running any other animation script since it changes DOM elements size
    homeTeaseFullWidth = new VOLO.FullWindowHeight({
        $window: $(window),
        $document: $(document),
        targetSelector: '.fullWindowHeight'
    });
    homeTeaseFullWidth.init();

    plateAnimation = new VOLO.RevealOnScroll({
        $window: $(window),
        $document: $(document),
        containerSelector: '.home__stats',
        targetSelector: '.home__stats__dish',
        smoothness: 5,
        startAtViewPercentage: 0.3,
        endAtViewPercentage: 0.07,
        targetTransitionLength: 85
    });

    deliveryTimeAnimation = new VOLO.NumberScroller({
        $window: $(window),
        $document: $(document),
        scrollersSelector: '.numbers__scroller',
        fromNumber: 60,
        toNumber: VOLO.configuration.averageDeliveryTime,
        transition: 'cubic-bezier(0.270, 1.170, 1.000, 1.000)',
        startingPointGetter: function() {
            return $('.numbers__scroller').offset().top;
        }
    });

    plzSelectionAnimation = new VOLO.AddClassOnScroll({
        $window: $(window),
        $document: $(document),
        targetSelector: '.restaurants__tool-box, .header',
        className: 'restaurants__tool-box-sticking',
        isActiveGetter: function() {
            return $('.restaurants').length && $('.header').length;
        },
        startingPointGetter: function() {
            return $('.restaurants').offset().top - $('.header').height() + 30;
        }
    });
    plzSelectionAnimation.init();

    headerAnimations = new VOLO.HeaderAnimations({
        $window: $(window),
        $document: $(document)
    });
    headerAnimations.init();

    function initCityAnimations() {
        var cities = $('.city');
        if (cities.length) {
            cities.each(function (index) {
                calledActionAnimation = new VOLO.ToggleClassOnHover({
                    $document: $(document),
                    containerSelector: '.home__cities .city:eq(' + index + ')',
                    targetSelector: '.city__called-action',
                    hoverClassName: 'city__called-action-show'
                });
                calledActionAnimation.init();

                cityFlipperAnimation = new VOLO.ToggleClassOnHover({
                    $document: $(document),
                    containerSelector: '.home__cities .city:eq(' + index + ')',
                    targetSelector: '.flipper',
                    hoverClassName: 'flipper--flipped'
                });
                cityFlipperAnimation.init();
            });
            $(document).off('page:load page:restore', initCityAnimations);
        }
    }

    var md = new MobileDetect(window.navigator.userAgent);
    if (!md.mobile()) {
        plateAnimation.init();
        deliveryTimeAnimation.init();

        $(document).on('page:load page:restore', initCityAnimations);
    }

    //On document.ready we trigger Turbolinks page:load event
    $(document).trigger('page:load');
};
