VOLO.UserAddressModel = Backbone.Model.extend({
    defaults: {
        city_id: '',
        city: '',
        area_id: 0,
        areas: '',
        address_line1: null,
        address_line2: null,
        address_line3: null,
        address_line4: null,
        address_line5: null,
        address_other: null,
        room: null,
        flat_number: null,
        structure: null,
        building: null,
        intercom: null,
        entrance: null,
        floor: null,
        district: null,
        postcode: null,
        company: null,
        latitude: null,
        longitude: null,
        is_delivery_available: false,
        formatted_customer_address: null,
        delivery_instructions: null,
        is_same_as_requested_location: false
    },

    validate: function (attributes) {
        if (!_.isString(attributes.address_line1) || attributes.address_line1.length === 0) {
            return 'address_line1 not valid';
        }
        if (!_.isString(attributes.address_line2) || attributes.address_line2.length === 0) {
            return 'address_line2 not valid';
        }
        if (!_.isString(attributes.city) || attributes.city.length === 0) {
            return 'city not valid';
        }
        if (!_.isString(attributes.postcode) || attributes.postcode.length === 0) {
            return 'postcode not valid';
        }
    }
});

VOLO.UserAddressCollection = Backbone.Collection.extend({
    model: VOLO.UserAddressModel,
    comparator: 'id',
    initialize: function (data, options) {
        _.bindAll(this);
        this.isLocalStorageEnabled = options.customer.isGuest;
        this.customerId = options.customer.id;
    },

    localStorage: function () {
        if (this.isLocalStorageEnabled) {
            return new Backbone.LocalStorage("UserAddressCollection");
        }

        return false;
    },

    url: function() {
        return Routing.generate('api_customers_address_list', {customerId: this.customerId});
    },

    filterByCityAndVendorId: function (currentCity, vendorId) {
        return this.filter(function(address) {
            var isDeliverable = address.get('is_delivery_available') || address.get('city') === currentCity;
            if (this.isLocalStorageEnabled) {
                isDeliverable = isDeliverable && address.get('vendor_id') === vendorId;
            }

            return isDeliverable;
        }, this);
    }
});
