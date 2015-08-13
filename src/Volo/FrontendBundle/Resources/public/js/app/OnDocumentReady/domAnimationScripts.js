//Attention: function for calling script which animate dom elements
//should run after dom changing scripts - VOLO.initDomChangingScripts
var VOLO = VOLO || {};
VOLO.initDomAnimationScripts = function() {
    var plateAnimation,
        deliveryTimeAnimation,
        cityFlipperAnimation,
        calledActionAnimation,
        plzSelectionAnimation,
        isMobile = new MobileDetect(window.navigator.userAgent).mobile(),
        $window = $(window),
        $document = $(document);

    function initCityAnimations() {
        var cities = $('.city');
        if (cities.length) {
            cities.each(function (index) {
                calledActionAnimation = new VOLO.ToggleClassOnHover({
                    $document: $document,
                    containerSelector: '.home__cities .city:eq(' + index + ')',
                    targetSelector: '.city__called-action',
                    hoverClassName: 'city__called-action-show'
                });
                calledActionAnimation.init();

                cityFlipperAnimation = new VOLO.ToggleClassOnHover({
                    $document: $document,
                    containerSelector: '.home__cities .city:eq(' + index + ')',
                    targetSelector: '.flipper',
                    hoverClassName: 'flipper--flipped'
                });
                cityFlipperAnimation.init();
            });
            $document.off('page:load page:restore', initCityAnimations);
        }
    }

    plateAnimation = new VOLO.RevealOnScroll({
        $window: $window,
        $document: $document,
        containerSelector: '.home__stats',
        targetSelector: '.home__stats__dish',
        smoothness: 5,
        startAtViewPercentage: 0.3,
        endAtViewPercentage: 0.07,
        targetTransitionLength: 85
    });

    deliveryTimeAnimation = new VOLO.NumberScroller({
        $window: $window,
        $document: $document,
        scrollersSelector: '.numbers__scroller',
        fromNumber: 60,
        toNumber: VOLO.configuration.averageDeliveryTime,
        transition: 'cubic-bezier(0.270, 1.170, 1.000, 1.000)',
        startingPointGetter: function() {
            return $('.numbers__scroller').offset().top;
        }
    });

    plzSelectionAnimation = new VOLO.AddClassOnScroll({
        $window: $window,
        $document: $document,
        targetSelector: '.restaurants__tool-box, .header',
        className: 'restaurants__tool-box--sticking',
        isActiveGetter: function() {
            return $('.restaurants__list').length && $('.header').length && !$('body').hasClass('restaurants--no-address');
        },
        startingPointGetter: function() {
            if ($document.find('body.show-ios-smart-banner').length) {
                return $('.restaurants__list').offset().top - $('.header').height() - $('.ios-smart-banner').height() + 30;
            } else {
                return $document.find('.header').outerHeight();
            }
        }
    });

    plzSelectionAnimation.init();

    if (!isMobile) {
        plateAnimation.init();
        deliveryTimeAnimation.init();

        $document.off('page:load page:restore', initCityAnimations).on('page:load page:restore', initCityAnimations);
    }
};
