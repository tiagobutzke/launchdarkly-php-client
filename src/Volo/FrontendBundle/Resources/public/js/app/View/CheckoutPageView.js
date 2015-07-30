var CheckoutPageView = Backbone.View.extend({
    events: {
        'click #finish-and-pay': '_submitOrder',
        'click #add_new_address_link': 'render',
        'click #edit_contact_information_form_link': function(e) {
            this._openContactInformationForm(e);
            this.render();
        }
    },

    initialize: function (options) {
        _.bindAll(this);

        this.vendorCode = this.$el.data().vendor_code;
        this.vendorId = this.$el.data().vendor_id;

        this.spinner = new Spinner();
        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.configuration = options.configuration;

        console.log('is guest user', this.$el.data().is_guest_user);

        this.model.set('is_guest_user', this.$el.data().is_guest_user);

        this.listenTo(this.model, 'change:address_id', this.render, this);
        this.listenTo(this.model, 'change:credit_card_id', this.render, this);
        this.listenTo(this.model, 'change:adyen_encrypted_data', this.render, this);
        this.listenTo(this.model, 'change:payment_type_id', this.render, this);
        this.listenTo(this.model, 'change:payment_type_code', this.render, this);
        this.listenTo(this.model, 'change:cart_dirty', this.render, this);
        this.listenTo(this.model, 'change:placing_order', this.render, this);

        this.listenTo(this.model, 'payment:success', this.handlePaymentSuccess, this);
        this.listenTo(this.model, 'payment:error', this.handlePaymentError, this);
    },

    render: function () {
        var addressFormVisible = this.$('#delivery_information_form_button').is(':visible');
        var contactFormVisible = this.$('#contact_information_form').is(':visible');

        console.log('CheckoutPageView:render', this.model.isValid());
        this.$('#finish-and-pay').toggleClass('button--disabled', !this.model.canBeSubmitted() || addressFormVisible || contactFormVisible);
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _openContactInformationForm: function(e) {
        e.preventDefault();
        var $editContactInformationForm = this.$('#edit_contact_information_form'),
            editContactInformationFormIsVisible;

        $editContactInformationForm.toggleClass('hide');
        editContactInformationFormIsVisible = $editContactInformationForm.hasClass('hide');
        $('#contact_information').find('.checkout__item').toggleClass('hide', !editContactInformationFormIsVisible);
    },

    _submitOrder: function () {
        if (this.$('#delivery_information_form_button').is(':visible')) {
            this.$('#error_msg_delivery_not_saved').removeClass('hide');
            this._scrollToError(this.$('#checkout-delivery-information-address').offset().top);

            return false;
        }

        if (this.$('#contact_information_form').is(':visible')) {
            this.$('#error_msg_contact_not_saved').removeClass('hide');
            this._scrollToError(this.$('.checkout__contact-information').offset().top);

            return false;
        }

        if (!this.model.canBeSubmitted()) {
            return false;
        }

        this.$('#error_msg_delivery_not_saved').addClass('hide');
        this.spinner.spin(this.$('#finish-and-pay')[0]);
        this.model.placeOrder(this.vendorCode, this.vendorId);
        this.$('.error_msg').addClass('hide');
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
        this.$('.error_msg').removeClass('hide');

        if (_.isObject(data) && _.isString(data.error.errors.message)) {
            this.$('.error_msg').html(data.error.errors.message);
        }
        this.spinner.stop();
    },

    redirectPost: function (location, args) {
        var compiled = _.template(this.$('#template__form__redirect').html()),
            view = compiled({location: location, args: args});

        this.$el.append(view);
        this.$('#form__redirect').submit();
    }
});
