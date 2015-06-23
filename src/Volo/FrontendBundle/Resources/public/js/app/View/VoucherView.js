var VoucherView = Backbone.View.extend({
    events: {
        'click #add_voucher_link': '_toggleForm',
        'click #remove_voucher_link': '_removeVoucher',
        'submit #voucher_form': '_addVoucher',
        'keyup #voucher': '_hideErrorMsg'
    },

    initialize: function() {
        _.bindAll(this);
        this.vendor_id = this.$el.data().vendor_id;
        this.voucherError = null;

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
            this.$('#voucher-wrapper').show();
            this.$('#voucher-text').text(vendorCart.get('voucher'));
            this.$('#voucher').val(vendorCart.get('voucher'));

            this.$('#voucher_form').hide();
            this.$('#add_voucher_link').hide();
        } else {
            this.$('#voucher-wrapper').hide();
            this.$('#add_voucher_link').show();
            this.$('#voucher-text').empty();
        }
    },

    _disableVoucher: function() {
        this.$el.css({opacity: 0.5});
        this._hideErrorMsg();
    },

    _hideErrorMsg: function() {
        this.$('.error_invalid_voucher').css('display', 'none');
        this.$('.error_empty_voucher').addClass('hide');
    },

    _showErrorMsg: function() {
        this.$('.error_invalid_voucher').css('display', 'block');
        this.$('.error_invalid_voucher').text(this.voucherError);
    },

    _showEmptyFieldErrorMsg: function() {
        this.$('.error_empty_voucher').removeClass('hide');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _toggleForm: function () {
        this.$('#voucher_form').toggle();
    },

    _removeVoucher: function() {
        var vendorCart = this.model.getCart(this.vendor_id);

        this.$('#voucher').val('');
        vendorCart.set('voucher', null);
        vendorCart.updateCart();
    },

    _addVoucher: function (event) {
        var vendorCart,
            $voucher = this.$('#voucher');

        if (!$voucher.val().length) {
            this._showEmptyFieldErrorMsg();
            event.preventDefault();

            return;
        }

        vendorCart = this.model.getCart(this.vendor_id);
        this.voucherError = null;
        vendorCart.set('voucher', $voucher.val());
        vendorCart.updateCart();

        event.preventDefault();
    },

    _handleCartError: function (data) {
        var supportedErrors = [
            'ApiVoucherDoesNotExistException',
            'ApiVoucherInactiveException',
            'ApiVoucherInvalidVendorException',
            'ApiVoucherLimitedToNewCustomersException',
            'ApiVoucherInvalidPaymentTypeException',
            'ApiVoucherNotValidForCustomerException',
            'ApiVoucherTemporaryClosedException',
            'ApiVoucherUsageExceededException'
        ];

        if (_.isObject(data) && _.indexOf(supportedErrors, _.get(data, 'error.errors.exception_type')) !== -1) {
            var vendorCart = this.model.getCart(this.vendor_id);
            var errorMessage = _.get(data, 'error.errors.message');
            vendorCart.set('voucher', null);

            if (_.isString(errorMessage)) {
                this.voucherError = errorMessage;
            }
            
            this._showErrorMsg();
        }
    }
});
