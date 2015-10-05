VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.Geocoder = function() {
    _.bindAll(this);
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

        googleGeocoder.geocode(criteria, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                deferred.resolve(results);
            } else {
                deferred.reject(status);
            }
        });

        return deferred;
    }
});
