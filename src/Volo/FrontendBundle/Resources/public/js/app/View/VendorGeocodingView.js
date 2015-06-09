var VendorGeocodingView = HomeSearchView.extend({
    initialize: function (options) {
        this.listenTo(this.model, 'invalid', this._alarmNoPostcode);

        HomeSearchView.prototype.initialize.apply(this, arguments);
    },

    _search: function (data) {
        console.log('_search ', this.cid);
        if (_.isObject(data)) {
            this._disableInputNode();
            this._requestIsDeliverable(data)
                .done(_.curry(this._parseIsDeliverable)(data))
                .fail(this._enableInputNode);
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

    _updateGeocodingBox: function (data) {
        this.$('.location__address').html(data.formattedAddress);
        this.$('.vendor__geocoding__tool-box__title').removeClass('hide');
        this.$('.input__postcode').addClass('hide');
    },

    _requestIsDeliverable: function (data) {
        var deferred = $.get(
            Routing.generate('vendor_delivery_validation_by_gps', {
                vendorId: this.model.get('vendor_id'),
                latitude: data.lat,
                longitude: data.lng
            })
        );

        deferred.fail(function (e) {
            this._showInputPopup(_.template($('#template-vendor-is-deliverable-server-error').html()));
        });

        return deferred;
    },

    _saveLocation: function (location) {
        $.ajax({
            url: Routing.generate('volo_customer_set_location'),
            data: {
                city: location.city,
                latitude: location.lat,
                longitude: location.lng,
                address: location.formattedAddress,
                postcode: location.postcode,
                _method: 'PUT'
            },
            method: 'PUT'
        }).always(function () {
            this._updateGeocodingBox(location);

            this.model.set('location', {
                location_type: "polygon",
                latitude: location.lat,
                longitude: location.lng
            });
        });
    },

    _alarmNoPostcode: function (event) {
        if (event.validationError === 'location_not_set') {
            this._showInputPopup(_.template($('#template-vendor-supply-postcode').html()));
        }
    },

    _parseIsDeliverable: function (data, response) {
        if (response.result) {
            this._saveLocation(data);
        } else {
            var url = Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                latitude: data.lat,
                longitude: data.lng,
                address: data.formattedAddress,
                postcode: data.postcode
            });

            var template = _.template($('#template-vendor-menu-nothing-found').html());
            this._enableInputNode();
            this._showInputPopup(template({url: url}));
        }
    },

    _applyNewLocationData: function (locationMeta, $input) {
        var data = this._getDataFromMeta(locationMeta);
        $input.val(data.formattedAddress);

        return data;
    }
});
