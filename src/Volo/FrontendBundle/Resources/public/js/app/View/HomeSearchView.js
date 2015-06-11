var HomeSearchView = Backbone.View.extend({
    initialize: function (options) {
        console.log('HomeSearchView.initialize ', this.cid);
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.inputNode = this.$('#postal_index_form_input');

        this.geocodingService.init(this.inputNode);

        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);

        this.inputNode.tooltip({
            placement: 'bottom',
            html: true,
            trigger: 'manual'
        });
    },

    events: {
        'click .teaser__button': '_submit',
        'autocomplete:submit_pressed .teaser__button': '_submitPressed',
        'focus #postal_index_form_input': '_hideTooltip',
        'blur #postal_index_form_input': '_hideTooltip'
    },

    unbind: function() {
        this.inputNode.tooltip('destroy');
        this.geocodingService.removeListeners(this.inputNode);
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
        var value = this.$('#postal_index_form_input').val() || '';
        if (value !== '') {
            console.log('not found');
            this._showInputPopup(this.$('#postal_index_form_input').data('msg_error_not_found'));
        }
    },

    _search: function(data) {
        console.log('_search ', this.cid);
        if (!!data && data.postcode) {
            this.model.set({latitude: data.lat, longitude: data.lng, formattedAddress: data.formattedAddress});
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
                deferred.resolve(this._applyNewLocationData(locationMeta, $input));
            }.bind(this));

        return deferred;
    },

    _applyNewLocationData: function (locationMeta, $input) {
        var data = this._getDataFromMeta(locationMeta);
        $input.val(data.formattedAddress);

        if (locationMeta.postcodeGuessed) {
            this._showInputPopup(this.$('#postal_index_form_input').data('msg_you_probably_mean'));
        }

        return data;
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

    _showInputPopup: function (text) {
        this.inputNode.attr('title', text).tooltip('fixTitle');
        this.inputNode.tooltip('show');

        var newPosition = this.inputNode.position().left;

        $('.tooltip').css('left', newPosition + 'px');
        $('.tooltip').css('visibility', 'visible');
    },

    _hideTooltip: function () {
        this.inputNode.tooltip('hide');
    }
});
