var VoucherView = Backbone.View.extend({
    events: {
        'click #checkout-add-voucher-link': '_toggleForm',
        'click #checkout-remove-selected-voucher-link': '_removeVoucher',
        'submit #checkout-voucher-form': '_addVoucher',
        'keyup #checkout-voucher-input': '_hideErrorMsg'
    },

    initialize: function() {
        _.bindAll(this);
        this.vendor_id = this.$el.data().vendor_id;

        this._initListeners();
        this._enableVoucher();
    },

    _initListeners: function() {
        var vendorCart = this.model.getCart(this.vendor_id);

        this.listenTo(vendorCart, 'cart:error', this._handleCartError);
        this.listenTo(vendorCart, 'cart:dirty', this._disableVoucher);
        this.listenTo(vendorCart, 'cart:ready', this._enableVoucher);
    },

    _enableVoucher: function() {
        this.$el.css({opacity: 1});
        var vendorCart = this.model.getCart(this.vendor_id);

        if (_.isString(vendorCart.get('voucher'))) {
            this.$('#checkout-selected-voucher').show();
            this.$('#checkout-selected-voucher-text').text(vendorCart.get('voucher'));
            this.$('#checkout-voucher-input').val(vendorCart.get('voucher'));

            this.$('#checkout-voucher-form').hide();
            this.$('#checkout-add-voucher-link').hide();
        } else {
            this.$('#checkout-selected-voucher').hide();
            this.$('#checkout-add-voucher-link').show();
            this.$('#checkout-selected-voucher-text').empty();
        }
    },

    _disableVoucher: function() {
        this.$el.css({opacity: 0.5});
        this._hideErrorMsg();
    },

    _hideErrorMsg: function() {
        this.$('.form__error-message--invalid-voucher').addClass('hide');
        this.$('.checkout__error-empty-voucher').addClass('hide');
    },

    _showErrorMsg: function(errorMessage) {
        this.$('.form__error-message--invalid-voucher').removeClass('hide');
        this.$('.form__error-message--invalid-voucher').html(errorMessage);
    },

    _showEmptyFieldErrorMsg: function() {
        this.$('.checkout__error-empty-voucher').removeClass('hide');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _toggleForm: function () {
        this.$('#checkout-voucher-form').toggle();
    },

    _removeVoucher: function() {
        var vendorCart = this.model.getCart(this.vendor_id);

        this.$('#checkout-voucher-input').val('');
        vendorCart.set('voucher', null);
        vendorCart.updateCart();
    },

    _addVoucher: function (event) {
        var vendorCart,
            $voucher = this.$('#checkout-voucher-input');

        if (!$voucher.val().length) {
            this._showEmptyFieldErrorMsg();
            event.preventDefault();

            return;
        }

        vendorCart = this.model.getCart(this.vendor_id);
        vendorCart.set('voucher', $voucher.val());
        vendorCart.updateCart();

        event.preventDefault();
    },

    _handleCartError: function (data) {
        var supportedErrors = [
            'ApiVoucherInactiveException',
            'ApiVoucherDoesNotExistException',
            'ApiVoucherInvalidVendorException',
            'ApiVoucherUsageExceededException',
            'ApiVoucherTemporaryClosedException',
            'ApiVoucherCustomerRequiredException',
            'ApiVoucherInvalidPaymentTypeException',
            'ApiVoucherNotValidForCustomerException',
            'ApiVoucherOrderAmountExceededException',
            'ApiVoucherProductCategoryUsageException',
            'ApiVoucherIsNotValidForPlatformException',
            'ApiVoucherLimitedToNewCustomersException',
            'ApiVoucherOrderAmountNotReachedException',
            'ApiVoucherUsagePerCustomerExceededException',
            'ApiVoucherTemporaryClosedWithScheduleException',
            'ApiVoucherPromotionOrderAmountNotReachedException',
            'ApiVoucherInvalidPaymentTypeButAnotherOneIsAvailableException'
        ];

        if (_.isObject(data) && _.indexOf(supportedErrors, _.get(data, 'error.errors.exception_type')) !== -1) {
            var vendorCart = this.model.getCart(this.vendor_id),
                errorMessage = this._getErrorMessage(data);

            this._triggerVoucherErrorEvent(errorMessage, vendorCart.get('voucher'));

            vendorCart.set('voucher', null);
            this._showErrorMsg(errorMessage);
        }
    },

    _getErrorMessage: function (data) {
        return _.get(data, 'error.errors.message', null);
    },

    _triggerVoucherErrorEvent: function (message, voucher) {
        this.trigger('voucherView:voucherError', {
            'message': message,
            'voucher': voucher
        });
    }
});
