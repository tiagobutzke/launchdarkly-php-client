
var StickOnTop  = function(target, container, stickToTopValueGetter, startingPointGetter, endPointGetter) {
    'use strict';
    var windowCache = $(window),
        endpoint,
        targetHeight,
        sticking = false,
        afterEndPoint = false;

    function onScroll() {
        var stickToTopValue = stickToTopValueGetter(),
            scrollTop = windowCache.scrollTop();

        if ((scrollTop + stickToTopValue) > startingPointGetter()) {
            if (!sticking) {
                target.css({
                    position: 'fixed',
                    top: stickToTopValue,
                    width: container.width() + 'px'
                });
                sticking = true;
            } else {
                endpoint = endPointGetter();
                targetHeight = target.outerHeight();
                if (endpoint > scrollTop + stickToTopValue + targetHeight) {
                    if (afterEndPoint) {
                        target.css({
                            top: stickToTopValue
                        });
                        afterEndPoint = false;
                    }
                } else {
                    target.css({
                        top: endpoint - scrollTop - targetHeight
                    });
                    afterEndPoint = true;
                }
            }
        } else {
            if (sticking) {
                target.css({
                    position: '',
                    top: '',
                    width: ''
                });
                sticking = false;
            }
        }
    }
    function onResize() {
        onScroll();
        if (sticking) {
            target.width(container.width());
        } else {
            target.css({
                width: ''
            });
        }
    }
    windowCache.scroll(onScroll);
    windowCache.resize(onResize);
};
