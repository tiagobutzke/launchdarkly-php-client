var VOLO = VOLO || {};

VOLO.PaymentInvalidItemModalView = Backbone.View.extend({
    events: {
        'click .modal-payment-error__go-to-vendor-button': '_goToVendorPage',
        'hide.bs.modal': 'unbind'
    },

    initialize: function (options) {
        console.log('CartErrorPaymentModalView.initialize', this.cid);
        _.bindAll(this);

        this.cart = options.cart;
        this._vendorObject = options.vendorObject || {};
        this.minimumOrderValueSetting = options.minimumOrderValueSetting;
        this._spinner = new Spinner();

        this._DEFAULT_HEADLINE = this.$('.modal__h4').data('default-error-headline');

        this._INVALID_PRODUCT_LIST_TEMPLATE = _.template($('#template-cart-invalid-products-list').html());

        this._BELOW_MINIMUM_CONFIRM_TEMPLATE = this.$('.modal-payment-error__minimum-amount-message').data('below-minimum-confirm-template');
        this._BELOW_MINIMUM_ERROR_MESSAGE = this.$('.modal-payment-error__minimum-amount-message').data('below-minimum-error-message');

        this.$el.modal({show: false, backdrop: 'static'});
    },

    unbind: function () {
        this._reset();
        this.stopListening();
        this.undelegateEvents();
    },

    _reset: function () {
        this._resetHeadline();
        this._resetInvalidItemList();
        this._resetBelowMinimumAmountMessage();
        this._disableButtons();
        this.$el.removeClass('modal-payment-error--cannot-checkout');
    },

    _goToVendorPage: function () {
        var vendorId = this._vendorObject.id;
        this.close();

        if (vendorId) {
            Turbolinks.visit(Routing.generate('vendor_by_id', {id: vendorId}));
        }
    },

    _noConfirmationNeeded: function () {
        return this.minimumOrderValueSetting === 'no_confirmation';
    },

    _canCheckoutBelowMinimumValue: function () {
        return this.minimumOrderValueSetting === 'always_ask';
    },

    _cannotCheckoutBelowMinimumValue: function () {
        return this.minimumOrderValueSetting === 'deny_bellow_minimum';
    },

    _resetHeadline: function () {
        this.$('.modal__h4').empty();
    },

    _setHeadline: function (headline) {
        this.$('.modal__h4').text(headline);
    },

    _resetInvalidItemList: function () {
        this.$('.modal-payment-error__invalid-product-list').empty();
    },

    _setInvalidItemList: function (invalidItemList) {
        var invalidProductNames = _.map(invalidItemList, function (product) {
            return product.get('name');
        });

        this.$('.modal-payment-error__invalid-product-list').html(this._INVALID_PRODUCT_LIST_TEMPLATE({
            invalidProducts: invalidProductNames
        }));
    },

    _resetBelowMinimumAmountMessage: function () {
        this.$('.modal-payment-error__minimum-amount-message').empty();
    },

    _setBelowMinimumAmountMessage: function (message) {
        this.$('.modal-payment-error__minimum-amount-message').html(message);
    },

    _renderBelowMinimumAmountErrorMessage: function () {
        this._setBelowMinimumAmountMessage(this._BELOW_MINIMUM_ERROR_MESSAGE);
    },

    _renderBelowMinimumAmountConfirmMessage: function (replacements) {
        var message = this._BELOW_MINIMUM_CONFIRM_TEMPLATE;

        if (replacements) {
            if ("subtotal" in replacements) {
                message = message.replace('%subtotal%', VOLO.formatCurrency(replacements.subtotal));
            }

            if ("minimumValue" in replacements) {
                message = message.replace('%minimum_order_amount%', VOLO.formatCurrency(replacements.minimumValue));
            }

            if ("vendorName" in replacements) {
                message = message.replace('%vendor_name%', replacements.vendorName);
            }
        }

        this._setBelowMinimumAmountMessage(message);
    },

    _enableButtons: function () {
        this.$('.modal-footer').removeClass("modal-payment-error__loading-footer");
        this._spinner.stop();
    },

    _disableButtons: function () {
        this.$('.modal-footer').addClass("modal-payment-error__loading-footer");
        this._spinner.spin(this.$('.modal-footer')[0]);
    },

    displayError: function (errorObject) {
        if (errorObject.ApiProductInvalidForVendorException) {
            this.listenToOnce(this.cart, 'cart:isValid', function () {
                if (this.cart.isSubtotalGreaterZero()) {
                    if (this._noConfirmationNeeded()) {
                        this._resetBelowMinimumAmountMessage();
                    } else if (this._canCheckoutBelowMinimumValue()) {
                        this._renderBelowMinimumAmountConfirmMessage({
                            subtotal: this.cart.get('subtotal'),
                            minimumValue: this.cart.get('minimum_order_amount'),
                            vendorName: this._vendorObject.name
                        });
                    } else if (this._cannotCheckoutBelowMinimumValue()) {
                        this._renderBelowMinimumAmountErrorMessage();
                        this.$el.addClass('modal-payment-error--cannot-checkout');
                    }
                }

                this._enableButtons();
            }.bind(this));

            this._setHeadline(this._DEFAULT_HEADLINE);
            this._setInvalidItemList(errorObject.invalidProducts);
            this._disableButtons();
        }
        this.$el.modal('show');
    },

    close: function () {
        this.$el.modal('hide');
    }
});
