if (!validate.Promise) {
    validate.Promise = window.Promise;
}

var VOLO = VOLO || {};

VOLO.views = []; //all create views should go here
VOLO.gtmViews = []; //all views, which needs GTM should go here

$(document).ready(VOLO.documentReadyFunction);

VOLO.createCustomerModel = function (jsonCustomer, isGuest) {
    VOLO.customer = new VOLO.CustomerModel(jsonCustomer, {
        isGuest: isGuest
    });

    return VOLO.customer;
};

VOLO.createUserAddressCollection = function (jsonUserAddress, customerModel) {
    jsonUserAddress = jsonUserAddress || [];

    VOLO.userAddressCollection = VOLO.userAddressCollection || new VOLO.UserAddressCollection([], {
            customer: customerModel
        });

    if (VOLO.userAddressCollection.isGuest) {
        VOLO.userAddressCollection.fetch();
    } else {
        VOLO.userAddressCollection.reset(jsonUserAddress);
    }

    return VOLO.userAddressCollection;
};

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

VOLO.createCheckoutModel = function (cartModel) {
    VOLO.checkoutModel = new CheckoutModel({}, {cartModel: cartModel});
    VOLO.checkoutModel.fetch();

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

VOLO.createCheckoutViews = function (cartModel, checkoutModel, locationModel, userAddressCollection, loginButtonView, customerModel) {
    var views = {},
        deliveryCheck = new DeliveryCheck();

    views.checkoutCartView = new CheckoutCartView({
        el: '#cart',
        model: cartModel,
        $header: $('.header'),
        locationModel: locationModel,
        $menuMain: $('.checkout__steps'),
        $window: $(window)
    });

    if ($('.checkout__payment__voucher').length > 0) {
        views.voucherView = new VoucherView({
            el: '.checkout__payment__voucher',
            model: cartModel
        });
    }
    if ($('#checkout-finish-and-pay-button').length > 0) {
        views.checkoutPageView = new CheckoutPageView({
            el: '.checkout__steps',
            $header: $('.header'),
            configuration: VOLO.configuration,
            model: checkoutModel,
            userAddressCollection: userAddressCollection,
            customerModel: customerModel,
            cartModel: cartModel,
            loginView: loginButtonView,
            locationModel: locationModel,
            deliveryCheck: deliveryCheck
        });
    }

    if ($('.checkout__payment__options').length > 0) {
        views.paymentTypeView = new PaymentTypeView({
            el: '.checkout__payment',
            checkoutModel: checkoutModel,
            customerModel: customerModel
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

    if (checkoutViews.voucherView) {
        checkoutViews.voucherView.render();
    }
    if (checkoutViews.checkoutPageView) {
        checkoutViews.checkoutPageView.render();
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

VOLO.createHomeView = function () {
    var homeView = new HomeView({
        el: '.home'
    });

    VOLO.views.push(homeView);

    return homeView;
};

VOLO.createLoginButtonView = function (customerModel) {
    var loginButtonView = new LoginButtonView({
        el: '.header__account',
        customerModel: customerModel
    });

    VOLO.views.push(loginButtonView);
    return loginButtonView;
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
        loginButtonView.showRegistrationModal();
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

VOLO.createProfileView = function(customerModel) {
    var profilePasswordFormView = new VOLO.ProfilePasswordFormView({
        el: '#profile-contact-information-form'
    });
    var profileContactView = new VOLO.ContactInformatioForm({
        el: $('#contact-information-form'),
        model: customerModel
    });

    VOLO.views.push(profileContactView);
    VOLO.views.push(profilePasswordFormView);

    return {
        profilePasswordFormView: profilePasswordFormView,
        profileContactView: profileContactView
    };
};

VOLO.doBootstrap = function(configuration) {
    moment.tz.setDefault(VOLO.configuration.timeZone);
    window.blazy.revalidate();

    var locationModel = VOLO.createLocationModel(VOLO.jsonLocation),
        checkoutViews = {},
        cartModel,
        checkoutModel,
        loginButtonView,
        cartIconView,
        restaurantsView
    ;

    userAddressCollection = VOLO.createUserAddressCollection(VOLO.jsonUserAddress, VOLO.customer);

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
    cartModel = VOLO.createCartModel(VOLO.jsonCart);
    checkoutModel = VOLO.createCheckoutModel(cartModel, locationModel);

    if ($('.menu__list-wrapper').length > 0) {
        checkoutModel.save(checkoutModel.defaults, {silent: true});
    }

    if ($('.restaurants__list').length > 0) {
        restaurantsView = VOLO.createRestaurantsView();
    }

    if ($('.header__account').length > 0) {
        loginButtonView = VOLO.createLoginButtonView(VOLO.customer);
        var $registerButton = $('.create_account__login__button');

        if ($registerButton.length > 0) {
            VOLO.createRegistrationEvent($registerButton, loginButtonView);
        }
    }

    var $checkoutMain = $('.checkout__steps');
    if ($checkoutMain.length > 0) {
        //cartModel = VOLO.createCartModel(VOLO.jsonCart);
        //checkoutModel = VOLO.createCheckoutModel(cartModel, locationModel, $checkoutMain.data('vendor_id'));
        cartModel.getCart($checkoutMain.data('vendor_id')).set('location', locationModel.attributes);

        checkoutViews = VOLO.createCheckoutViews(cartModel, checkoutModel, locationModel, userAddressCollection, loginButtonView, VOLO.customer);
        VOLO.renderCheckoutViews(checkoutViews);
    }

    if ($('.home__teaser__form').length > 0) {
        homeSearchView = VOLO.createHomeSearchView(locationModel);
        homeSearchView.render();
    }

    if ($('.home__teaser__form').length > 0) {
        homeView = VOLO.createHomeView();
        homeView.render();
    }

    if ($('.header__account').length > 0) {
        if ($('body').hasClass('show-change-password-modal') && $('.header__account__login-text').length > 0) {
            loginButtonView.showModalResetPassword();
        }
    }

    if ($('.restaurants__tool-box').length > 0) {
        var vendorSearchView = VOLO.createVendorsListSearchView();
        vendorSearchView.render();
    }

    if ($('.order-status-wrapper').length > 0) {
        /**
         * Temporary disabled
         *
         * https://jira.rocket-internet.de/browse/SGFD-18918
         */
        //if (!_.isNull(VOLO.orderTrackingInterval)) {
        //    clearInterval(VOLO.orderTrackingInterval);
        //}
        //VOLO.orderTrackingInterval = setInterval(VOLO.OrderTracking, 60000);
    }

    if ($('.profile__blocks-wrapper').length > 0) {
        VOLO.createProfileView(VOLO.customer).profileContactView.fillUpForm();
    }

    var GTMServiceInstance = VOLO.initGTMService({
        options: {
            referrer: VOLO.tagManager.referrer,
            currency: VOLO.configuration.currencySymbol,
            pageType: VOLO.tagManager.pageType
        },
        checkoutModel: checkoutModel,
        sessionId: configuration.sessionId,
        checkoutPageView: checkoutViews.checkoutPageView,
        loginButtonView: loginButtonView,
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
    VOLO.views.length = 0;

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
