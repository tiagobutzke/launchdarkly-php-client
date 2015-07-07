var VOLO = VOLO || {};

VOLO.views = []; //all create views should go here
VOLO.gtmViews = []; //all views, which needs GTM should go here

$(document).ready(VOLO.documentReadyFunction);

VOLO.createCartModel = function (jsonCart) {
    VOLO.cartModel = VOLO.cartModel || new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        parse: true
    });

    return VOLO.cartModel;
};

VOLO.createLocationModel = function (jsonLocation) {
    VOLO.locationModel = VOLO.locationModel || new LocationModel(jsonLocation);

    return VOLO.locationModel;
};

VOLO.createCheckoutModel = function (cartModel, locationModel, vendorId) {
    VOLO.checkoutModel = new CheckoutModel({}, {cartModel: cartModel});

    cartModel.getCart(vendorId).set('location', locationModel.attributes);

    return VOLO.checkoutModel;
};

VOLO.createCartViews = function (cartModel, locationModel, gtmService) {
    var $header = $('.header'),
        menuView = new MenuView({
            el: '.menu__main',
            cartModel: cartModel,
            locationModel: locationModel,
            $header: $header,
            gtmService: gtmService
        }),
        cartView = new CartView({
            el: '#cart',
            model: cartModel,
            locationModel: locationModel,
            $header: $header,
            $menuMain: $('.menu__main'),
            $window: $(window),
            gtmService: gtmService,
            smallScreenMaxSize: VOLO.configuration.smallScreenMaxSize
        }),
        cartErrorModalView = new CartErrorModalView({
            el: '#cartCalculationErrorModal',
            model: cartModel
        });

    VOLO.views.push(menuView, cartView, cartErrorModalView);
    VOLO.gtmViews.push(menuView, cartView);

    return {
        cartView: cartView,
        menuView: menuView,
        cartErrorModalView: cartErrorModalView
    };
};

VOLO.createCheckoutViews = function (cartModel, checkoutModel, locationModel) {
    var views = {},
        deliveryCheck = new DeliveryCheck();

    views.checkoutCartView = new CheckoutCartView({
        el: '#cart',
        model: cartModel,
        $header: $('.header'),
        locationModel: locationModel,
        $menuMain: $('.checkout__main'),
        $window: $(window)
    });

    if ($('.time-picker').length > 0) {
        views.timePickerView = new TimePickerView({
            el: '.time-picker',
            model: cartModel
        });
    }
    if ($('.voucher-component').length > 0) {
        views.voucherView = new VoucherView({
            el: '.voucher-component',
            model: cartModel
        });
    }
    if ($('#finish-and-pay').length > 0) {
        views.checkoutButtonView = new CheckoutButtonView({
            el: '.checkout-button',
            model: checkoutModel
        });
    }
    if ($('.checkout-delivery-component').length > 0) {
        views.checkoutDeliveryInformationView = new CheckoutDeliveryInformationView({
            el: '.checkout-delivery-component',
            model: checkoutModel
        });
    }
    if ($('#delivery_information_form').length > 0) {
        views.checkoutDeliveryValidationView = new CheckoutDeliveryValidationView({
            el: '#delivery_information_form',
            deliveryCheck: deliveryCheck,
            locationModel: locationModel,
            geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1]),
            postalCodeGeocodingService: new PostalCodeGeocodingService(VOLO.configuration.locale.split('_')[1])
        });
    }

    if ($('#payment_form').length > 0) {
        views.paymentFormView = new PaymentFormView({
            el: '#payment_form',
            model: checkoutModel
        });
    }

    if ($('#contact_information_form').length > 0 ) {
        views.checkoutInformationValidationFormView = new VOLO.CheckoutContactInformationView({
            el: '#contact_information_form'
        });
    }

    if ($('.checkout__payments-wrapper').length > 0) {
        views.paymentTypeView = new PaymentTypeView({
            el: '.checkout__payments-wrapper',
            checkoutModel: checkoutModel
        });
    }

    views.cartErrorModalView = new CartErrorModalView({
        el: '#cartCalculationErrorModal',
        model: cartModel
    });


    VOLO.views = VOLO.views.concat(_.values(views));
    return views;
};

VOLO.renderCheckoutViews = function (checkoutViews) {
    checkoutViews.checkoutCartView.render();

    if (checkoutViews.timePickerView) {
        checkoutViews.timePickerView.render();
    }
    if (checkoutViews.voucherView) {
        checkoutViews.voucherView.render();
    }
    if (checkoutViews.checkoutButtonView) {
        checkoutViews.checkoutButtonView.render();
    }
    if (checkoutViews.checkoutDeliveryInformationView) {
        checkoutViews.checkoutDeliveryInformationView.render();
    }
};

VOLO.initIntl = function (locale, currency_symbol) {
    var deferred = $.Deferred();

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
                deferred.resolve();
            });
        });
    } else {
        VOLO.initCurrencyFormat(locale, currency_symbol);
        deferred.resolve();
    }

    return deferred;
};

VOLO.initCurrencyFormat = function (locale, currency_symbol) {
    VOLO.formatCurrency = new Intl.NumberFormat(locale, {
        style: 'currency',
        minimumFractionDigits: 2,
        currency: currency_symbol
    }).format;
    VOLO.formatNumber = new Intl.NumberFormat(locale).format;
};

VOLO.createHomeSearchView = function (locationModel) {
    var homeSearchView = new HomeSearchView({
        el: '.teaser__form',
        model: locationModel,
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });

    VOLO.views.push(homeSearchView);
    return homeSearchView;
};

VOLO.createLoginButtonView = function () {
    var loginButtonView = new LoginButtonView({
        el: '.header__account'
    });

    VOLO.views.push(loginButtonView);
    return loginButtonView;
};

VOLO.createExistingUserLoginView = function () {
    var existingUserLoginView = new ExistingUserLoginView({
        el: '#show-login-overlay'
    });

    VOLO.views.push(existingUserLoginView);
    return existingUserLoginView;
};

VOLO.createCartIconView = function () {
    var cartIconView = new VendorCartIconView({
        el: '.header__cart'
    });

    VOLO.views.push(cartIconView);
    return cartIconView;
};

VOLO.createVendorsListSearchView = function () {
    var vendorSearchView = new VendorsSearchView({
        el: '.restaurants__tool-box',
        model: VOLO.locationModel,
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });

    VOLO.views.push(vendorSearchView);
    return vendorSearchView;
};

VOLO.initGTMService = function(checkoutModel, configuration, checkoutDeliveryValidationView, checkoutInformationValidationFormView) {
    if (_.isObject(VOLO.GTMServiceInstance)) {
        VOLO.GTMServiceInstance.unbind();
    }

    VOLO.GTMServiceInstance = new VOLO.GTMService({
        dataLayer: dataLayer,
        checkoutModel: checkoutModel,
        sessionId: configuration.sessionId,
        checkoutDeliveryValidationView: checkoutDeliveryValidationView,
        checkoutInformationValidationFormView: checkoutInformationValidationFormView
    });

    return VOLO.GTMServiceInstance;
};

VOLO.createRegistrationEvent = function ($button, loginButtonView) {
    var registerClick = $button.click(function () {
        var customerData = $button.data();
        customerData = customerData ? customerData.object : null;

        loginButtonView.showRegistrationModal(customerData);
    });

    views.push(registerClick);
    return registerClick;
};

VOLO.OrderTracking = function () {
    var $statusWrapper = $('.order-status-wrapper'),
        code;

    if ($statusWrapper.length === 0) {
        return;
    }

    code = $statusWrapper.data().order_code;

    $.ajax({
        url: Routing.generate('order_tracking', {orderCode: code, partial: 1})
    }).done(function (response) {
        console.log('order tracking update ', (new Date()).toString());
        $('.tracking-steps').html(response);
    });
};

VOLO.doBootstrap = function(configuration) {
    var locationModel = VOLO.createLocationModel(VOLO.jsonLocation),
        gtmCheckoutValidationView, cartModel, checkoutModel, checkoutInformationValidationFormView;

    if ($('.menu__main').length > 0) {
        cartModel = VOLO.createCartModel(VOLO.jsonCart);

        var cartViews = VOLO.createCartViews(cartModel, locationModel, VOLO.GTMServiceInstance);
        cartViews.cartView.render();
    }

    var $checkoutMain = $('.checkout__main');
    if ($checkoutMain.length > 0) {
        cartModel = VOLO.createCartModel(VOLO.jsonCart);
        checkoutModel = VOLO.createCheckoutModel(cartModel, locationModel, $checkoutMain.data('vendor_id'));

        var checkoutViews = VOLO.createCheckoutViews(cartModel, checkoutModel, locationModel);
        gtmCheckoutValidationView = checkoutViews.checkoutDeliveryValidationView;
        checkoutInformationValidationFormView = checkoutViews.checkoutInformationValidationFormView;
        VOLO.renderCheckoutViews(checkoutViews);
    }

    if ($('.teaser__form').length > 0) {
        var homeSearchView = VOLO.createHomeSearchView(locationModel);
        homeSearchView.render();
    }

    if ($('.header__account').length > 0) {
        var loginButtonView = VOLO.createLoginButtonView(),
            $registerButton = $('.create_account__login__button');

        if ($registerButton.length > 0) {
            VOLO.createRegistrationEvent($registerButton, loginButtonView);
        }
    }

    if ($('.header__cart').length > 0) {
        var cartIconView = VOLO.createCartIconView();
        cartIconView.render();
    }

    if ($('.restaurants__tool-box').length > 0) {
        var vendorSearchView = VOLO.createVendorsListSearchView();
        vendorSearchView.render();
    }

    if ($('.order-status-wrapper').length > 0) {
        if (!_.isNull(VOLO.orderTrackingInterval)) {
            clearInterval(VOLO.orderTrackingInterval);
        }
        VOLO.orderTrackingInterval = setInterval(VOLO.OrderTracking, 60000);
    }

    if ($('#show-login-overlay').length > 0) {
        var existingUserLoginView = VOLO.createExistingUserLoginView();
        existingUserLoginView.render();
    }

    var GTMServiceInstance = VOLO.initGTMService(
        checkoutModel,
        configuration,
        gtmCheckoutValidationView,
        checkoutInformationValidationFormView
    );

    _.invoke(VOLO.gtmViews, 'setGtmService', GTMServiceInstance);
};

$(document).on('page:load page:restore', function () {
    console.log('page:load');
    window.blazy.revalidate();

    VOLO.initIntl(VOLO.configuration.locale.replace('_', '-'), VOLO.configuration.currencySymbol).done(VOLO.doBootstrap(VOLO.configuration));
});

$(document).on('page:before-unload', function () {
    console.log('page:before-unload');

    _.invoke(VOLO.views, 'unbind');

    if (_.isObject(VOLO.orderTrackingInterval)) {
        clearInterval(VOLO.orderTrackingInterval);
        VOLO.orderTrackingInterval = null;
    }

    if (_.isObject(VOLO.GTMServiceInstance)) {
        VOLO.GTMServiceInstance.unbind();
        delete VOLO.GTMServiceInstance;
    }

    dataLayer = [];
});

Turbolinks.pagesCached(10);

Turbolinks.enableProgressBar();
