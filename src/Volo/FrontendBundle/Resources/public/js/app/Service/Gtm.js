var VOLO = VOLO || {};
VOLO.GTMService = function (options) {
    this.dataLayer = options.dataLayer;
    this.sessionId = options.sessionId;
    this.checkoutModel = options.checkoutModel || null;
    this.checkoutDeliveryValidationView = options.checkoutDeliveryValidationView;

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
    },

    unbind: function () {
        this.stopListening();
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

        for (var i = 0; i < cookies.length; i++) {
            var c = cookies[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(formattedName) === 0) {
                return c.substring(formattedName.length, c.length);
            }
        }

        return '';
    },

    _hasCookie: function (name) {
        var username = this._getCookie(name);

        return username !== '';
    }
});
