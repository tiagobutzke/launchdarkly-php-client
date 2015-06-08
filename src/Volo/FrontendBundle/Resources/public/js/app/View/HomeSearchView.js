var HomeSearchView = Backbone.View.extend({
    initialize: function (options) {
        console.log('HomeSearchView.initialize ', this.cid);
        _.bindAll(this);
        this.geocodingService = options.geocodingService;

        var $input = this.$('#postal_index_form_input');

        this.geocodingService.init($input);

        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);
    },

    events: {
        'click .teaser__button': '_submit',
        'autocomplete:submit_pressed .teaser__button': '_submitPressed'
    },

    unbind: function() {
        this.$('#postal_index_form_input').tooltip('destroy');
        this.geocodingService.removeListeners(this.$('#postal_index_form_input'));
        this.stopListening();
        this.undelegateEvents();
    },

    _tabPressed: function() {
        console.log('_tabPressed ', this.cid);
        this._getNewLocation(this.$('#postal_index_form_input')).fail(this._notFound);
    },

    _submitPressed: function() {
        console.log('_submitPressed ', this.cid);
        this._getNewLocation(this.$('#postal_index_form_input')).done(this._search);
    },

    _notFound: function() {
        console.log('not found');
        this._showTooltip(this.$('#postal_index_form_input').data('msg_error_not_found'));
    },

    _search: function(data) {
        console.log('_search ', this.cid);
        if (!!data && data.postcode) {
            Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                address: data.formattedAddress,
                longitude: data.lng,
                latitude: data.lat,
                postcode: data.postcode
            }));
        } else {
            this._notFound();
        }
    },

    _submit: function() {
        console.log('_submit ', this.cid);
        this._getNewLocation(this.$('#postal_index_form_input')).done(this._search);
    },

    _getNewLocation: function($input) {
        console.log('_getNewLocation ', this.cid);
        var deferred = $.Deferred();

        this.geocodingService.getLocation($input)
            .fail(deferred.reject, this)
            .done(function(locationMeta) {
                console.log('_getNewLocation.done ', this.cid);

                var data = this._getDataFromMeta(locationMeta);
                $input.val(data.formattedAddress);
                this._showTooltip(this.$('#postal_index_form_input').data('msg_you_probably_mean'));

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
            postcode: locationMeta.postalCode.value,
            lat: locationMeta.lat,
            lng: locationMeta.lng,
            city: locationMeta.city
        };
    },

    _showTooltip: function (title) {
        var options = {trigger: 'manual', title: title};
        
        this.$('#postal_index_form_input').tooltip(options);
        this.$('#postal_index_form_input').tooltip('show');
    }
});
