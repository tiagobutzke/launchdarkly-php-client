var VOLO = VOLO || {};
$(document).on('ready page:load', function () {
    setTimeout(function() {
            new Blazy({
                breakpoints: [{
                    width: 300, // max-width
                    src: 'data-src-300',
                    mode: 'viewport'
                },{
                    width: 400, // max-width
                    src: 'data-src-400',
                    mode: 'viewport'
                }, {
                    width: 600, // max-width
                    src: 'data-src-600',
                    mode: 'viewport'
                }, {
                    width: 800, // max-width
                    src: 'data-src-800',
                    mode: 'viewport'
                }, {
                    width: 1000, // max-width
                    src: 'data-src-1000',
                    mode: 'viewport'
                }, {
                    width: 1200, // max-width
                    src: 'data-src-1200',
                    mode: 'viewport'
                },{
                    width: 1400, // max-width
                    src: 'data-src-1400',
                    mode: 'viewport'
                }, {
                    width: 99999999, // max-width
                    src: 'data-src-biggest',
                    mode: 'viewport'
                }]
            });
    }, 100);
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
