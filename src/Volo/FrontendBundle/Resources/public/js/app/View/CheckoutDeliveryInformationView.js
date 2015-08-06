/**
 * model: CheckoutModel
 * collection: VOLO.UserAddressCollection
 * options:
 *  - locationModel: LocationModel
 */
VOLO.CheckoutDeliveryInformationView = Backbone.View.extend({
    events: {
        "click .checkout__title-link__text--add-address-delivery": '_openAddressForm',
        "click .checkout__title-link--guest": '_editGuestAddress',
        "click .checkout__title-link__text--cancel-delivery": '_closeAddressForm',
        "submit #delivery-information-form": '_submit'
    },

    initialize: function (options) {
        _.bindAll(this);

        this.subViews = [];
        this.template = _.template($('#template-delivery-address').html());
        this.locationModel = options.locationModel;
        this.customerModel = options.customerModel;

        this.vendorId = options.vendorId;

        if (!this.customerModel.isGuest && this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId).length > 0) {
            this._selectLastAddress();
        }

        this.listenTo(this.model, 'change:address_id', this._changeAddress);
        this.listenTo(this.collection, 'custom:edit', this._editAddress);
        this.listenTo(this.collection, 'add', this._renderAddress);
        this.listenTo(this.collection, 'update', this._renderAddNewAddressLink);

        this.checkoutDeliveryValidationView = new VOLO.CheckoutDeliveryValidationView({
            el: this.$('#delivery-information-form'),
            deliveryCheck: options.deliveryCheck,
            locationModel: options.locationModel,
            geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1]),
            postalCodeGeocodingService: new PostalCodeGeocodingService(VOLO.configuration.locale.split('_')[1])
        });
    },

    render: function () {
        console.log('CheckoutDeliveryInformationView.render ', this.cid);

        this._emptyAddressForm();

        if (this.customerModel.isGuest && _.isNull(this.model.get('address_id'))) {
            this.$('.checkout__title-link__icon--plus').addClass('hide');
            this.$('.checkout__title-link__text--add-address-delivery').addClass('hide');

            this._editGuestAddress();
        } else {
            if (this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId).length === 0) {
                this._openAddressForm();
            } else {
                this._closeAddressForm();
            }
        }

        this._renderAddressList();
        this._renderAddNewAddressLink();

        return this;
    },

    _renderAddressList: function () {
        _.invoke(this.subViews, 'remove');
        _.each(this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId), this._renderAddress);
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
        if (this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId).length === 0) {
            this._hideCloseFormAddressLink();
            this._openAddressForm();
        } else {
            this._showCloseFormAddressLink();
        }
    },

    _editAddress: function (address) {
        this._fillForm(address);

        if (address && address.isValid()) {
            this.$("#delivery-information-form-button").removeClass('button--disabled');
        } else {
            this.$("#delivery-information-form-button").addClass('button--disabled');
        }
        this._openAddressForm();
    },

    _showCloseFormAddressLink: function () {
        if (this.customerModel.isGuest && _.isNull(this.model.get('address_id'))) {
            this.$('.checkout__title-link__text--cancel-delivery').addClass('hide');
        } else {
            this.$('.checkout__title-link__text--cancel-delivery').removeClass('hide');
        }
    },

    _hideCloseFormAddressLink: function () {
        this.$('.checkout__title-link__text--cancel-delivery').addClass('hide');
    },

    _editGuestAddress: function () {
        var address = _.first(this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId));
        if (address) {
            this._editAddress(address);
        }
    },

    _openAddressForm: function () {
        this.$('.form__error-message').addClass('hide');
        this.$('#checkout-delivery-information-list').addClass('hide');
        this.$('#checkout-add-new-address-form').removeClass('hide');
        this.$el.addClass('checkout__delivery-information--list-shown');

        if (this.customerModel.isGuest) {
            this.$('.checkout__title-link--guest').addClass('hide');
        }

        this.trigger('form:open', this);
    },

    _closeAddressForm: function () {
        this.$('#checkout-delivery-information-list').removeClass('hide');
        this.$('#checkout-add-new-address-form').addClass('hide');
        this.$el.removeClass('checkout__delivery-information--list-shown');
        this._emptyAddressForm();

        if (this.customerModel.isGuest) {
            this.$('.checkout__title-link--guest').removeClass('hide');
        }

        this.trigger('form:close', this);
    },

    _emptyAddressForm: function () {
        this.$('#delivery-information-address_id').val('');
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
        this.checkoutDeliveryValidationView.unbind();
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

    _submit: function () {
        this.checkoutDeliveryValidationView.submit(this._createAddress);

        return false;
    },

    _createAddress: function () {
        var data = {}, model;

        _.each(this.$('#delivery-information-form').serializeJSON().customer_address, function (val, key) {
            if (_.trim(val).length > 0) {
                data[key] = val;
            }
        });
        if (this.customerModel.isGuest) {
            data.vendor_id = this.vendorId;
        }

        this._closeAddressForm();
        this._emptyAddressForm();

        model = this.collection.get(data.id);
        if (model) {
            model.save(data, {
                wait: false,
                success: this._updateSelectedAddress,
                error: this.onCreateError
            });
        } else {
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
        }

        return false;
    },

    _fillForm: function (address) {
        var attributes = address.toJSON();

        this.$('#delivery-information-address_id').val(attributes.id);
        this.$('#delivery-information-address-line1').val(attributes.address_line1);
        this.$('#delivery-information-address-line2').val(attributes.address_line2);
        this.$('#delivery-information-company').val(attributes.company);
        this.$('#delivery-information-city').val(attributes.city);
        this.$('#delivery-information-city_id').val(attributes.city_id);
        this.$('#delivery-information-postal-index').val(attributes.postcode);
        this.$('#delivery-information-instructions').val(attributes.delivery_instructions);
        this.$('#delivery-information-address-latitude').val(attributes.latitude);
        this.$('#delivery-information-address-longitude').val(attributes.longitude);
    },

    onCreateError: function (model, response) {
        var oldAddress = this.collection.get(this.model.previousAttributes().address_id);

        _.invoke(this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId), 'trigger', 'state:deactivate');
        this.collection.remove(model);
        this._updateSelectedAddress(oldAddress);
        this._renderAddressList();

        this._fillForm(model);
        this._openAddressForm();

        _.each(_.get(response,  'responseJSON.error.errors', []), function (error) {
            var selector = 'input[name=\'customer_address['+ error.field_name +']\']',
                element = this.$(selector);
            _.each(_.get(error, 'violation_messages', []), function (message) {
                this.checkoutDeliveryValidationView.createErrorMessage(message, element[0]);
            }, this);
        }, this);

    },

    _updateSelectedAddress: function (address) {
        var id = null;
        if (address) {
            id = address.id;
        }

        this.model.save('address_id', id);
        this.trigger('delivery:submit:successful_before', {
            deliveryTime: this.$('#order-delivery-time').val()
        });

    },

    _selectLastAddress: function () {
        var addresses = this.collection.filterByCityAndVendorId(this.locationModel.get('city'), this.vendorId),
            address = _.last(addresses),
            id = null;

        if (address) {
            id = address.id;
        }
        this.model.save('address_id', id);
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
        'click .delivery-information__delete-modal__link-yes': '_delete',
        'click .checkout__delivery-information__edit-link': '_edit'
    },

    className: 'checkout__delivery-information__addresses',

    initialize: function (options) {
        _.bindAll(this);

        this.template = _.template($('#template-delivery-address').html());
        this.checkoutModel = options.checkoutModel;
        this.listenTo(this.model, 'state:active', this.renderActiveState);
        this.listenTo(this.model, 'state:deactivate', this.renderDeactivateState);
        this.listenTo(this.model, 'destroy', this.remove);
        this.listenTo(this.model, 'change', this.render);
    },

    render: function () {
        var md = new MobileDetect(window.navigator.userAgent);

        this.$el.html(this.template(this.model.attributes));
        if (this.checkoutModel.get('is_guest_user')) {
            this.$el.addClass('isGuest');
        }

        if (this.model.id === this.checkoutModel.get('address_id')) {
            this.$el.addClass('checkout__delivery-information__addresses--active');
        }

        if (md.mobile()) {
            $('.checkout__delivery-information__delete-link').show();
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
        if (this.checkoutModel.get('is_guest_user')) {
            return false;
        }

        this.$('.checkout__delivery-information__delete-modal-wrapper').addClass('hide');
        this.checkoutModel.set('address_id', this.model.id);
        this.renderActiveState();

        return false;
    },

    _showAddressDeleteModal: function() {
        this.$('.checkout__delivery-information__delete-modal-wrapper').addClass('hide');
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
    },

    _edit: function () {
        this.model.collection.trigger('custom:edit', this.model);
    }
});
