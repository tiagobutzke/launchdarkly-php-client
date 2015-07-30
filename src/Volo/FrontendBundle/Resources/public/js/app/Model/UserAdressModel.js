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

    filterByCity: function (currentCity) {
        return this.filter(function(address) {
            return address.get('is_delivery_available') || address.get('city') === currentCity;
        });
    }
});
