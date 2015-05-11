
var RevealOnScroll = function(target, container, startAtViewPercentage, endAtViewPercentage, targetTransitionLength) {
    var windowCache = $(window),
        smoothness = 10,
        lastScrollValue,
        viewportHeight,
        min,
        max;

    function onResize() {
        var containerTop = container.position().top;

        viewportHeight = windowCache.height();
        min = containerTop - (viewportHeight * startAtViewPercentage);
        max = containerTop - (viewportHeight * endAtViewPercentage);
    }

    function onScroll() {
        var currentScroll = windowCache.scrollTop(),
            scrollingDown = (lastScrollValue - currentScroll) < 0,
            proportion,
            newTopPosition;

        proportion = (currentScroll - min) / (max - min);

        if(scrollingDown) {
            if(proportion > 1) {
                target.animate({ top: '0' }, smoothness);
            } else if(proportion > 0) {
                newTopPosition = (proportion * targetTransitionLength) - targetTransitionLength;
                target.animate({ top: - newTopPosition + '%' }, smoothness);
            }
        } else {
            if(proportion < 0) {
                target.animate({ top:  targetTransitionLength + '%' }, 0);
            } else if(proportion < 1) {
                newTopPosition = (proportion * targetTransitionLength) - targetTransitionLength;
                target.animate({ top: - newTopPosition + '%' }, smoothness);
            }
        }

        lastScrollValue = currentScroll;
    }

    target.css({ top: targetTransitionLength + '%' });
    onResize();
    onScroll();

    windowCache.scroll(onScroll);
    windowCache.resize(onResize);
};



