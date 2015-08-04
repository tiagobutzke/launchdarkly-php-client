if (!validate.Promise) {
    validate.Promise = window.Promise;
}

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
            el: '.menu__list-wrapper',
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
            $menuMain: $('.menu__list-wrapper'),
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
        views.checkoutPageView = new CheckoutPageView({
            el: '.checkout__main',
            $header: $('.header'),
            configuration: VOLO.configuration,
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
        views.checkoutDeliveryValidationView = new VOLO.CheckoutDeliveryValidationView({
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
    if (checkoutViews.checkoutPageView) {
        checkoutViews.checkoutPageView.render();
    }
    if (checkoutViews.checkoutDeliveryInformationView) {
        checkoutViews.checkoutDeliveryInformationView.render();
    }
};

VOLO.initIntl = function (configuration) {
    var deferred = $.Deferred(),
        locale = configuration.locale.replace('_', '-'),
        currencySymbol = configuration.currencySymbol;

    if (_.isUndefined(window.Intl)) {
        $.ajax({
            url: '/js/dist/intl.js'
        }).done(function () {
            $.ajax({
                url: '/js/dist/intl/locale/' + locale + '.json',
                dataType: 'json'
            }).done(function (data) {
                IntlPolyfill.__addLocaleData(data);
                VOLO.initCurrencyFormat(locale, currencySymbol);
                console.log('INTL polyfill loaded');
                deferred.resolve(configuration);
            });
        });
    } else {
        VOLO.initCurrencyFormat(locale, currencySymbol);
        deferred.resolve(configuration);
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
        el: '.home__teaser__form',
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

VOLO.createRestaurantsView = function() {
    var restaurantsView = new VOLO.RestaurantsView({
        el: '.restaurants__list'
    });

    VOLO.views.push(restaurantsView);
    VOLO.gtmViews.push(restaurantsView);
    return restaurantsView;
};

VOLO.initGTMService = function(options) {
    if (_.isObject(VOLO.GTMServiceInstance)) {
        VOLO.GTMServiceInstance.unbind();
    }
    VOLO.GTMServiceInstance = new VOLO.GTMService(options);

    return VOLO.GTMServiceInstance;
};

VOLO.createRegistrationEvent = function ($button, loginButtonView) {
    var registerClick = $button.click(function () {
        var customerData = $button.data();
        customerData = customerData ? customerData.object : null;

        loginButtonView.showRegistrationModal(customerData);
    });

    VOLO.views.push(registerClick);
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

VOLO.createLogoutLinkView = function(cartModel) {
    var view = new VOLO.LogoutLinkView({
        el: '.logout-link',
        model: cartModel
    });

    VOLO.views.push(view);
    return view;
};

VOLO.createProfileView = function () {
    var profilePasswordFormView = new VOLO.ProfilePasswordFormView({
        el: '#profile-password-form'
    });

    VOLO.views.push(profilePasswordFormView);

    return profilePasswordFormView;
};

VOLO.doBootstrap = function(configuration) {
    window.blazy.revalidate();

    var locationModel = VOLO.createLocationModel(VOLO.jsonLocation),
        gtmCheckoutValidationView,
        cartModel,
        checkoutModel,
        checkoutInformationValidationFormView,
        loginButtonView,
        existingUserLoginView,
        cartIconView,
        restaurantsView
    ;

    if ($('.header__cart').length > 0 && $('#cart').length === 0) {
        cartIconView = VOLO.createCartIconView();
        cartIconView.render();
    }

    if ($('.menu__list-wrapper').length > 0) {
        cartModel = VOLO.createCartModel(VOLO.jsonCart);

        var cartViews = VOLO.createCartViews(cartModel, locationModel, VOLO.GTMServiceInstance),
            urlZipCode = window.location.search.split('zip=')[1];

        cartViews.cartView.render();
        if (urlZipCode) {
            cartViews.cartView.setZipCode(urlZipCode);
        }
    }

    if ($('.restaurants__list').length > 0) {
        restaurantsView = VOLO.createRestaurantsView();
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

    if ($('.home__teaser__form').length > 0) {
        var homeSearchView = VOLO.createHomeSearchView(locationModel);
        homeSearchView.render();
    }

    if ($('.header__account').length > 0) {
        loginButtonView = VOLO.createLoginButtonView();
        var $registerButton = $('.create_account__login__button');

        if ($registerButton.length > 0) {
            VOLO.createRegistrationEvent($registerButton, loginButtonView);
        }

        if ($('body').hasClass('show-change-password-modal') && $('.header__account__login-text').length > 0) {
            loginButtonView.showModalResetPassword(location.pathname.split('/')[4]);
        }
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
        existingUserLoginView = VOLO.createExistingUserLoginView();
        existingUserLoginView.render();
    }

    if ($('.logout-link').length > 0) {
        VOLO.createLogoutLinkView(cartModel);
    }

    if ($('#profile-password-form').length > 0) {
        VOLO.createProfileView();
    }

    var GTMServiceInstance = VOLO.initGTMService({
        options: {
            referrer: VOLO.tagManager.referrer,
            currency: VOLO.configuration.currencySymbol,
            pageType: VOLO.tagManager.pageType
        },
        checkoutModel: checkoutModel,
        sessionId: configuration.sessionId,
        checkoutDeliveryValidationView: gtmCheckoutValidationView,
        checkoutInformationValidationFormView: checkoutInformationValidationFormView,
        loginButtonView: loginButtonView,
        existingUserLoginView: existingUserLoginView,
        restaurantsView: restaurantsView
    });

    _.invoke(VOLO.gtmViews, 'setGtmService', GTMServiceInstance);
    _.invoke(VOLO.gtmViews, 'onGtmServiceCreated');
};

$(document).on('page:load page:restore', function () {
    console.log('page:load');

    VOLO.initIntl(VOLO.configuration).done(VOLO.doBootstrap);
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

    dataLayer.length = 0;
});

Turbolinks.pagesCached(10);

Turbolinks.enableProgressBar();
