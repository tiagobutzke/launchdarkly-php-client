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

VOLO.initCheckoutModel = function (cartModel) {
    VOLO.checkoutModel = new CheckoutModel({}, {cartModel: cartModel});

    return VOLO.checkoutModel;
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

VOLO.initCheckoutViews = function (cartModel, checkoutModel, deliveryCheck) {
    if (_.isObject(VOLO.CheckoutCartView)) {
        VOLO.CheckoutCartView.unbind();
    }
    if (_.isObject(VOLO.timePickerView)) {
        VOLO.timePickerView.unbind();
    }
    if (_.isObject(VOLO.voucherView)) {
        VOLO.voucherView.unbind();
    }
    if (_.isObject(VOLO.checkoutDeliveryInformationView)) {
        VOLO.checkoutDeliveryInformationView.unbind();
    }
    VOLO.CheckoutCartView = new CheckoutCartView({
        el: '.desktop-cart',
        model: cartModel,
        $header: $('.header'),
        $menuMain: $('.checkout__main'),
        $window: $(window)
    });

    if ($('.time-picker').length > 0) {
        VOLO.timePickerView = new TimePickerView({
            el: '.time-picker',
            model: cartModel
        });
    }
    if ($('.voucher-component').length > 0) {
        VOLO.voucherView = new VoucherView({
            el: '.voucher-component',
            model: cartModel
        });
    }
    if ($('#finish-and-pay').length > 0) {
        VOLO.checkoutButtonView = new CheckoutButtonView({
            el: '.checkout-button',
            model: checkoutModel
        });
    }
    if ($('.checkout-delivery-component').length > 0) {
        VOLO.checkoutDeliveryInformationView = new CheckoutDeliveryInformationView({
            el: '.checkout-delivery-component',
            model: checkoutModel
        });
    }
    if ($('#delivery_information_form').length > 0) {
        if (VOLO.checkoutDeliveryInformationView) {
            VOLO.checkoutDeliveryInformationView.unbind();
        }
        VOLO.checkoutDeliveryInformationView = new CheckoutDeliveryValidationView({
            el: '#delivery_information_form',
            deliveryCheck: deliveryCheck,
            geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
        });
    }
};

VOLO.initIntl = function (locale, currency_symbol) {
    if (_.isUndefined(window.Intl)) {
        $.ajax({
           url: '/js/dist/intl.js'
        }).done(function () {
            $.ajax({
                url: '/js/dist/intl/locale/' + locale + '.json',
                dataType: 'json'
            }).done(function (data) {
                IntlPolyfill.__addLocaleData(data);
                VOLO.initCurrencyFormat(locale, currency_symbol);
                console.log('INTL polyfill loaded');
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
    if (VOLO.homeSearchView) {
        VOLO.homeSearchView.unbind();
    }
    VOLO.homeSearchView = new HomeSearchView({
        el: '.teaser__form',
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });
};

VOLO.initVendorsListSearch = function() {
    VOLO.vendorSearchView = new VendorsSearchView({
        el: '.restaurants__tool-box',
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });
};

$(document).ready(function () {
    // On document.ready we trigger Turbolinks page:load event
    $(document).trigger('page:load');
});

$(document).on('page:load page:restore', function () {
    console.log('page:load');
    window.blazy.revalidate();

    if (!_.isFunction(VOLO.formatCurrency)) {
        // Browser doesn't have INTL support, stop the rendering here.
        // The polyfill is loaded asynchronously and will trigger page:load again
        return;
    }

    if ($('.menu__main').length > 0) {
        VOLO.initCartModel(VOLO.jsonCart);
        VOLO.initCartViews(VOLO.cartModel);
        VOLO.cartView.render();
    }

    if ($('.checkout__main').length > 0) {
        VOLO.initCartModel(VOLO.jsonCart);
        VOLO.initCheckoutModel(VOLO.cartModel);
        VOLO.initCheckoutViews(VOLO.cartModel, VOLO.checkoutModel, new DeliveryCheck());
        VOLO.CheckoutCartView.render();

        if (_.isObject(VOLO.timePickerView)) {
            VOLO.timePickerView.render();
        }
        if (_.isObject(VOLO.voucherView)) {
            VOLO.voucherView.render();
        }
        if (_.isObject(VOLO.checkoutButtonView)) {
            VOLO.checkoutButtonView.render();
        }
        if (_.isObject(VOLO.checkoutDeliveryInformationView)) {
            VOLO.checkoutDeliveryInformationView.render();
        }
    }

    if ($('.teaser__form').length > 0) {
        VOLO.initHomeSearch();
        VOLO.homeSearchView.render();
    }

    if($('.restaurants__tool-box').length > 0) {
        VOLO.initVendorsListSearch();
        VOLO.vendorSearchView.render();
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
    if (_.isObject(VOLO.CheckoutCartView)) {
        VOLO.CheckoutCartView.unbind();
    }
    if (_.isObject(VOLO.homeSearchView)) {
        VOLO.homeSearchView.unbind();
    }
    if (_.isObject(VOLO.timePickerView)) {
        VOLO.timePickerView.unbind();
    }
    if (_.isObject(VOLO.voucherView)) {
        VOLO.voucherView.unbind();
    }
    if (_.isObject(VOLO.vendorSearchView)) {
        VOLO.vendorSearchView.unbind();
    }
    if (_.isObject(VOLO.checkoutButtonView)) {
        VOLO.checkoutButtonView.unbind();
    }
    if (_.isObject(VOLO.checkoutDeliveryInformationView)) {
        VOLO.checkoutDeliveryInformationView.unbind();
    }
});

Turbolinks.pagesCached(10);

Turbolinks.enableProgressBar();
