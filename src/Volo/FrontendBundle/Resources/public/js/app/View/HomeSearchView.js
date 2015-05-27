var HomeSearchView = Backbone.View.extend({
    initialize: function (options) {
        this.geocodingService = options.geocodingService;
    },

    render: function() {
        var $input = $('#postal_index_form_input');

        this.geocodingService.init($input);
        this._preventSubmitOnEnter($input);
        this._initSubmitButton($input);
        this._initEvents($input);
    },

     _preventSubmitOnEnter: function($input) {
        google.maps.event.addDomListener($input[0], 'keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });
    },

    _initEvents: function($input) {
        $input.on('autocomplete:place_changed autocomplete:tab_pressed', function() {
            this._getNewLocation($input).fail(this._notFound, this);
        }.bind(this));

        $input.on('autocomplete:submit_pressed', function() {
            this._getNewLocation($input).done(this._search, this);
        }.bind(this));
    },

    _notFound: function() {
        console.log('not found');
    },

    _search: function(data) {
        if (!!data.post_code) {
            Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
                lng: data.lng,
                lat: data.lat,
                plz: data.post_code
            }));
        } else {
            this._notFound();
        }
    },

    _initSubmitButton: function($input) {
        $('#postal_index_form_submit').click(function() {
            this._getNewLocation($input).then(this._search, this);
        }.bind(this));
    },

    _getNewLocation: function($input) {
        var deferred = $.Deferred();

        this.geocodingService.getLocation($input)
            .fail(deferred.reject, this)
            .done(function(locationMeta) {
                var data = this._getDataFromMeta(locationMeta);

                deferred.resolve(data);
            }.bind(this));

        return deferred;
    },

    _getDataFromMeta: function (locationMeta) {
        var formattedAddress = locationMeta.formattedAddress;

        if (!formattedAddress.match(locationMeta.postalCode.value)) {
            formattedAddress = locationMeta.postalCode.value + " " + formattedAddress;
        }

        return {
            formattedAddress: formattedAddress,
            post_code: locationMeta.postalCode.value,
            lat: locationMeta.lat,
            lng: locationMeta.lng
        };
    }
});
