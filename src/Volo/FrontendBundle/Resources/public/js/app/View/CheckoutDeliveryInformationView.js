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
        "click .select_other_address_link__text--select" : '_openAddressList',
        "click .select_other_address_link__text--cancel" : '_closeAddressList',
        "submit #delivery_information_form": '_addAddress'
    },

    initialize: function (options) {
        _.bindAll(this);

        if (this.model.get('is_guest_user')) {
            return; // as guest, nothing has to be done.
        }

        this.template = _.template($('#template-delivery_address').html());
        this.locationModel = options.locationModel;

        this.vendorId = this.$el.data().vendor_id;

        if (this.collection.length > 0) {
            this.model.set('address_id', this.collection.last().id);
        }

        this.listenTo(this.model, 'change:address_id', this._changeAddress, this);
    },

    render: function () {
        console.log('CheckoutDeliveryInformationView.render ', this.cid);

        this.$('#select_other_address_link').toggleClass('hide', this.collection.length < 1);

        this._hideAddressForm();
        this._hideAddressList();

        this._renderSelectedAddress();
    },

    _renderAddress: function (address) {
        var view = new VOLO.UserAddressView({
            model: address,
            checkoutModel: this.model
        });

        this.$('#delivery-information-list').append(view.render().el);
    },

    _renderSelectedAddress: function () {
        var addressId = this.model.get('address_id'),
            selectedAddress = this.collection.get(addressId),
            attributes = this.collection.model.prototype.defaults;

        if (selectedAddress) {
            attributes = selectedAddress.attributes;
        }

        this.$('.selected-address').empty().append(this.template(attributes));
    },

    _hideAddressForm: function () {
        this.$('#add_new_address_form').addClass('hide', true);
    },

    _showAddressForm: function () {
        this.$('#add_new_address_form').removeClass('hide', true);
    },

    _hideAddressList: function () {
        this.$('#delivery-information-list').addClass('hide');
    },

    _showAddressList: function () {
        this.$('#delivery-information-list').removeClass('hide');
    },

    _hideDeliveryInformationListWrapper: function () {
        this.$('#delivery-information-list-wrapper').addClass('hide');
    },

    _showDeliveryInformationListWrapper: function () {
        this.$('#delivery-information-list-wrapper').removeClass('hide');
    },

    _hideSelectedAddress: function () {
        this.$('.selected-address').addClass('hide');
    },

    _showSelectedAddress: function () {
        this.$('.selected-address').removeClass('hide');
    },

    _closeAddressForm: function () {
        this._hideAddressForm();
        this._emptyAddressForm();
        this.$el.removeClass('delivery_information_list-shown');

        this._showDeliveryInformationListWrapper();
    },

    _emptyAddressForm: function () {
        this.$('#address_line1').val('');
        this.$('#address_line2').val('');
        this.$('#company').val('');
        this.$('#delivery_instructions').val('');
        this.$('#address_latitude').val('');
        this.$('#address_latitude').val('');
        this.$('#address_longitude').val('');
    },

    _closeAddressList: function () {
        this._hideAddressList();

        this._showDeliveryInformationListWrapper();
        this._renderSelectedAddress();
        this._showSelectedAddress();
        this.$('#select_other_address_link').removeClass('address-selection--open');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _changeAddress: function () {
        this._closeAddressList();
        this._renderSelectedAddress();
    },

    _openAddressForm: function (event) {
        event.preventDefault();

        this._hideDeliveryInformationListWrapper();
        this._showAddressForm();
        this.$el.addClass('delivery_information_list-shown');
    },

    _openAddressList: function () {
        var currentCity = this.locationModel.get('city');

        var foo = this.collection.filter(function(address) {
            return address.get('is_delivery_available') || address.get('city') === currentCity;
        });

        this.$('#delivery-information-list').empty();
        _.each(foo, this._renderAddress);

        this.$('#select_other_address_link').addClass('address-selection--open');
        this._hideSelectedAddress();
        this._showAddressList();
        //this.render();
    },

    _addAddress: function () {
        var data = this.$('#delivery_information_form').serializeJSON().customer_address,
            model = this.collection.findSimilar(data);

        this._closeAddressForm();

        if (model) {
            this._setSelectedAddress(model);

            return false;
        }

        this.model.set('address_id', 0);
        this.collection.create(data, {
            wait: false,
            success: this._setSelectedAddress
        });

        return false;
    },

    _setSelectedAddress: function (address) {
        this.model.unset('address_id', {silent: true});
        this.model.set('address_id', address.id);
        this._hideAddressForm();
    }
});

/**
 * model: VOLO.UserAddressModel
 * options:
 *  - locationModel: CheckoutModel
 */
VOLO.UserAddressView = Backbone.View.extend({
    events: {
        "click .delivery_address": '_selectAddress'
    },

    className: 'checkout__address-list',

    initialize: function (options) {
        _.bindAll();

        this.template = _.template($('#template-delivery_address').html());
        this.checkoutModel = options.checkoutModel;
    },

    render: function () {
        this.$el.html(this.template(this.model.attributes));
        if (this.model.id === this.checkoutModel.get('address_id')) {
            this.$el.addClass('checkout__address-list--active');
        }

        return this;
    },

    _selectAddress: function () {
        this.checkoutModel.unset('address_id', {silent: true});
        this.checkoutModel.set('address_id', this.model.id);

        return false;
    }
});
