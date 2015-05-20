var VOLO = VOLO || {};
$(document).on('ready', function () {
    window.blazy = new Blazy({
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
    $(window).bind('scroll', function() {
        var distanceY = window.pageYOffset || document.documentElement.scrollTop,
            shrinkOn = 1,
            header = $(".header");
        if (distanceY > shrinkOn) {
            header.addClass("header--white");
        } else {
            if (header.hasClass("header--white") && !header.hasClass("header-small")) {
                header.removeClass("header--white");
            }
        }
    });
});

VOLO.initCartViews = function (vendor_id, jsonCart) {
    VOLO.cartModel = VOLO.cartModel || new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        vendor_id: vendor_id
    });

    VOLO.menu = new MenuView({
        el: '.menu__main',
        cartModel: VOLO.cartModel
    });

    VOLO.cartView = new CartView({
        el: '.desktop-cart',
        model: VOLO.cartModel,
        vendor_id: vendor_id
    });
};


$(document).on('page:load', function () {
    window.blazy.revalidate();
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
