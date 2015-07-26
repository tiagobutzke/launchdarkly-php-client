/**
 * model: CheckoutModel
 * collection: VOLO.UserAddressCollection
 * options:
 *  - locationModel: LocationModel
 */
VOLO.CheckoutDeliveryInformationView = Backbone.View.extend({
    events: {
        "click .add_new_address_link__text--add-address": '_openAddressForm',
        "click .add_new_address_link__text--cancel": '_closeAddressForm',
        "submit #delivery_information_form": '_createAddress'
    },

    initialize: function (options) {
        _.bindAll(this);

        if (this.model.get('is_guest_user')) {
            return; // as guest, nothing has to be done.
        }

        this.subViews = [];
        this.template = _.template($('#template-delivery_address').html());
        this.locationModel = options.locationModel;

        this.vendorId = this.$el.data().vendor_id;

        if (this.collection.length > 0) {
            this._selectLastAddress();
        }

        this.listenTo(this.model, 'change:address_id', this._changeAddress);
        this.listenTo(this.collection, 'add', this._renderAddress);
    },

    render: function () {
        console.log('CheckoutDeliveryInformationView.render ', this.cid);

        if (this.collection.length === 0) {
            this._emptyAddressForm();
            this._openAddressForm();
        } else {
            this._closeAddressForm();
            this._renderAddressList();
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

        this.$('#delivery-information-list').append(view.render().el);
        this.subViews.push(view);
    },

    _openAddressForm: function () {
        this.$('#delivery-information-list').addClass('hide');
        this.$('#add_new_address_form').removeClass('hide', true);
        this.$el.addClass('delivery_information_list-shown');
    },

    _closeAddressForm: function () {
        this.$('#delivery-information-list').removeClass('hide');
        this.$('#add_new_address_form').addClass('hide', true);
        this.$el.removeClass('delivery_information_list-shown');
    },

    _emptyAddressForm: function () {
        this.$('#address_line1').val('');
        this.$('#address_line2').val('');
        this.$('#company').val('');
        this.$('#delivery_instructions').val('');
        this.$('#address_latitude').val('');
        this.$('#address_longitude').val('');
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
        var data = this.$('#delivery_information_form').serializeJSON().customer_address,
            model = this.collection.findSimilar(data);

        this._closeAddressForm();

        if (model) {
            model.save(data, {
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

        this.$('#address_line1').val(attributes.address_line1);
        this.$('#address_line2').val(attributes.address_line2);
        this.$('#company').val(attributes.company);
        this.$('#delivery_instructions').val(attributes.delivery_instructions);
        this.$('#address_latitude').val('');
        this.$('#address_longitude').val('');

        this._openAddressForm();

        _.each(response.responseJSON.error.errors, function (error) {
            var selector = 'input[name=\'customer_address['+ error.field_name +']\']',
                element = this.$(selector);
            _.each(error.violation_messages, function (message) {
                var e = $('<span class="error_msg"></span>').text(message);
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
        this._emptyAddressForm();
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
        'click .delivery_address': '_selectAddress',
        'click button': '_delete'
    },

    className: 'checkout__address-list',

    initialize: function (options) {
        _.bindAll(this);

        this.template = _.template($('#template-delivery_address').html());
        this.checkoutModel = options.checkoutModel;
        this.listenTo(this.model, 'state:active', this.renderActiveState);
        this.listenTo(this.model, 'state:deactivate', this.renderDeactivateState);
        this.listenTo(this.model, 'destroy', this.remove);
    },

    render: function () {
        this.$el.html(this.template(this.model.attributes));
        if (this.model.id === this.checkoutModel.get('address_id')) {
            this.$el.addClass('checkout__address-list--active');
        }

        return this;
    },

    renderActiveState: function() {
        this.$el.addClass('checkout__address-list--active');
    },

    renderDeactivateState: function() {
        this.$el.removeClass('checkout__address-list--active');
    },

    _selectAddress: function () {
        this.checkoutModel.set('address_id', this.model.id);
        this.renderActiveState();

        return false;
    },

    _delete: function () {
        this.model.destroy();
        this.checkoutModel.set('address_id', null);

        return false;
    }
});
