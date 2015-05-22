var VOLO = VOLO || {};
$(document).on('ready', function () {
    window.blazy = new Blazy({
        breakpoints: volo_thumbor_transformations.breakpoints
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

VOLO.initCheckoutViews = function (vendorId, jsonCart) {
    VOLO.cartModel = VOLO.cartModel || new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        vendor_id: vendorId
    });

    VOLO.checkoutView = new CheckoutView({
        el: '.desktop-cart',
        model: VOLO.cartModel,
        vendor_id: vendorId
    });
};

$(document).on('page:load', function () {
    window.blazy.revalidate();
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
