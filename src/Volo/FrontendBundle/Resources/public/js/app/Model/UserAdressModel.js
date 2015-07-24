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

    url: function() {
        return Routing.generate('customer_address_list', {customerId: 0});
    },

    findSimilar: function (address) {
        return this.findWhere({
            address_line1: address.address_line1,
            address_line2: address.address_line2,
            city: address.city,
            city_id: address.city_id,
            postcode: address.postcode,
            delivery_instructions: '' === address.delivery_instructions ? null : address.delivery_instructions,
            company: '' === address.company ? null : address.company
        });
    }
});
