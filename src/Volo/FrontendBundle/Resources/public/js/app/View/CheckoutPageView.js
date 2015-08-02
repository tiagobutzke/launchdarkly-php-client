var CheckoutPageView = Backbone.View.extend({
    events: {
        'click #checkout-finish-and-pay-button': '_submitOrder'
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
        this.contactInformationView = new VOLO.CheckoutContactInformationView({
            el: this.$('.checkout__contact-information'),
            customerModel: this.customerModel,
            userAddressCollection: this.userAddressCollection,
            loginView: options.loginView
        });
        this.timePickerView = new TimePickerView({
            model: options.cartModel,
            vendor_id: this.vendorId
        });

        console.log('is guest user ', this.customerModel.isGuest);
        this.model.set('is_guest_user', this.customerModel.isGuest);

        this.listenTo(this.model, 'change', this.renderPayButton, this);

        this.listenTo(this.model, 'payment:success', this.handlePaymentSuccess, this);
        this.listenTo(this.model, 'payment:error', this.handlePaymentError, this);
        this.listenTo(this.userAddressCollection, 'update', this.renderContactInformationStep);

        this.listenTo(this.customerModel, 'change', this._switchPaymentFormVisibility);

        this.listenTo(this.contactInformationView, 'form:open', this.renderPayButton);
        this.listenTo(this.contactInformationView, 'form:close', this.renderPayButton);
    },

    render: function () {
        this.renderContactInformationStep();

        this.timePickerView.setElement(this.$('.checkout__time-picker'));
        this.timePickerView.render();

        this._switchPaymentFormVisibility();
        this.renderPayButton();

        return this;
    },

    renderPayButton: function () {
        var isButtonDisabled = this.userAddressCollection.length === 0 ||
            (!this.model.canBeSubmitted() ||
                this.$('#delivery-information-form').is(':visible') ||
                this.$('#contact-information-form').is(':visible')
            );

        console.log('isButtonDisabled ', isButtonDisabled);
        if (isButtonDisabled) {
            this.$('#checkout-finish-and-pay-button').addClass('button--disabled');
        } else {
            this.$('#checkout-finish-and-pay-button').removeClass('button--disabled');
        }
    },

    _switchPaymentFormVisibility: function () {
        if (this.userAddressCollection.length > 0 && this.customerModel.isValid()) {
            this.$('.checkout__payment').removeClass('checkout__step--reduced');
            this.$('.checkout__payment .checkout__step__items').removeClass('hide');
            this._refreshBlazy();
        } else {
            this.$('.checkout__payment').addClass('checkout__step--reduced');
            this.$('.checkout__payment .checkout__step__items').addClass('hide');
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
        this.stopListening();
        this.undelegateEvents();
        this.contactInformationView.unbind();
        this.timePickerView.unbind();
    },

    _submitOrder: function () {
        var isSubscribedNewsletter = this.contactInformationView.isNewsletterSubscriptionChecked(),
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

        //var address = this.userAddressCollection.get(this.model.get('address_id'));
        this.model.placeOrder(this.vendorCode, this.vendorId, this.customerModel, address, isSubscribedNewsletter);

        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: isSubscribedNewsletter
        });

        this.$('.form__error-message').addClass('hide');
    },

    _scrollToError: function(msgOffset) {
        var paddingFromHeader = 16,
            scrollToOffset =  msgOffset - paddingFromHeader - this.domObjects.$header.outerHeight();

        this.$('#error_msg_delivery_not_saved').removeClass('hide');
        $('body').animate({
            scrollTop: scrollToOffset
        }, this.configuration.anchorScrollSpeed);
    },

    handlePaymentSuccess: function (data) {
        if (_.isNull(data.hosted_payment_page_redirect)) {
            this.model.cartModel.emptyCart(this.vendorId);
            Turbolinks.visit(Routing.generate('order_tracking', {orderCode: data.code}));
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
        this.$('.form__error-message').removeClass('hide');

        if (_.isObject(data) && _.isString(_.get(data, 'error.errors.message'))) {
            this.$('.form__error-message').html(data.error.errors.message);
        } else if (exists) {
            this.contactInformationView.renderExistingUser();
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
