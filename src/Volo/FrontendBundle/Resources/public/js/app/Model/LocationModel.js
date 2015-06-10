var LocationModel = Backbone.Model.extend({
    defaults: {
        location_type: "polygon",
        latitude: null,
        longitude: null,
        formattedAddress: null
    },

    initialize: function (data, options) {
        _.bindAll(this);
    },

    validate: function () {
        if (_.isNull(this.get('latitude')) || _.isNull(this.get('longitude'))) {
            return 'no_location';
        }
    },

    saveLocation: function (data) {
        $.ajax({
            url: Routing.generate('volo_customer_set_location'),
            data: {
                city: data.city,
                latitude: data.lat,
                longitude: data.lng,
                address: data.formattedAddress,
                postcode: data.postcode,
                _method: 'PUT'
            },
            method: 'PUT'
        });
    }
});
