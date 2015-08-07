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
        if (!attributes.address_line1) {
            return 'address_line1 not valid';
        }
        if (!attributes.address_line2) {
            return 'address_line2 not valid';
        }
        if (!attributes.city) {
            return 'city not valid';
        }
        if (!attributes.postcode) {
            return 'postcode not valid';
        }
    }
});

VOLO.UserAddressCollection = Backbone.Collection.extend({
    model: VOLO.UserAddressModel,
    comparator: function (a, b) {
        if (a.id >=b.id) return -1 ;
        if (a.id < b.id) return 1 ;

        return 0;
    },

    initialize: function (data, options) {
        _.bindAll(this);
        this.isGuest = options.customer.isGuest;
        this.customerId = options.customer.id;
    },

    localStorage: function () {
        if (this.isGuest) {
            return new Backbone.LocalStorage("UserAddressCollection");
        }

        return null;
    },

    url: function() {
        return Routing.generate('api_customers_address_list', {customerId: this.customerId});
    },

    filterByCityAndVendorId: function (currentCity, vendorId) {
        return this.filter(function(address) {
            var isDeliverable;
            if (this.isGuest) {
                isDeliverable = address.get('vendor_id') === vendorId;
            } else {
                isDeliverable = address.isNew() || address.get('is_delivery_available') || address.get('city') === currentCity;
            }

            return isDeliverable;
        }, this);
    }
});
