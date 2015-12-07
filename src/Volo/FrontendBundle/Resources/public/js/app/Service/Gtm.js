var VOLO = VOLO || {};
VOLO.GTMService = function (options) {
    this.options = {};
    this.options.referrer = options.options.referrer;
    this.options.currency = options.options.currency;
    this.options.pageType = options.options.pageType;

    this.dataLayer = options.dataLayer;
    this.sessionId = options.sessionId;
    this.checkoutModel = options.checkoutModel || null;
    this.checkoutPageView = options.checkoutPageView;
    this.loginButtonView = options.loginButtonView;
    this.restaurantsView = options.restaurantsView;
    this.homeSearchView = options.homeSearchView;
    this.homeView = options.homeView;
    this.checkoutVoucherView = options.checkoutVoucherView;
    this.fullAddressHomeSearchView = options.fullAddressHomeSearchView;

    this.initialize();
};

_.extend(VOLO.GTMService.prototype, Backbone.Events, {
    initialize: function () {
        if (_.isObject(this.checkoutModel)) {
            this.listenTo(this.checkoutModel, 'payment:attempt_to_pay', this.fireCheckoutPaymentDetailsSet);
            this.listenTo(this.checkoutModel, 'payment:error', this.fireCheckoutPaymentFailed);
        }

        if (_.isObject(this.checkoutPageView)) {
            this.listenTo(
                this.checkoutPageView,
                'delivery:submit:successful_before',
                this.fireCheckoutDeliveryDetailsSet
            );
        }

        if (_.isObject(this.checkoutVoucherView)) {
            this.listenTo(
                this.checkoutVoucherView,
                'voucherView:voucherError',
                this.fireVoucherError
            );
        }

        if (_.isObject(this.checkoutModel)) {
            this.listenTo(this.checkoutModel, 'checkoutModel:addressOpened', this.fireCheckoutHasStarted);
            this.listenTo(this.checkoutModel, 'checkoutModel:paymentOpened', this.fireCheckoutContactDetailsSet);
        }

        if (_.isObject(this.restaurantsView)) {
            this.listenTo(
                this.restaurantsView,
                'restaurantsView:restaurantClicked',
                this.fireRestaurantClicked
            );

            this.listenTo(
                this.restaurantsView,
                'restaurantsView:restaurantsDisplayedOnLoad',
                this.fireRestaurantsDisplayedOnLoad
            );

            this.listenTo(
                this.restaurantsView,
                'restaurantsView:restaurantsDisplayedOnScroll',
                this.fireRestaurantsDisplayedOnScroll
            );

            this.listenTo(
                this.restaurantsView,
                'restaurants-view:gtm-restaurants-loaded',
                this._push
            );
        }

        if (_.isObject(this.homeSearchView)) {
            this.listenTo(
                this.homeSearchView,
                'ctaTrackable:ctaClicked',
                this.fireCTAClicked
            );
        }

        if (_.isObject(this.homeView)) {
            this.listenTo(
                this.homeView,
                'ctaTrackable:ctaClicked',
                this.fireCTAClicked
            );
        }

        if (_.isObject(this.loginButtonView)) {
            this._bindLoginRegistrationEvents();
        }

        if (_.isObject(this.fullAddressHomeSearchView)) {
            this.listenTo(this.fullAddressHomeSearchView, 'home-search-view:gtm-open-map', this._push);
            this.listenTo(this.fullAddressHomeSearchView, 'home-search-view:gtm-submit', this._push);
            this.listenTo(this.fullAddressHomeSearchView.mapModalView, 'map-dialog:gtm-error-shown', this._push);
        }

        this._push(this.doMobileDetection(window.navigator.userAgent));
    },

    unbind: function () {
        this.stopListening();
    },

    doMobileDetection: function (userAgent) {
        var md = new MobileDetect(userAgent);

        var detectionResult = {
            deviceType: 'desktop',
            deviceName: null
        };

        if (md.phone()) {
            detectionResult.deviceType = 'mobile';
            detectionResult.deviceName = md.phone();
        }

        if (md.tablet()) {
            detectionResult.deviceType = 'tablet';
            detectionResult.deviceName = md.tablet();
        }

        return detectionResult;
    },

    fireVoucherError: function (data) {
        this._push({
            'event': 'errorVoucher',
            'coupon': data.voucher,
            'apiErrorMessage': data.message
        });
    },

    fireLogin: function (data) {
        this._push({
            'event': 'login',
            'method': data.method,
            'result': data.result
        });
    },

    fireRegistration: function (data) {
        this._push({
            'event': 'register',
            'method': data.method,
            'result': data.result
        });
    },

    fireOrderStatus: function(data, referrer, sessionId) {
        if (!this._hasCookie('orderPay')) {
            return;
        }

        Cookies.remove('orderPay');

        data.event = 'transaction';
        data.sessionId = _.get(this, 'sessionId', sessionId);

        this._push(data, referrer);
    },

    fireAddProduct: function (vendorId, data) {
        var cookieName = this._createCookieName(vendorId);
        if (!this._hasCookie(cookieName)) {
            this._push({
                'event': 'addToCart',
                'productName': data.name,
                'productId': data.id,

                'productPrice': data.productPrice,
                'cartValue': data.cart.value,
                'cartContents': data.cart.contents,
                'cartQuantity': data.cart.quantity,
                'actionLocation': data.actionLocation,
                'ecommerce': {
                    'currencyCode': this.options.currency,
                    'add': {
                        'products': [{
                            'name': data.vendor.name,
                            'id': data.vendor.id,
                            'category': data.vendor.category,
                            'variant': data.vendor.variant,
                            'quantity': 1
                        }]
                    }
                },

                'sessionId': this.sessionId
            });

            this._setCookie(cookieName, 'true');
        }
    },

    fireCheckoutHasStarted: function () {
        console.debug('GTM::fireCheckoutHasStarted');

        this._push({
            'event': 'checkout',
            'checkoutStep': '1 - Checkout Started',
            'sessionId': this.sessionId
        });
    },

    fireCheckoutDeliveryDetailsSet: function (data) {
        console.debug('GTM::fireCheckoutDeliveryDetailsSet');

        this._push({
            'event': 'checkout',
            'checkoutStep': '2 - Delivery Details Set',
            'deliveryTime': data.deliveryTime,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutContactDetailsSet: function () {
        console.debug('GTM::fireCheckoutContactDetailsSet');

        this._push({
            'event': 'checkout',
            'checkoutStep': '3 - Contact Info Provided',
            'sessionId': this.sessionId
        });
    },

    fireCheckoutPaymentDetailsSet: function (data) {
        console.debug('GTM::fireCheckoutPaymentDetailsSet');

        this._push({
            'event': 'checkout',
            'checkoutStep': '4 - Payment Details Set',
            'paymentMethod': data.paymentMethod,
            'newsletterSignup': data.newsletterSignup,
            'sessionId': this.sessionId
        });
    },

    fireSurchargeAction: function (data) {
        this._push({
            'event': 'surchargePopup',
            'clickTarget': data.clickTarget,
            'differenceToMinimumAmount': data.differenceToMinimumAmount,
            'minimumOrderValue': data.minimumOrderValue
        });
    },

    fireCheckoutPaymentFailed: function (data) {
        this._push({
            'event': 'paymentFailed',
            'paymentMethod': data.paymentMethod,
            'sessionId': this.sessionId
        });
    },

    fireRestaurantClicked: function (data) {
        this._push({
            'event': 'restaurantClick',
            'ecommerce': {
                'currencyCode': this.options.currency,
                'click': {
                    'actionField': {
                        'list': this.options.pageType
                    },
                    'products': [{
                        'name': data.name,
                        'id': data.id,
                        'variant': data.variant,
                        'position': data.position
                    }]
                }
            }
        });
    },

    fireRestaurantsDisplayedOnLoad: function(impressions) {
        this._push({
            'ecommerce': {
                'currencyCode': this.options.currency,
                'impressions': _.map(impressions, this._addListToImpressions, this)
            }
        });
    },

    fireRestaurantsDisplayedOnScroll: function(impressions) {
        this._push({
            'event': 'restaurantImpressions',
            'ecommerce': {
                'currencyCode': this.options.currency,
                'impressions': _.map(impressions, this._addListToImpressions, this)
            }
        });
    },

    fireCTAClicked: function (data) {
        this._push({
            'event': 'homeCTAclick',
            'ctaName': data.name
        });
    },

    fireVirtualPageView: function () {
        var updates = {
                'event': 'virtualPageView'
            },
            isECommerceExists = _.some(dataLayer, function (el) {
                return _.isObject(el.ecommerce);
            })
        ;

        if (!isECommerceExists) {
            updates.ecommerce = {};
        }

        this._push(updates);
    },

    _createCookieName: function (vendorId) {
        return 'gtm_event_addFirstProduct-' + vendorId;
    },

    _setCookie: function (name, value) {
        Cookies.set(name, value, { expires: Infinity });
    },

    _hasCookie: function (name) {
        return !_.isUndefined(Cookies.get(name));
    },

    _bindLoginRegistrationEvents: function () {
        this.listenTo(
            this.loginButtonView,
            'loginRegistrationView:login',
            this.fireLogin
        );
        this.listenTo(
            this.loginButtonView,
            'loginRegistrationView:registration',
            this.fireRegistration
        );
    },

    _addListToImpressions: function(element) {
        element.list = this.options.pageType;

        return element;
    },

    _push: function(data, referrer) {
        data.referrer = _.get(this, 'options.referrer', referrer);
        console.log('GTM referrer ', data.referrer);
        console.log('GTM log', data);
        dataLayer.push(data);
    }
});
