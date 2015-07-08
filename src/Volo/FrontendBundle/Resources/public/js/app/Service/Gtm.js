var VOLO = VOLO || {};
VOLO.GTMService = function (options) {
    this.dataLayer = options.dataLayer;
    this.sessionId = options.sessionId;
    this.checkoutModel = options.checkoutModel || null;
    this.checkoutDeliveryValidationView = options.checkoutDeliveryValidationView;
    this.checkoutInformationValidationFormView = options.checkoutInformationValidationFormView;

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
    },

    unbind: function () {
        this.stopListening();
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

    _createCookieName: function (vendorId) {
        return 'gtm_event_addFirstProduct-' + vendorId;
    },

    _setCookie: function (name, value) {
        Cookies.set(name, value, { expires: Infinity });
    },

    _hasCookie: function (name) {
        return !_.isUndefined(Cookies.get(name));
    },

    _push: function(data) {
        dataLayer.push(data);
        _.invoke(ga.getAll(), 'send', 'event');
    }
});
