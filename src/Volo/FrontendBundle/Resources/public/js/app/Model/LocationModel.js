var LocationModel = Backbone.Model.extend({
    defaults: {
        location_type: "polygon",
        latitude: null,
        longitude: null,
        postcode: null,
        city: '',
        address: null,
        formattedAddress: null,
        street: '',
        building: ''
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

VOLO.FullAddressLocationModel = Backbone.Model.extend({
    defaults: {
        location_type: "polygon",
        latitude: null,
        longitude: null,
        postcode: null,
        city: '',
        address: null,
        formattedAddress: null,
        street: '',
        building: ''
    },

    initialize: function (data, options) {
        _.bindAll(this);
        this.appConfig = options.appConfig;
    },

    validate: function(address) {
        var defaultRequiredFields = ['city', 'postcode', 'street', 'building'],
            requiredFields = _.get(this.appConfig, 'address_config.required_fields', defaultRequiredFields);

        if (!_.isNumber(address.latitude) && !_.isNumber(address.longitude)) {
            return 'location';
        }

        return _.find(requiredFields, function(field) {
            if (_.isEmpty(address[field])) {
                return field;
            }
        });
    }
});
