var LocationModel = Backbone.Model.extend({
    defaults: {
        location_type: "polygon",
        latitude: null,
        longitude: null,
        postcode: null,
        city: '',
        address: null
    },

    urlRoot: function() {
        return Routing.generate('volo_customer_set_location');
    },

    initialize: function (data, options) {
        _.bindAll(this);
    },

    validate: function (attrs) {
        if (_.isNull(attrs.latitude) || _.isNull(attrs.longitude)) {
            return 'no_location';
        }
    }
});
