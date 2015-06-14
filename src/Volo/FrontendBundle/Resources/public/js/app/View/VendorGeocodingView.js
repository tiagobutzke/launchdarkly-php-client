/**
 * model: LocationModel
 * options:
 * - modelCart: VendorCartModel
 */
var VendorGeocodingView = HomeSearchView.extend({
    initialize: function (options) {
        _.bindAll(this);
        this.modelCart = options.modelCart;

        this.listenTo(this.modelCart, 'invalid', this._alarmNoPostcode);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this.performDeliverableCheck);

        HomeSearchView.prototype.initialize.apply(this, arguments);

        console.log('VendorGeocodingView:init', this.model);

        this.performDeliverableCheck();
    },

    /**
     * @override
     */
    postInit: function() {
        this.modelCart.set('location', this.modelCart.defaults.location);
        if (this.model.isValid()) {
            this.modelCart.set('location', {
                location_type: this.model.defaults.location_type,
                latitude:  this.model.get('latitude'),
                longitude: this.model.get('longitude')
            });
        }
    },

    performDeliverableCheck: function () {
        console.log('performDeliverableCheck ', this.cid);
        if (this.model.isValid() && !this.modelCart.isValid()) {
            this.modelCart.updateLocationIfDeliverable(this.model.toJSON())
                .done(this._parseIsDeliverable)
                .fail(this.onSearchFail);
        } else {
            this.onSearchFail();
        }
    },

    onSearchFail: function () {
        this._showInputPopup(_.template($('#template-vendor-is-deliverable-server-error').html()));
        this._enableInputNode();
    },

    _applyNewLocationData: function (locationMeta) {
        HomeSearchView.prototype._applyNewLocationData.apply(this, arguments);
        this._submitPressed();
    },

    _showFormattedAddress: function() {
        console.log('_showFormattedAddress ', this.cid);
        this.$('.vendor__geocoding__tool-box__title').removeClass('hide');
        this.$('.input__postcode').addClass('hide');
        this._hideTooltip();
    },

    _hideFormattedAddress: function() {
        console.log('_hideFormattedAddress ', this.cid);
        this.$('.vendor__geocoding__tool-box__title').addClass('hide');
        this.$('.input__postcode').removeClass('hide');
    },

    /**
     * @override
     * @private
     */
    _afterSubmit: function() {
        this._disableInputNode();
        this.performDeliverableCheck();
    },

    _alarmNoPostcode: function (event) {
        if (event.validationError === 'location_not_set') {
            this._hideFormattedAddress();
            this._showInputPopup(_.template($('#template-vendor-supply-postcode').html()));
        }
    },

    _parseIsDeliverable: function (response) {
        var data = this.model.toJSON();
        if (response.result) { // is deliverable
            this.$('.location__address').html(this.model.get('postcode'));
            this._showFormattedAddress();
            this.model.save();
        } else {
            var url = Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                latitude: data.latitude,
                longitude: data.longitude,
                address: data.postcode + ", " + data.city,
                postcode: data.postcode
            });

            var template = _.template($('#template-vendor-menu-nothing-found').html());
            this._enableInputNode();
            this._showInputPopup(template({url: url}));
        }
    }
});
