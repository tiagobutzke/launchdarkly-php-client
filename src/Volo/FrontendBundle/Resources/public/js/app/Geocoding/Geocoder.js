VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.Geocoder = function(appConfig) {
    _.bindAll(this);

    this.appConfig = appConfig;
};
_.extend(VOLO.Geocoding.Geocoder.prototype, Backbone.Events, {
    geocodeAddress: function(placeName) {
        return this.geocode({'address': placeName});
    },

    geocodeLatLng: function(lat, lng) {
        return this.geocode({'location': {lat: lat, lng: lng}});
    },

    geocode: function(criteria) {
        var deferred = $.Deferred(),
            googleGeocoder = new google.maps.Geocoder();

        if (this.appConfig) {
            criteria =  _.extend({}, criteria, {
                componentRestrictions: { country: this.appConfig.countryCode }
            });
        }

        googleGeocoder.geocode(criteria, function (results, status) {
            if (this._isCountryAddressOnly(results)) {
                deferred.reject('ZERO_RESULTS');
            } else if (status == google.maps.GeocoderStatus.OK) {
                deferred.resolve(results);
            } else {
                deferred.reject(status);
            }
        }.bind(this));

        return deferred;
    },

    _isCountryAddressOnly: function(results) {
        return _.get(results, '[0]address_components.length') === 1 && _.get(results, '[0]address_components[0].types').indexOf('country') !== -1;
    }
});
