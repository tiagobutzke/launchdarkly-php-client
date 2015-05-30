var VOLO = VOLO || {};
$(document).ready(function () {
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

VOLO.initCartModel = function (jsonCart) {
    VOLO.cartModel = VOLO.cartModel || new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        parse: true
    });

    return VOLO.cartModel;
};

VOLO.initView = function(callback, jsonCart) {
    callback(VOLO.initCartModel(jsonCart));
    if (_.isObject(window.Intl)) {
        try {
            new Intl.NumberFormat(VOLO.configuration.locale.replace('_', '-'));
            //VOLO.checkoutView.render();
        } catch (err) {
            console.log(err);
        }
    }
};

VOLO.initCartViews = function (cartModel) {
    var $header = $('.header');
    
    if (_.isObject(VOLO.menu)) {
        VOLO.menu.unbind();
    }
    VOLO.menu = new MenuView({
        el: '.menu__main',
        cartModel: cartModel,
        $header: $header
    });

    if (_.isObject(VOLO.cartView)) {
        VOLO.cartView.unbind();
    }
    VOLO.cartView = new CartView({
        el: '.desktop-cart',
        model: cartModel,
        $header: $header,
        $menuMain: $('.menu__main'),
        $window: $(window)
    });
};

VOLO.initCheckoutViews = function (cartModel) {
    if (_.isObject(VOLO.checkoutView)) {
        VOLO.checkoutView.unbind();
    }
    VOLO.checkoutView = new CheckoutView({
        el: '.desktop-cart',
        model: cartModel,
        $header: $('.header'),
        $menuMain: $('.checkout__main'),
        $window: $(window)
    });
};

VOLO.initIntl = function (userLocale, locale, currency_symbol) {
    if (_.isUndefined(window.Intl)) {
        $.ajax({
           url: '/js/dist/intl.js'
        }).done(function () {
            $.ajax({
                url: '/js/dist/intl/locale/' + userLocale + '.json'
            }).done(function (data) {
                IntlPolyfill.__addLocaleData(data);
                VOLO.initCurrencyFormat(locale, currency_symbol);
                $(document).trigger('page:load');
            });
        });
    } else {
        VOLO.initCurrencyFormat(locale, currency_symbol);
    }
};

VOLO.initCurrencyFormat = function (locale, currency_symbol) {
    VOLO.formatCurrency = new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency_symbol
    }).format;
    VOLO.formatNumber = new Intl.NumberFormat(locale).format;
};

VOLO.initHomeSearch = function() {
    VOLO.homeSearchView = new HomeSearchView({
        el: '.teaser__search',
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });

    VOLO.homeSearchView.render();
};


$(document).ready(function () {
    // On document.ready we trigger Turbolinks page:load event
    $(document).trigger('page:load');
});

$(document).on('page:load', function () {
    console.log('page:load');
    window.blazy.revalidate();

    if ($('.menu__main').length > 0) {
        VOLO.initView(VOLO.initCartViews, VOLO.jsonCart);
        VOLO.cartView.render();
    }

    if ($('.checkout__main').length > 0) {
        VOLO.initView(VOLO.initCheckoutViews, VOLO.jsonCart);
        VOLO.checkoutView.render();
    }
});
$(document).on('page:restore', function () {
    console.log('page:restore');
    window.blazy.revalidate();

    if ($('.menu__main').length > 0) {
        VOLO.initView(VOLO.initCartViews, VOLO.jsonCart);
        VOLO.cartView.render();
    }

    if ($('.checkout__main').length > 0) {
        VOLO.initView(VOLO.initCheckoutViews, VOLO.jsonCart);
        VOLO.checkoutView.render();
    }
});

$(document).on('page:before-unload', function () {
    console.log('page:before-unload');
    if (_.isObject(VOLO.menu)) {
        VOLO.menu.unbind();
    }
    if (_.isObject(VOLO.cartView)) {
        VOLO.cartView.unbind();
    }
    if (_.isObject(VOLO.checkoutView)) {
        VOLO.checkoutView.unbind();
    }
});

Turbolinks.pagesCached(10);

Turbolinks.enableProgressBar();
