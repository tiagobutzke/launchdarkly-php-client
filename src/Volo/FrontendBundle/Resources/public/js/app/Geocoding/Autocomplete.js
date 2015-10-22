VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.Autocomplete = function() {
    _.bindAll(this);
};
_.extend(VOLO.Geocoding.Autocomplete.prototype, Backbone.Events, {
    init: function($inputNode, appConfig) {
        this.autocomplete = new google.maps.places.Autocomplete($inputNode[0], {
                types: _.get(appConfig, 'address_config.autocomplete_type', ['(regions)']),
                componentRestrictions: {
                    country: appConfig.countryCode
                }
            }
        );
        this.$node = $inputNode;

        //because of inconsistency of click and enter in dropdown
        google.maps.event.addDomListener($inputNode[0], 'keydown', function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });

        this.placeChanged = google.maps.event.addListener(this.autocomplete, 'place_changed', this._placeChanged);
    },

    unbind: function() {
        this.autocomplete && google.maps.event.clearInstanceListeners(this.autocomplete);
        this.$node && google.maps.event.clearListeners(this.$node[0]);
        this.placeChanged && google.maps.event.removeListener(this.placeChanged);

        $('.pac-container').remove();
    },

    getFirstResultValue: function() {
        var address = $('.pac-container .pac-item:first .pac-item-query').text(),
            region = $(".pac-container .pac-item:first span:not(.pac-matched):last").text();

        if (_.isEmpty(address) && _.isEmpty(region)) {
            return this.$node.val();
        } else {
            return [address, region].join(',');
        }
    },

    _placeChanged: function() {
        var place = this.autocomplete.getPlace(),
            location = _.get(place, 'geometry.location');

        if (location) {
            this.trigger('autocomplete-search:place-found', place);
        } else if (place && place.name) {
            this.trigger('autocomplete-search:place-without-location', place);
        } else {
            this.trigger('autocomplete-search:unknown-error');
        }
    }
});
