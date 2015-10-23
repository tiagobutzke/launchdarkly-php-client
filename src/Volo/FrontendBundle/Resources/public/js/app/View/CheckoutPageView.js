var CheckoutPageView = Backbone.View.extend({
    events: {
        'click #checkout-finish-and-pay-button': '_submitOrder',
        'click .openRegistrationPopup': '_openRegistrationModal'
    },

    initialize: function (options) {
        _.bindAll(this);

        this.vendorCode = this.$el.data().vendor_code;
        this.vendorId = this.$el.data().vendor_id;

        this.spinner = new Spinner();
        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.configuration = options.configuration;
        this.customerModel = options.customerModel;
        this.userAddressCollection = options.userAddressCollection;
        this.locationModel = options.locationModel;
        this.cartModel = options.cartModel;
        this.contactInformationView = new VOLO.CheckoutContactInformationView({
            el: this.$('.checkout__contact-information'),
            vendorId: this.vendorId,
            customerModel: this.customerModel,
            userAddressCollection: this.userAddressCollection,
            locationModel: this.locationModel,
            checkoutModel: this.model,
            loginView: options.loginView
        });

        this.checkoutDeliveryInformationView = new VOLO.CheckoutDeliveryInformationView({
            el: this.$('.checkout__delivery-information'),
            model: this.model,
            vendorId: this.vendorId,
            collection: this.userAddressCollection,
            customerModel: this.customerModel,
            locationModel: this.locationModel,
            deliveryCheck: options.deliveryCheck,
            loginView: options.loginView
        });

        this.timePickerView = new TimePickerView({
            el: this.$('.checkout__time-picker'),
            model: options.cartModel,
            vendor_id: this.vendorId,
            values: options.timePickerValues,
            minDeliveryTime: this.$el.data().vendor_min_delivery_time
        });

        console.log('is guest user ', this.customerModel.isGuest);
        this.model.save('is_guest_user', this.customerModel.isGuest);

        this.listenTo(this.model, 'change', this.renderPayButton, this);

        this.listenTo(this.model, 'payment:success', this.handlePaymentSuccess, this);
        this.listenTo(this.model, 'payment:error', this.handlePaymentError, this);
        this.listenTo(this.userAddressCollection, 'update', this.renderContactInformationStep);

        this.listenTo(this.model, 'change', this._switchPaymentFormVisibility);
        this.listenTo(this.customerModel, 'change', this._switchPaymentFormVisibility);
        this.listenTo(this.userAddressCollection, 'update', this._switchPaymentFormVisibility);

        this.listenTo(this.contactInformationView, 'form:open', this.renderPayButton);
        this.listenTo(this.contactInformationView, 'form:close', this.renderPayButton);

        this.listenTo(this.checkoutDeliveryInformationView, 'form:open', this.renderPayButton);
        this.listenTo(this.checkoutDeliveryInformationView, 'form:close', this.renderPayButton);

        this.listenTo(this.contactInformationView, 'all', this.trigger);
        this.listenTo(this.checkoutDeliveryInformationView, 'all', this.trigger);

        if (options.configuration.countryCode === 'fi') {
            this.listenTo(this.cartModel.getCart(this.vendorId), 'cart:ready', this._updatePaymentsMethod);
            this._updatePaymentsMethod();
        }
    },

    _openRegistrationModal: function() {
        this.checkoutDeliveryInformationView._showRegistrationModal();

        return false;
    },

    _updatePaymentsMethod: function() {
        var totalValue = this.cartModel.getCart(this.vendorId).get('total_value');

        if (totalValue > 0) {
            if (this.model.get('payment_type_code') === 'invoice') {
                this.$('.checkout__payment__option-wrapper').first().click();
            }

            this.$('.checkout__payment__zero-price-message').addClass('hide');
            this.$('.checkout__payment__options-list').removeClass('hide');
        } else {
            this.$('.checkout__payment__zero-price-message').removeClass('hide');
            this.$('.checkout__payment__options-list').addClass('hide');
            this.$('.invoice').click();
        }
    },

    render: function () {
        this.renderContactInformationStep();

        this.timePickerView.render();

        this._switchPaymentFormVisibility();
        this.renderPayButton();

        this.checkoutDeliveryInformationView.render();

        return this;
    },


    renderPayButton: function () {
        var isButtonDisabled = !this.model.get('address_id') ||
            !this.model.canBeSubmitted() ||
            this.$('#delivery-information-form').is(':visible') ||
            this.$('#contact-information-form').is(':visible');

        console.log('isButtonDisabled ', isButtonDisabled);
        this.$('#checkout-finish-and-pay-button').toggleClass('button--disabled', isButtonDisabled);
    },

    _switchPaymentFormVisibility: function () {
        if (!_.isNull(this.model.get('address_id')) &&
            this.model.get('is_contact_information_valid') && this.customerModel.isValid()) {
            this.$('.checkout__payment').removeClass('checkout__step--reduced');
            this.$('.checkout__payment .checkout__step__items').removeClass('hide');
            this._refreshBlazy();
        } else {
            this.$('.checkout__payment').addClass('checkout__step--reduced');
            this.$('.checkout__payment .checkout__step__items').addClass('hide');
        }

        if (!this.$('.checkout__payment').hasClass('checkout__step--reduced')) {
            console.debug('Checkout step 3');
            this.model.trigger('checkoutModel:paymentOpened');
        }
    },

    _refreshBlazy: function () {
        if (window.blazy) {
            window.blazy.revalidate();
        }
    },

    renderContactInformationStep: function () {
        console.log('renderContactInformationStep', this.cid);
        this.contactInformationView.render();
    },

    unbind: function () {
        console.log('unbind checkoutPage ', this.cid);
        this.stopListening();
        this.undelegateEvents();
        this.contactInformationView.unbind();
        this.checkoutDeliveryInformationView.unbind();
        this.timePickerView.unbind();
    },

    _submitOrder: function () {
        var isSubscribedNewsletter = this.customerModel.get('is_newsletter_subscribed'),
            address = this.userAddressCollection.get(this.model.get('address_id'));

        if (this.$('#delivery-information-form-button').is(':visible')) {
            this.$('#error-message-delivery-not-saved').removeClass('hide');
            this._scrollToError(this.$('#checkout-delivery-information-address').offset().top);

            return false;
        }

        if (this.$('#contact-information-form').is(':visible')) {
            this.$('#error-message-contact-not-saved').removeClass('hide');
            this._scrollToError(this.$('.checkout__contact-information').offset().top);

            return false;
        }

        if (!this.model.canBeSubmitted()) {
            return false;
        }

        this.$('#error-message-delivery-not-saved').addClass('hide');
        this.spinner.spin(this.$('#checkout-finish-and-pay-button')[0]);

        this.model.placeOrder(this.vendorCode, this.vendorId, this.customerModel, address, isSubscribedNewsletter);

        this.$('.form__error-message').addClass('hide');
    },

    _scrollToError: function(msgOffset) {
        var paddingFromHeader = 16,
            scrollToOffset =  msgOffset - paddingFromHeader - this.domObjects.$header.outerHeight();

        $('html, body').animate({
            scrollTop: scrollToOffset
        }, this.configuration.anchorScrollSpeed);
    },

    handlePaymentSuccess: function (data) {
        if (_.isNull(data.hosted_payment_page_redirect)) {
            Turbolinks.visit(Routing.generate('order_tracking', {
                orderCode: data.code
            }));
        } else {
            if (data.hosted_payment_page_redirect.method === 'post') { // adyen hpp
                var params = data.hosted_payment_page_redirect.parameters,
                    url = "https://" + window.location.hostname + Routing.generate('handle_payment', {'orderCode': data.code});

                params.countryCode = this.configuration.countryCode.toUpperCase();
                params.resURL = url;
                this.redirectPost(data.hosted_payment_page_redirect.url, params);
            } else {
                window.location.replace(data.hosted_payment_page_redirect.url); // paypal
            }
        }
        this.spinner.stop();
    },

    handlePaymentError: function (data) {
        var exists = _.get(data, 'exists', {exists: false});

        if (_.isObject(data) && _.isString(_.get(data, 'error.errors.message'))) {
            this.$('.checkout__payment__finish-and-pay .form__error-message').html(data.error.errors.message);
            this.$('.checkout__payment__finish-and-pay .form__error-message').removeClass('hide');
        } else {
            console.log(data);
        }
        this.spinner.stop();
    },

    redirectPost: function (location, args) {
        var compiled = _.template(this.$('#template__form__redirect').html()),
            view = compiled({location: location, args: args});

        this.$el.append(view);
        this.$('#checkout-form-redirect').submit();
    }
});
