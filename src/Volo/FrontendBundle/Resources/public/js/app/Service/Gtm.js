var VOLO = VOLO || {};
VOLO.GTMService = function (options) {
    this.options = {};
    this.options.referrer = options.options.referrer;
    this.options.currency = options.options.currency;
    this.options.pageType = options.options.pageType;

    this.dataLayer = options.dataLayer;
    this.sessionId = options.sessionId;
    this.checkoutModel = options.checkoutModel || null;
    this.checkoutDeliveryValidationView = options.checkoutDeliveryValidationView;
    this.checkoutInformationValidationFormView = options.checkoutInformationValidationFormView;
    this.loginButtonView = options.loginButtonView;
    this.existingUserLoginView = options.existingUserLoginView;
    this.restaurantsView = options.restaurantsView;

    this.initialize();
};

_.extend(VOLO.GTMService.prototype, Backbone.Events, {
    initialize: function () {
        if (_.isObject(this.checkoutModel)) {
            this.listenTo(this.checkoutModel, 'payment:attempt_to_pay', this.fireCheckoutPaymentDetailsSet);
            this.listenTo(this.checkoutModel, 'payment:error', this.fireCheckoutPaymentFailed);
        }

        if (_.isObject(this.checkoutDeliveryValidationView)) {
            this.listenTo(
                this.checkoutDeliveryValidationView,
                'submit:successful_before',
                this.fireCheckoutDeliveryDetailsSet
            );
        }

        if (_.isObject(this.checkoutInformationValidationFormView)) {
            this.listenTo(
                this.checkoutInformationValidationFormView,
                'validationView:validateSuccessful',
                this.fireCheckoutContactDetailsSet
            );
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
        }

        if (_.isObject(this.loginButtonView)) {
            this._bindLoginRegistrationEvents();
        }

        if (_.isObject(this.existingUserLoginView)) {
            this._bindLoginRegistrationEvents();
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

    fireOrderStatus: function(data) {
        if (!this._hasCookie('orderPay')) {
            return;
        }

        Cookies.expire('orderPay');

        data.event = 'transaction';
        data.sessionId = this.sessionId;

        this._push(data);
    },

    fireAddProduct: function (vendorId, data) {
        var cookieName = this._createCookieName(vendorId);
        if (!this._hasCookie(cookieName)) {
            this._push({
                'event': 'addToCart',
                'productName': data.name,
                'productId': data.id,
                'sessionId': this.sessionId
            });

            this._setCookie(cookieName, 'true');
        }
    },

    fireCheckoutDeliveryDetailsSet: function (data) {
        this._push({
            'event': 'checkout',
            'checkoutStep': '2 - Delivery Details Set',
            'deliveryTime': data.deliveryTime,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutContactDetailsSet: function (data) {
        this._push({
            'event': 'checkout',
            'checkoutStep': '3 - Contact Info Provided',
            'newsletterSignup': data.newsletterSignup,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutPaymentDetailsSet: function (data) {
        this._push({
            'event': 'checkout',
            'checkoutStep': '4 - Payment Details Set',
            'paymentMethod': data.paymentMethod,
            'sessionId': this.sessionId
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

    _push: function(data) {
        data.referrer = this.options.referrer;
        dataLayer.push(data);
    }
});
