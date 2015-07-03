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
            this.listenTo(this.checkoutModel, 'payment:attempt_to_pay', $.proxy(function (data) {
                this.fireCheckoutPaymentDetailsSet(data.paymentMethod);
            }, this));
            this.listenTo(this.checkoutModel, 'payment:error', $.proxy(function (data) {
                this.fireCheckoutPaymentFailed(data.paymentMethod);
            }, this));
        }

        if (_.isObject(this.checkoutDeliveryValidationView)) {
            this.listenTo(this.checkoutDeliveryValidationView, 'submit:successful_before', $.proxy(function (data) {
                this.fireCheckoutDeliveryDetailsSet(data.deliveryTime);
            }, this));
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

        this._deleteCookie('orderPay');

        data.event = 'transaction';
        data.sessionId = this.sessionId;

        dataLayer.push(data);
    },

    fireAddProduct: function (vendorId, data) {
        var cookieName = this._createCookieName(vendorId);
        if (!this._hasCookie(cookieName)) {
            this.dataLayer.push({
                'event': 'addToCart',
                'productName': data.name,
                'productId': data.id,
                'sessionId': this.sessionId
            });

            this._setCookie(cookieName, 'true');
        }
    },

    fireCheckoutDeliveryDetailsSet: function (deliveryTime) {
        dataLayer.push({
            'event': 'checkout',
            'checkoutStep': '2 - Delivery Details Set',
            'deliveryTime': deliveryTime,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutContactDetailsSet: function (data) {
        dataLayer.push({
            'event': 'checkout',
            'checkoutStep': '3 - Contact Info Provided',
            'newsletterSignup': data.newsletterSignup,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutPaymentDetailsSet: function (paymentMethod) {
        this.dataLayer.push({
            'event': 'checkout',
            'checkoutStep': '4 - Payment Details Set',
            'paymentMethod': paymentMethod,
            'sessionId': this.sessionId
        });
    },

    fireCheckoutPaymentFailed: function (paymentMethod) {
        this.dataLayer.push({
            'event': 'paymentFailed',
            'paymentMethod': paymentMethod,
            'sessionId': this.sessionId
        });
    },

    _createCookieName: function (vendorId) {
        return 'gtm_event_addFirstProduct-' + vendorId;
    },

    _setCookie: function (name, value) {
        var expires = "expires=0";
        document.cookie = name + "=" + value + "; " + expires;
    },

    _getCookie: function (name) {
        var formattedName = name + "=";
        var cookies = document.cookie.split(';');
        console.log(cookies);

        for (var i = 0; i < cookies.length; i++) {
            var c = cookies[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(formattedName) === 0) {
                return c.substring(formattedName.length, c.length);
            }
        }

        return '';
    },

    _hasCookie: function (name) {
        var cookie = this._getCookie(name);

        return cookie !== '';
    },

    _deleteCookie: function (name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
});
