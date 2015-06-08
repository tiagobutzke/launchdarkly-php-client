var HomeSearchView = Backbone.View.extend({
    initialize: function (options) {
        console.log('HomeSearchView.initialize ', this.cid);
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.domObjects = this.domObjects || {};
        this.domObjects.$body = options.$body;
        this.inputNode = this.$('#postal_index_form_input');

        this.geocodingService.init(this.inputNode);

        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);

        this.inputNode.tooltip({
            title: '',
            placement: 'bottom',
            html: true,
            trigger: 'manual'
        });
        this.domObjects.$body.on('click', $.proxy(this._bodyClickHandler, this));
    },

    events: {
        'click .teaser__button': '_submit',
        'autocomplete:submit_pressed .teaser__button': '_submitPressed'
    },

    unbind: function() {
        this.geocodingService.removeListeners(this.inputNode);
        this.stopListening();
        this.undelegateEvents();
        this.inputNode.tooltip('destroy');
        this.domObjects.$body.unbind($.proxy(this._bodyClickHandler, this));
        delete this.domObjects.$body;
        delete this.inputNode;
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
        this._showInputPopup(this.$('#postal_index_form_input').data('msg_error_not_found'));
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
                deferred.resolve(this._applyNewLocationData(locationMeta, $input));
            }.bind(this));

        return deferred;
    },

    _applyNewLocationData: function (locationMeta, $input) {
        var data = this._getDataFromMeta(locationMeta);
        $input.val(data.formattedAddress);
        this._showInputPopup(this.$('#postal_index_form_input').data('msg_you_probably_mean'));

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

    _hideBalloon: function () {
        this.inputNode.tooltip('hide');
    },

    _showInputPopup: function (text) {
        this.inputNode.attr('title', text).tooltip('fixTitle');
        setTimeout($.proxy(function () {
            this.inputNode.tooltip('show');
        }, this), 10);
    },

    _bodyClickHandler: function (e) {
        if (!$(e.target).hasClass('tooltip-inner')) {
            this.inputNode.tooltip('hide');
        }
    }
});
