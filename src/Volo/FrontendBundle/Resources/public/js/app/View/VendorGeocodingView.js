var VendorGeocodingView = HomeSearchView.extend({
    initialize: function (options) {
        _.bindAll(this);
        this.locationModel = options.locationModel;

        this.listenTo(this.model, 'invalid', this._alarmNoPostcode);

        HomeSearchView.prototype.initialize.apply(this, arguments);

        console.log('VendorGeocodingView:init', this.locationModel);
    },

    performDeliverableCheck: function () {
        if (this.locationModel.isValid()) {
            if (!this.model.isValid()) {
                console.log('locationModal has location, checking deliverability');
                this.model.updateLocationIfDeliverable({
                    lat: this.locationModel.get('latitude'),
                    lng: this.locationModel.get('longitude')
                });
            } else {
                this._showFormattedAddress(this.locationModel.get('formattedAddress'));
            }
        }
    },

    onSearchFail: function () {
        this._showInputPopup(_.template($('#template-vendor-is-deliverable-server-error').html()));
        this._enableInputNode();
    },

    _showFormattedAddress: function(address) {
        this.$('.location__address').html(address);
        this.$('.vendor__geocoding__tool-box__title').removeClass('hide');
        this.$('.input__postcode').addClass('hide');
    },

    _search: function (data) {
        console.log('_search ', this.cid, data);

        if (!!data && data.postcode) {
            this.locationModel.set({
                latitude: data.lat,
                longitude: data.lng,
                formattedAddress: data.postcode + ", " + data.city
            });

            this._disableInputNode();

            this.model.updateLocationIfDeliverable(data)
                .done(function (response) {this._parseIsDeliverable(data, response);}.bind(this))
                .fail(this.onSearchFail);
        } else {
            this._notFound();
        }
    },

    _enableInputNode: function () {
        this.inputNode.css('opacity', '1').attr('disabled', false).focus();
    },

    _disableInputNode: function () {
        this.inputNode.css('opacity', '.4').attr('disabled', 'true');
    },

    _alarmNoPostcode: function (event) {
        if (event.validationError === 'location_not_set' && !this.inputNode.hasClass('hide')) {
            this._showInputPopup(_.template($('#template-vendor-supply-postcode').html()));
        }
    },

    _parseIsDeliverable: function (data, response) {
        if (response.result) {
            this.$('.location__address').html(data.postcode);
            this.$('.vendor__geocoding__tool-box__title').removeClass('hide');
            this.$('.input__postcode').addClass('hide');

            this.locationModel.saveLocation(data);
        } else {
            var url = Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                latitude: data.lat,
                longitude: data.lng,
                address: data.postcode + ", " + data.city,
                postcode: data.postcode
            });

            var template = _.template($('#template-vendor-menu-nothing-found').html());
            this._enableInputNode();
            this._showInputPopup(template({url: url}));
        }
    }
});
