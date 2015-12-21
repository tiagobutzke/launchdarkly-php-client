var VOLO = VOLO || {};

VOLO.views = []; //all create views should go here
VOLO.gtmViews = []; //all views, which needs GTM should go here

$(document).ready(VOLO.documentReadyFunction);
VOLO.createFloodBannerModel = function() {
    VOLO.floodBannerModel = new Backbone.Model({
        hiddenByUser: false
    });

    return VOLO.floodBannerModel;
};

VOLO.createFilterModel = function () {
    return new VOLO.FilterModel();
};

VOLO.createVendorCollection = function(locationModel, filterModel) {
    return new VOLO.VendorCollection([], {
        locationModel: locationModel,
        filterModel: filterModel
    });
};

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

VOLO.createCartModel = function (jsonCart, defaultCartValues) {
    VOLO.cartModel = new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        defaultCartValues: defaultCartValues,
        parse: true,
        timezone: VOLO.configuration.timeZone
    });

    return VOLO.cartModel;
};

VOLO.createLocationModel = function (jsonLocation) {
    VOLO.locationModel = VOLO.locationModel || new LocationModel(jsonLocation);

    return VOLO.locationModel;
};

VOLO.createEventLocationModel = function (jsonVendorLocation, jsonCityLocation, jsonUserLocation) {
    if (!_.isEmpty(jsonVendorLocation)) {
        return new LocationModel(jsonVendorLocation);
    } else if (!_.isEmpty(jsonCityLocation)) {
        return new LocationModel(jsonCityLocation);
    }

    return new LocationModel(jsonUserLocation);
};

VOLO.createCheckoutModel = function (cartModel) {
    VOLO.checkoutModel = new CheckoutModel({}, {cartModel: cartModel});
    VOLO.checkoutModel.fetch();

    return VOLO.checkoutModel;
};

VOLO.createBannersView = function(locationModel, floodBannerModel) {
    var bannersView = new VOLO.BannersView({
        el: 'body',
        locationModel: locationModel,
        floodBannerModel: floodBannerModel
    });

    VOLO.views.push(bannersView);
    return bannersView;
};

VOLO.createVendorPopupView = function() {
    var vendorPopupView = new VOLO.VendorPopupView({
        el: '.vendor-popup__modal'
    });

    VOLO.views.push(vendorPopupView);
    return vendorPopupView;
};

VOLO.createUnsubscribePopupView = function() {
    var unsubscribePopupView = new VOLO.UnsubscribePopupView({
        el: '.unsubscribe__modal'
    });

    VOLO.views.push(unsubscribePopupView);
    return unsubscribePopupView;
};

VOLO.createCartViews = function (options) {
    var $header = $('.header'),
        $menuMain = $('.menu__list-wrapper'),
        $body = $('body'),
        menuView, cartView, confirmBelowMinimumAmountView;

    menuView = new MenuView({
        el: '.menu__list-wrapper',
        cartModel: options.cartModel,
        locationModel: options.locationModel,
        $header: $header,
        $postalCodeBar: options.$postalCodeBar,
        $body: $body,
        gtmService: options.gtmService,
        vendorGeocodingView: options.vendorGeocodingView
    });

    confirmBelowMinimumAmountView = new ConfirmBelowMinimumAmountView({
        el: '.modal-confirm-below-minimum-amount',
        model: options.cartModel.getCart(options.vendor.id),
        gtmService: options.gtmService
    });

    cartView = new CartView({
        el: '#cart',
        model: options.cartModel,
        locationModel: options.locationModel,
        $window: $(window),
        $body: $body,
        $header: $header,
        $menuMain: $menuMain,
        gtmService: options.gtmService,
        smallScreenMaxSize: VOLO.configuration.smallScreenMaxSize,
        timePickerValues: VOLO.timePickerValues,
        vendorGeocodingView: options.vendorGeocodingView,
        confirmBelowMinimumAmountView: confirmBelowMinimumAmountView,
        minimum_order_value_setting: VOLO.configuration.minimum_order_value_setting
    });

    VOLO.views.push(menuView, cartView, confirmBelowMinimumAmountView);

    return {
        cartView: cartView,
        menuView: menuView,
        confirmBelowMinimumAmountView: confirmBelowMinimumAmountView
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
        $window: $(window),
        timePickerValues: VOLO.timePickerValues,
        minimum_order_value_setting: VOLO.configuration.minimum_order_value_setting
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
            deliveryCheck: deliveryCheck,
            timePickerValues: VOLO.timePickerValues,
            minimum_order_value_setting: VOLO.configuration.minimum_order_value_setting
        });
    }

    if ($('.checkout__payment__options').length > 0) {
        views.paymentTypeView = new PaymentTypeView({
            el: '.checkout__payment',
            checkoutModel: checkoutModel,
            customerModel: customerModel,
            cartModel: cartModel,
            vendorId: $('.checkout__steps').data('vendor_id')
        });
    }

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
    var currencyLocale = locale;

    if (locale === 'en-AU' && window.navigator.userAgent.search(/chrome/i) !== -1) {
        currencyLocale = 'en-US';
        currency_symbol = 'USD';
    }

    VOLO.formatCurrency = new Intl.NumberFormat(currencyLocale, {
        style: 'currency',
        minimumFractionDigits: 2,
        currency: currency_symbol
    }).format;
    VOLO.formatNumber = new Intl.NumberFormat(locale).format;
};

VOLO.createHomeSearchView = function (locationModel) {
    var homeSearchView = new HomeSearchView({
        el: '.restaurants-search-form',
        model: locationModel,
        geocodingService: new GeocodingService(VOLO.configuration.countryCode)
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

VOLO.createCartIconView = function (defaultCartValues) {
    var cartIconView = new VendorCartIconView({
        el: '.header__cart',
        defaultCartValues: defaultCartValues
    });

    VOLO.views.push(cartIconView);

    return cartIconView;
};

VOLO.createVendorsListSearchView = function (locationModel, vendorCollection) {
    var vendorSearchView;

    if (VOLO.isMapEnabled()) {
        vendorSearchView = new VOLO.FullAddressVendorsSearchView({
            el: '.restaurants-container',
            model: locationModel,
            vendorCollection: vendorCollection,
            appConfig: VOLO.configuration
        });
    } else {
        vendorSearchView = new VOLO.VendorsSearchView({
            el: '.restaurants-container',
            model: locationModel,
            vendorCollection: vendorCollection,
            geocodingService: new GeocodingService(VOLO.configuration.countryCode)
        });
    }

    VOLO.views.push(vendorSearchView);

    return vendorSearchView;
};

VOLO.createVendorsListSearchNoLocationView = function (locationModel) {
    var vendorSearchView;
    if (VOLO.isMapEnabled()) {
        vendorSearchView = new VOLO.FullAddressVendorsSearchNoLocationView({
            el: '.restaurants .hero-banner-wrapper',
            model: locationModel,
            $window: $(window),
            appConfig: VOLO.configuration
        });
    } else {
        vendorSearchView = new VendorsSearchNoLocationView({
            el: '.restaurants .hero-banner-wrapper ',
            model: locationModel,
            geocodingService: new GeocodingService(VOLO.configuration.countryCode),
            $window: $(window)
        });
    }

    VOLO.views.push(vendorSearchView);

    return vendorSearchView;
};

VOLO.createRestaurantsView = function(vendorCollection, filterModel) {
    var restaurantsView = new VOLO.RestaurantsView({
        el: '.restaurants-container',
        collection: vendorCollection,
        filterModel: filterModel
    });

    VOLO.views.push(restaurantsView);
    VOLO.gtmViews.push(restaurantsView);

    VOLO.restaurantsView = restaurantsView;

    return restaurantsView;
};

VOLO.createFilterView = function(vendorCollection, filterModel) {
    var filtersView = new VOLO.FiltersView({
            el: '.restaurants-container',
            model: filterModel,
            vendorCollection: vendorCollection
        });

    VOLO.views.push(filtersView);

    return filtersView;
};

VOLO.createNewsLetterView = function() {
    var newsLetterView = new VOLO.NewsLetterView({
        el: '.footer__subscribe'
    });

    VOLO.views.push(newsLetterView);

    return newsLetterView;
};

VOLO.createRestaurantsSearchView = function() {
    var restaurantsSearchView = new VOLO.AddressFormStickingOnTop({
        el: '.restaurants__search-bar',
        $container: $('.hero-banner-wrapper'),
        $header: $('.header')
    });

    VOLO.views.push(restaurantsSearchView);
    return restaurantsSearchView;
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
    var profileContactView = new VOLO.ProfileContactInformationForm({
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

VOLO.createSpecialInstructionsTutorialView = function() {
    var view = new VOLO.SpecialInstructionsTutorialView({
        el: '.desktop-cart__special-instructions-tutorial'
    });

    VOLO.views.push(view);

    return view;
};

VOLO.createVendorGeocodingView = function($postalCodeBar, locationModel, cartModel, vendor) {
    var vendorGeocodingView;
    if (VOLO.isMapEnabled()) {
        vendorGeocodingView = new VOLO.FullAddressVendorSearchView({
            model: locationModel,
            el: $postalCodeBar,
            modelCart: cartModel.getCart(vendor.id),
            $window: $(window),
            appConfig: VOLO.configuration
        });
    } else {
        vendorGeocodingView = new VendorGeocodingView({
            el: $postalCodeBar,
            geocodingService: new GeocodingService(VOLO.configuration.countryCode),
            model: locationModel,
            modelCart: cartModel.getCart(vendor.id),
            $window: $(window)
        });
    }

    VOLO.views.push(vendorGeocodingView);

    return vendorGeocodingView;
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
        restaurantsView,
        vendorGeocodingView,
        vendorPopupView,
        userAddressCollection,
        filterModel,
        vendorCollection,
        fullAddressHomeSearchView
    ;

    userAddressCollection = VOLO.createUserAddressCollection(VOLO.jsonUserAddress, VOLO.customer);

    if ($('.header__cart').length > 0 && $('#cart').length === 0) {
        cartIconView = VOLO.createCartIconView(VOLO.defaultCartValues);
        cartIconView.render();
    }

    if ($('.footer__subscribe').length) {
        newsLetterView = VOLO.createNewsLetterView();
        newsLetterView.render();
    }

    if ($('.vendor-popup__modal').length > 0) {
        vendorPopupView = VOLO.createVendorPopupView();
        vendorPopupView.render();
    }

    if ($('.unsubscribe__modal').length > 0) {
        unsubscribePopupView = VOLO.createUnsubscribePopupView();
        unsubscribePopupView.render();
    }

    cartModel = VOLO.createCartModel(VOLO.jsonCart, VOLO.defaultCartValues);
    checkoutModel = VOLO.createCheckoutModel(cartModel, locationModel);

    if ($('.menu__list-wrapper').length > 0) {
        checkoutModel.save(checkoutModel.defaults, {silent: true});
    }

    if ($('.restaurants__list').length > 0) {
        filterModel = VOLO.createFilterModel();
        vendorCollection = VOLO.createVendorCollection(locationModel, filterModel);

        restaurantsView = VOLO.createRestaurantsView(vendorCollection, filterModel);

        if ($('.restaurants__tool-box').length > 0) {
            VOLO.createFilterView(vendorCollection, filterModel);
            VOLO.createVendorsListSearchView(locationModel, vendorCollection).render();
        }
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
    }

    if ($('.home .restaurants-search-form').length > 0) {
        if (VOLO.isMapEnabled()) {
            fullAddressHomeSearchView = new VOLO.FullAddressHomeSearchView({
                el: '.restaurants-search-form',
                appConfig: VOLO.configuration,
                model: locationModel
            });
            fullAddressHomeSearchView.render();

            VOLO.views.push(fullAddressHomeSearchView);
        } else {
            var homeSearchView = VOLO.createHomeSearchView(locationModel),
                homeView = VOLO.createHomeView();

            homeSearchView.render();
            homeView.render();
        }
    }

    if ($('.restaurants .restaurants-search-form').length > 0) {
        VOLO.createVendorsListSearchNoLocationView(locationModel).render();
        VOLO.createRestaurantsSearchView();
    }

    if ($('.header__account').length > 0) {
        if ($('body').hasClass('show-change-password-modal') && $('.header__account__login-text').length > 0) {
            loginButtonView.showModalResetPassword();
        }
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
        VOLO.createProfileView(VOLO.customer).profileContactView.render();
    }

    if ($('.desktop-cart__special-instructions-tutorial').length) {
        VOLO.createSpecialInstructionsTutorialView();
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
        checkoutVoucherView: checkoutViews.voucherView,
        loginButtonView: loginButtonView,
        restaurantsView: restaurantsView,
        fullAddressHomeSearchView: fullAddressHomeSearchView
    });

    if ($('.menu__list-wrapper').length > 0) {
        var $postalCodeBar = $('.menu__postal-code-bar'),
            vendor = $('.menu__list-wrapper').data('vendor');

        vendorGeocodingView = VOLO.createVendorGeocodingView($postalCodeBar, locationModel, cartModel, vendor);
        vendorGeocodingView.render();

        if ($('#cart').length) {
            var cartViews = VOLO.createCartViews({
                    cartModel: cartModel,
                    locationModel: locationModel,
                    gtmService: GTMServiceInstance,
                    vendorGeocodingView: vendorGeocodingView,
                    $postalCodeBar: $postalCodeBar,
                    vendor: vendor
                }),
                urlZipCode = window.location.search.split('zip=')[1];

            cartViews.cartView.render();
            if (urlZipCode) {
                cartViews.cartView.setZipCode(urlZipCode);
            }
        }
    }

    _.invoke(VOLO.gtmViews, 'setGtmService', GTMServiceInstance);

    if ($checkoutMain.length > 0) {
        VOLO.renderCheckoutViews(checkoutViews);
    }

    GTMServiceInstance.fireVirtualPageView();

    var floodBannerModel = VOLO.createFloodBannerModel(),
        eventLocationModel = VOLO.createEventLocationModel(VOLO.jsonVendorLocation, VOLO.jsonCityLocation, locationModel.toJSON()),
        bannersView = VOLO.createBannersView(eventLocationModel, floodBannerModel);
    bannersView.render();

    _.invoke(VOLO.gtmViews, 'onGtmServiceCreated');
};

VOLO.isFullAddressAutoComplete = function () {
    return VOLO.configuration.address_config.autocomplete_type[0] !== '(regions)';
};

VOLO.isMapEnabled = function () {
    return _.get(VOLO, 'configuration.address_config.map_enabled', false);
};

$(document).on('page:load page:restore', function () {
    console.log('page:load');

    VOLO.initIntl(VOLO.configuration).done(VOLO.doBootstrap);
});

$(document).on('page:receive', function (event) {
    console.log('Config App Version: ' + VOLO.configuration.appVersion);
    console.log('Cookie App Version: ' + Cookies("AppVersion"));

    if (VOLO.configuration.appVersion !== Cookies("AppVersion")) {
        $(this).off();
        window.location.assign(event.originalEvent.data.url);
    }
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
