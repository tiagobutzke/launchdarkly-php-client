var VOLO = VOLO || {};

VOLO.vendorsRoute = {
    navigateToVendorsList: function(address) {
        console.log('Going to: ', this.getVendorsRoute(address));
        Turbolinks.visit(this.getVendorsRoute(address));
    },

    getVendorsRoute: function(address) {
        return Routing.generate('volo_location_search_vendors_by_gps', {
            city: address.city,
            address: encodeURIComponent(address.address),
            longitude: address.longitude,
            latitude: address.latitude,
            postcode: address.postcode,
            street: encodeURIComponent(address.street),
            building: encodeURIComponent(address.building)
        });
    }
};
