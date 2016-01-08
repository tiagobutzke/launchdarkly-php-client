VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.Places = function(appConfig, node) {
    _.bindAll(this);

    this.placesService = new google.maps.places.PlacesService(node);
    this.appConfig = appConfig;
};
_.extend(VOLO.Geocoding.Places.prototype, {
    isAddressUnique: function(userAddress, address) {
        var deferred = $.Deferred(),
            query = this._getQuery(userAddress, address);

        this.placesService.textSearch({query: query}, function(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                deferred.resolve(results.length === 1);
            } else {
                deferred.reject(results, status);
            }
        });

        return deferred;
    },

    _getQuery: function(userAddress, address) {
        return userAddress.split(' ').concat([address.city, this.appConfig.countryCode]).join(',+');
    }
});
