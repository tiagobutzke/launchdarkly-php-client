$(document).ready(function() {
    var plateAnimation,
        deliveryTimeAnimation,
        cityFlipperAnimation,
        calledActionAnimation;

    plateAnimation = new VOLO.RevealOnScroll({
        $window: $(window),
        $document: $(document),
        containerSelector: '.stats',
        targetSelector: '.stats__dish',
        smoothness: 5,
        startAtViewPercentage: 0.3,
        endAtViewPercentage: 0.07,
        targetTransitionLength: 85
    });
    plateAnimation.init();

    deliveryTimeAnimation = new VOLO.NumberScroller({
        $window: $(window),
        $document: $(document),
        scrollersSelector: '.numbers__scroller',
        fromNumber: 60,
        toNumber: 29,
        transition: 'cubic-bezier(0.270, 1.170, 1.000, 1.000)',
        startingPointGetter: function() {
            return $(".numbers__scroller").offset().top;
        }
    });
    deliveryTimeAnimation.init();

    function initCityAnimations() {
        var cities = $('.city');
        if (cities.length) {
            cities.each(function (index) {
                calledActionAnimation = new VOLO.ToggleClassOnHover({
                    $document: $(document),
                    containerSelector: '.home-section.cities .city:eq(' + index + ')',
                    targetSelector: '.city__called-action',
                    hoverClassName: 'city__called-action-show'
                });
                calledActionAnimation.init();

                cityFlipperAnimation = new VOLO.ToggleClassOnHover({
                    $document: $(document),
                    containerSelector: '.home-section.cities .city:eq(' + index + ')',
                    targetSelector: '.flipper',
                    hoverClassName: 'flipper--flipped'
                });
                cityFlipperAnimation.init();
            });
            $(document).off('page:load page:restore', initCityAnimations);
        }
    }
    $(document).on('page:load page:restore', initCityAnimations);
});
