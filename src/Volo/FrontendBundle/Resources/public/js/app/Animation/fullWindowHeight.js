var FullWindowHeight = function() {
    'use strict';
    var targets = $('.fullWindowHeight'),
        $windowCached = $(window);

    function onResize() {
        targets.height($windowCached.height());
    }
    if (targets.length) {
        onResize();
        $windowCached.resize(onResize);
    }
};
