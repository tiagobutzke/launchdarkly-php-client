var VOLO = VOLO || {};
$(document).ready(function () {
    window.blazy = new Blazy({
        breakpoints: volo_thumbor_transformations.breakpoints,
        offset: 400
    });
});

VOLO.initCartModel = function (jsonCart) {
    VOLO.cartModel = VOLO.cartModel || new CartModel(jsonCart, {
        dataProvider: new CartDataProvider(),
        parse: true
    });


    return VOLO.cartModel;
};

VOLO.initCheckoutModel = function (cartModel, locationModel, vendorId) {
    VOLO.checkoutModel = new CheckoutModel({}, {cartModel: cartModel});

    cartModel.getCart(vendorId).set('location', locationModel.attributes);

    return VOLO.checkoutModel;
};

VOLO.initCartViews = function (cartModel, locationModel) {
    var $header = $('.header');

    if (_.isObject(VOLO.menu)) {
        VOLO.menu.unbind();
    }
    VOLO.menu = new MenuView({
        el: '.menu__main',
        cartModel: cartModel,
        locationModel: locationModel,
        $header: $header
    });

    if (_.isObject(VOLO.cartView)) {
        VOLO.cartView.unbind();
    }
    VOLO.cartView = new CartView({
        el: '.desktop-cart',
        model: cartModel,
        locationModel: VOLO.locationModel,
        $header: $header,
        $menuMain: $('.menu__main'),
        $window: $(window)
    });

    if (_.isObject(VOLO.cartErrorModalView )) {
        VOLO.cartErrorModalView.unbind();
    }
    VOLO.cartErrorModalView = new CartErrorModalView({
        el: '#cartCalculationErrorModal',
        model: cartModel
    });
};

VOLO.initCheckoutViews = function (cartModel, checkoutModel, deliveryCheck, locationModel) {
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
        locationModel: locationModel,
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
        if (VOLO.checkoutDeliveryValidationView ) {
            VOLO.checkoutDeliveryValidationView .unbind();
        }
        VOLO.checkoutDeliveryValidationView = new CheckoutDeliveryValidationView({
            el: '#delivery_information_form',
            deliveryCheck: deliveryCheck,
            locationModel: locationModel,
            geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
        });
    }

    if ($('#payment_form').length > 0) {
        if (VOLO.paymentFormView) {
            VOLO.paymentFormView.unbind();
        }

        VOLO.paymentFormView = new PaymentFormView({
            el: '#payment_form',
            model: checkoutModel
        });
    }

    if ($('#contact_information_form').length > 0 ) {
        var View = ValidationView.extend({
            events: function(){
                return _.extend({},ValidationView.prototype.events,{
                    'keydown #mobile_number': '_hideErrorMsg'
                });
            },

            _hideErrorMsg: function() {
                this.$('.invalid_number').hide();
            }
        });

        VOLO.checkoutInformationValidationFormView = new View({
            el: '#contact_information_form',
            constraints: {
                "customer[first_name]": {
                    presence: true
                },
                "customer[last_name]": {
                    presence: true
                },
                "customer[email]": {
                    presence: true,
                    email: true
                },
                "customer[mobile_number]": {
                    presence: true
                }
            }
        });
    }

    if (_.isObject(VOLO.cartErrorModalView )) {
        VOLO.cartErrorModalView.unbind();
    }
    VOLO.cartErrorModalView = new CartErrorModalView({
        el: '#cartCalculationErrorModal',
        model: cartModel
    });
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
        minimumFractionDigits: 2,
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
        model: VOLO.locationModel,
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });
};

VOLO.initLoginButtonView = function() {
    if (VOLO.loginButtonView) {
        VOLO.loginButtonView.unbind();
    }
    VOLO.loginButtonView = new LoginButtonView({
        el: '.header__account'
    });
};

VOLO.initExistingUserLoginView = function() {
    if (VOLO.existingUserLoginView) {
        VOLO.existingUserLoginView.unbind();
    }
    VOLO.existingUserLoginView = new ExistingUserLoginView({
        el: '#show-login-overlay'
    });
};

VOLO.initCartIconLink = function() {
    console.log("initializing cart icon link");
    if (VOLO.cartIconView) {
        VOLO.cartIconView.unbind();
    }
    VOLO.cartIconView = new VendorCartIconView({
        el: '.header__cart'
    });
};

VOLO.initVendorsListSearch = function() {
    VOLO.vendorSearchView = new VendorsSearchView({
        el: '.restaurants__tool-box',
        model: VOLO.locationModel,
        geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1])
    });
};

VOLO.OrderTracking = function() {
    var $statusWrapper = $('.order-status-wrapper'),
        code;

    if ($statusWrapper.length === 0) {
        return;
    }

    code = $statusWrapper.data().order_code;

    $.ajax({
        url: Routing.generate('order_tracking', {orderCode: code, partial: 1})
    }).done(function(response) {
        console.log('order tracking update ', (new Date()).toString());
        $('.tracking-steps').html(response);
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

    console.log(VOLO.jsonLocation);
    VOLO.locationModel = VOLO.locationModel || new LocationModel(VOLO.jsonLocation);
    console.log('locationModel', VOLO.locationModel);

    if ($('.menu__main').length > 0) {
        VOLO.initCartModel(VOLO.jsonCart);
        VOLO.initCartViews(VOLO.cartModel, VOLO.locationModel);
        VOLO.cartView.render();
    }

    if ($('.checkout__main').length > 0) {
        VOLO.initCartModel(VOLO.jsonCart);
        VOLO.initCheckoutModel(VOLO.cartModel, VOLO.locationModel, $('.checkout__main').data('vendor_id'));
        VOLO.initCheckoutViews(VOLO.cartModel, VOLO.checkoutModel, new DeliveryCheck(), VOLO.locationModel);
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
        VOLO.initCartIconLink();
    }

    if ($('.header__account').length > 0) {
        VOLO.initLoginButtonView();
    }

    if($('.restaurants__tool-box').length > 0) {
        VOLO.initVendorsListSearch();
        VOLO.vendorSearchView.render();
        VOLO.initCartIconLink();
    }
    if ($('.order-status-wrapper').length > 0) {
        if (!_.isNull(VOLO.orderTrackingInterval)) {
            clearInterval(VOLO.orderTrackingInterval);
        }
        VOLO.orderTrackingInterval = setInterval(VOLO.OrderTracking, 60000);
    }

    if ($('#show-login-overlay').length > 0) {
        VOLO.initExistingUserLoginView();
        VOLO.existingUserLoginView.render();
    }

    var $registerButton = $('.create_account__login__button');
    if ($registerButton.length > 0) {
        VOLO.registerClick = $registerButton.click(function() {
            var customerData = $registerButton.data();
            customerData = customerData ? customerData.object : null;

            VOLO.loginButtonView.showRegistrationModal(customerData);
        });
    }
});

$(document).on('page:before-unload', function () {
    console.log('page:before-unload');
    if (VOLO.registerClick) {
        VOLO.registerClick.unbind();
    }

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
    if (_.isObject(VOLO.loginButtonView)) {
        VOLO.loginButtonView.unbind();
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
    if (_.isObject(VOLO.checkoutDeliveryValidationView )) {
        VOLO.checkoutDeliveryValidationView .unbind();
    }
    if (_.isObject(VOLO.cartErrorModalView )) {
        VOLO.cartErrorModalView.unbind();
    }
    if (_.isObject(VOLO.checkoutInformationValidationFormView)) {
        VOLO.checkoutInformationValidationFormView.unbind();
    }
    if (_.isObject(VOLO.orderTrackingInterval)) {
        clearInterval(VOLO.orderTrackingInterval);
        VOLO.orderTrackingInterval = null;
    }
    if (_.isObject(VOLO.cartIconView)) {
        VOLO.cartIconView.unbind();
    }
    if (_.isObject(VOLO.paymentFormView)) {
        VOLO.paymentFormView.unbind();
    }
    if (_.isObject(VOLO.existingUserLoginView)) {
        VOLO.existingUserLoginView.unbind();
    }
});

Turbolinks.pagesCached(10);

Turbolinks.enableProgressBar();
