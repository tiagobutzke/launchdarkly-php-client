
var NumberScroller = function(scrollers, startingPointGetter, toNumber) {
    'use strict';
    var windowCache = $(window),
        speedFactor = (110 - toNumber) / 100;

    function onScroll() {
        var speedValue = 3 * speedFactor;
        if ((windowCache.scrollTop() + windowCache.height()) > startingPointGetter()) {
            scrollers.css({
                top: '-' + (60 - toNumber) + '00%',
                '-webkit-transition': 'top ' + speedValue + 's cubic-bezier(0.270, 1.170, 1.000, 1.000)',
                transition: 'top ' + speedValue + 's cubic-bezier(0.270, 1.170, 1.000, 1.000)'
            });

            windowCache.off('scroll', onScroll);
        }
    }
    windowCache.on('scroll', onScroll);
};
