var VoucherView = Backbone.View.extend({
    events: {
        'click #add_voucher_link': 'toggleForm',
        'click #remove_voucher_link': 'removeVoucher',
        'submit #voucher_form': 'addVoucher'
    },

    initialize: function () {
        this.vendor_id = this.$el.data().vendor_id;

        var vendorCart = this.model.getCart(this.vendor_id);

        this.isFormOpen = false;
        this.voucherError = null;

        this.listenTo(vendorCart, 'change:voucher', this.render, this);
        this.listenTo(vendorCart, 'cart:error', this.handleCartError, this);
        this.listenTo(vendorCart, 'cart:dirty', function() {this.$el.css({opacity: 0.5}, this);}, this);
        this.listenTo(vendorCart, 'cart:ready', function() {this.$el.css({opacity: 1}, this);}, this);
    },

    render: function () {
        var vendorCart = this.model.getCart(this.vendor_id);

        if (_.isString(vendorCart.get('voucher'))) {
            this.isFormOpen = false;

            this.$('#voucher-text').html(vendorCart.get('voucher'));
            this.$('#voucher').val(vendorCart.get('voucher'));
            this.$('#add_voucher_link').addClass('hide');
            this.$('#voucher-wrapper').removeClass('hide');
        } else {
            this.$('#voucher-text').html('');
            this.$('#voucher').val('');
            this.$('#add_voucher_link').removeClass('hide');
            this.$('#voucher-wrapper').addClass('hide');
        }

        if (_.isString(this.voucherError)) {
            this.$('.error_invalid_voucher').removeClass('hide');
            this.$('.error_invalid_voucher_text').html(this.voucherError);
            this.isFormOpen = true;
        } else {
            this.$('.error_invalid_voucher').addClass('hide');
        }

        if (this.isFormOpen) {
            this.$('#voucher_form').removeClass('hide');
        } else {
            this.$('#voucher_form').addClass('hide');
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    toggleForm: function (event) {
        this.isFormOpen = !this.isFormOpen;
        this.render();

        event.preventDefault();
    },

    removeVoucher: function (event) {
        var vendorCart = this.model.getCart(this.vendor_id);

        this.isFormOpen = false;
        vendorCart.set('voucher', null);
        vendorCart.updateCart();

        event.preventDefault();
    },

    addVoucher: function (event) {
        var vendorCart = this.model.getCart(this.vendor_id);

        this.voucherError = null;
        vendorCart.set('voucher', this.$('#voucher').val());
        vendorCart.updateCart();

        event.preventDefault();
    },

    handleCartError: function (data) {
        var supportedErrors = [
            'ApiVoucherDoesNotExistException',
            'ApiVoucherInactiveException',
            'ApiVoucherInvalidVendorException',
            'ApiVoucherLimitedToNewCustomersException',
            'ApiVoucherInvalidPaymentTypeException',
            'ApiVoucherNotValidForCustomerException',
            'ApiVoucherTemporaryClosedException',
            'ApiVoucherUsageExceededException',
        ];

        if (_.isObject(data) && _.indexOf(supportedErrors, data.error.errors.exception_type) !== -1) {
            var vendorCart = this.model.getCart(this.vendor_id);

            vendorCart.set('voucher', null);
            this.isFormOpen = true;

            if (_.isString(data.error.errors.message)) {
                this.voucherError = data.error.errors.message;
            }
            
            this.render();
        }
    }
});
