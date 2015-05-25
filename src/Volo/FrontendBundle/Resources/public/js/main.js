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

VOLO.initCartViews = function (cartModel) {
    if (_.isObject(VOLO.menu)) {
        VOLO.menu.remove();
    }
    VOLO.menu = new MenuView({
        el: '.menu__main',
        cartModel: cartModel,
        $header: $('.header')
    });

    if (_.isObject(VOLO.cartView)) {
        VOLO.cartView.remove();
    }
    VOLO.cartView = new CartView({
        el: '.desktop-cart',
        model: cartModel
    });
};

VOLO.initCheckoutViews = function (cartModel) {
    if (_.isObject(VOLO.checkoutView)) {
        VOLO.checkoutView.remove();
    }
    VOLO.checkoutView = new CheckoutView({
        el: '.desktop-cart',
        model: cartModel
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
                $(document).trigger('intl:ready');
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

$(document).on('page:load', function () {
    window.blazy.revalidate();

    if ($('.menu__main').length > 0) {
        VOLO.initCartViews(VOLO.initCartModel(VOLO.jsonCart));
        if (_.isObject(window.Intl)) {
            try {
                new Intl.NumberFormat(VOLO.configuration.locale);
                VOLO.cartView.render();
            } catch (err) {
            }
        }
    }

    if ($('.checkout__main').length > 0) {
        VOLO.initCheckoutViews(VOLO.initCartModel(VOLO.jsonCart));
        if (_.isObject(window.Intl)) {
            try {
                new Intl.NumberFormat(VOLO.configuration.locale);
                VOLO.checkoutView.render();
            } catch (err) {
            }
        }
    }
});

$(document).on('page:before-unload', function () {
    if (_.isObject(VOLO.menu)) {
        VOLO.menu.remove();
    }
    if (_.isObject(VOLO.cartView)) {
        VOLO.cartView.remove();
    }
    if (_.isObject(VOLO.checkoutView)) {
        VOLO.checkoutView.remove();
    }
});

$(document).on('intl:ready', function () {
    if ($('.menu__main').length > 0 && _.isObject(VOLO.cartView)) {
        VOLO.cartView.render();
    }
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
