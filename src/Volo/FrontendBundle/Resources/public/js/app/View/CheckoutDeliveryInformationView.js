/**
 * model: CheckoutModel
 * collection: VOLO.UserAddressCollection
 * options:
 *  - locationModel: LocationModel
 */
VOLO.CheckoutDeliveryInformationView = Backbone.View.extend({
    events: {
        "click .checkout__title-link__text--add-address-delivery": '_openAddressForm',
        "click .checkout__title-link__text--cancel-delivery": '_closeAddressForm',
        "submit #delivery-information-form": '_createAddress'
    },

    initialize: function (options) {
        _.bindAll(this);

        if (this.model.get('is_guest_user')) {
            return; // as guest, nothing has to be done.
        }

        this.subViews = [];
        this.template = _.template($('#template-delivery-address').html());
        this.locationModel = options.locationModel;

        this.vendorId = this.$el.data().vendor_id;

        if (this.collection.filterByCity(this.locationModel.get('city')).length > 0) {
            this._selectLastAddress();
        }

        this.listenTo(this.model, 'change:address_id', this._changeAddress);
        this.listenTo(this.collection, 'add', this._renderAddress);
        this.listenTo(this.collection, 'update', this._renderAddNewAddressLink);
    },

    render: function () {
        console.log('CheckoutDeliveryInformationView.render ', this.cid);

        this._emptyAddressForm();

        if (this.collection.filterByCity(this.locationModel.get('city')).length === 0) {
            this._openAddressForm();
        } else {
            this._closeAddressForm();
        }

        this._renderAddressList();
        this._renderAddNewAddressLink();

        var md = new MobileDetect(window.navigator.userAgent);
        if (md.mobile()) {
            $('.checkout__delivery-information__delete-link').show();
        }

        return this;
    },

    _renderAddressList: function () {
        _.invoke(this.subViews, 'remove');
        _.each(this.collection.filterByCity(this.locationModel.get('city')), this._renderAddress);
    },

    _renderAddress: function (address) {
        var view = new VOLO.UserAddressView({
            model: address,
            checkoutModel: this.model
        });

        this.$('#checkout-delivery-information-list').append(view.render().el);
        this.subViews.push(view);
    },

    _renderAddNewAddressLink: function () {
        if (this.collection.filterByCity(this.locationModel.get('city')).length === 0) {
            this._hideCloseFormAddressLink();
            this._openAddressForm();
        } else {
            this._showCloseFormAddressLink();
        }
    },

    _showCloseFormAddressLink: function () {
        this.$('.checkout__title-link__text--cancel-delivery').removeClass('hide');
    },

    _hideCloseFormAddressLink: function () {
        this.$('.checkout__title-link__text--cancel-delivery').addClass('hide');
    },

    _openAddressForm: function () {
        this.$('#checkout-delivery-information-list').addClass('hide');
        this.$('#checkout-add-new-address-form').removeClass('hide', true);
        this.$el.addClass('checkout__delivery-information--list-shown');
    },

    _closeAddressForm: function () {
        this.$('#checkout-delivery-information-list').removeClass('hide');
        this.$('#checkout-add-new-address-form').addClass('hide', true);
        this.$el.removeClass('checkout__delivery-information--list-shown');
    },

    _emptyAddressForm: function () {
        this.$('#delivery-information-address-line1').val('');
        this.$('#delivery-information-address-line2').val('');
        this.$('#delivery-information-company').val('');
        this.$('#delivery_instructions').val('');
        this.$('#delivery-information-address-latitude').val('');
        this.$('#delivery-information-address-longitude').val('');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _changeAddress: function (checkoutModel, id) {
        var oldAddress, address;
        if (_.isNull(id)) {
            this._selectLastAddress();

            return;
        }

        oldAddress = this.collection.get(this.model.previous('address_id'));
        if (!_.isNull(id) && oldAddress) {
            oldAddress.trigger('state:deactivate');
        }

        address = this.collection.get(id);
        if (address) {
            address.trigger('state:active');
        }
    },

    _createAddress: function () {
        var data = this.$('#delivery-information-form').serializeJSON().customer_address,
            model = this.collection.findSimilar(data);

        this._closeAddressForm();
        this._emptyAddressForm();

        if (model) {
            model.save(data, {
                wait: false,
                success: this._updateSelectedAddress,
                error: this.onCreateError
            });

            return false;
        }

        this.collection.once('add', function (address, collection) {
            var lastActiveAddress = collection.get(this.model.get('address_id'));
            address.trigger('state:active');
            if (lastActiveAddress) {
                lastActiveAddress.trigger('state:deactivate');
            }
        }, this);

        this.collection.create(data, {
            wait: false,
            success: this._updateSelectedAddress,
            error: this.onCreateError
        });

        return false;
    },

    onCreateError: function (model, response) {
        var oldAddress = this.collection.get(this.model.previousAttributes().address_id),
            attributes = model.toJSON();

        _.invoke(this.collection.filterByCity(this.locationModel.get('city')), 'trigger', 'state:deactivate');
        this.collection.remove(model);
        this._updateSelectedAddress(oldAddress);

        this.$('#delivery-information-address-line1').val(attributes.address_line1);
        this.$('#delivery-information-address-line2').val(attributes.address_line2);
        this.$('#delivery-information-company').val(attributes.company);
        this.$('#delivery_instructions').val(attributes.delivery_instructions);
        this.$('#delivery-information-address-latitude').val('');
        this.$('#delivery-information-address-longitude').val('');

        this._openAddressForm();

        _.each(response.responseJSON.error.errors, function (error) {
            var selector = 'input[name=\'customer_address['+ error.field_name +']\']',
                element = this.$(selector);
            _.each(error.violation_messages, function (message) {
                var e = $('<span class="form__error-message"></span>').text(message);
                element.after(e);
            }, this);
        }, this);

    },

    _updateSelectedAddress: function (address) {
        var id = null;
        if (address) {
            id = address.id;
        }

        this.model.set('address_id', id);
    },

    _selectLastAddress: function () {
        var addresses = this.collection.filterByCity(this.locationModel.get('city')),
            address = _.last(addresses),
            id = null;

        if (address) {
            id = address.id;
        }
        this.model.set('address_id', id);
    }
});

/**
 * model: VOLO.UserAddressModel
 * options:
 *  - locationModel: CheckoutModel
 */
VOLO.UserAddressView = Backbone.View.extend({
    events: {
        'click .checkout__delivery-information__item': '_selectAddress',
        'click .checkout__delivery-information__delete-link': '_showAddressDeleteModal',
        'click .delivery-information__delete-modal__link-no': '_hideAddressDeleteModal',
        'click .delivery-information__delete-modal__link-yes': '_delete'
    },

    className: 'checkout__delivery-information__addresses',

    initialize: function (options) {
        _.bindAll(this);

        this.template = _.template($('#template-delivery-address').html());
        this.checkoutModel = options.checkoutModel;
        this.listenTo(this.model, 'state:active', this.renderActiveState);
        this.listenTo(this.model, 'state:deactivate', this.renderDeactivateState);
        this.listenTo(this.model, 'destroy', this.remove);
    },

    render: function () {
        this.$el.html(this.template(this.model.attributes));
        if (this.model.id === this.checkoutModel.get('address_id')) {
            this.$el.addClass('checkout__delivery-information__addresses--active');
        }

        return this;
    },

    renderActiveState: function() {
        this.$el.addClass('checkout__delivery-information__addresses--active');
    },

    renderDeactivateState: function() {
        this.$el.removeClass('checkout__delivery-information__addresses--active');
    },

    _selectAddress: function () {
        $('.checkout__delivery-information__delete-modal-wrapper').addClass('hide');
        this.checkoutModel.set('address_id', this.model.id);
        this.renderActiveState();

        return false;
    },

    _showAddressDeleteModal: function() {
        $('.checkout__delivery-information__delete-modal-wrapper').addClass('hide');
        this.$('.checkout__delivery-information__delete-modal-wrapper').removeClass('hide');

        return false;
    },

    _hideAddressDeleteModal: function() {
        this.$('.checkout__delivery-information__delete-modal-wrapper').addClass('hide');

        return false;
    },

    _delete: function () {
        this.model.destroy();
        this.checkoutModel.set('address_id', null);

        return false;
    }
});
