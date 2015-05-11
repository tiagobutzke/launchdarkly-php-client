
var NumberScroller = function(scrollers, startingPointGetter, toNumber) {

    var windowCache = $(window),
        speedFactor = (110 - toNumber) / 100;

    function onScroll() {
        if((windowCache.scrollTop() + windowCache.height()) > startingPointGetter()) {
            scrollers.css({
                top: '-' + (60 - toNumber) + '00%',
                transition:'top ' + (3 * speedFactor) + 's cubic-bezier(0.270, 1.170, 1.000, 1.000)'
            });

            windowCache.off('scroll', onScroll);
        }
    }

    windowCache.on('scroll', onScroll);
};



