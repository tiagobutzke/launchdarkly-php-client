var VendorGeocodingView = HomeSearchView.extend({
    events: {
        'click .teaser__button': '_submit',
        'keyup input': '_hideBalloon',
        'autocomplete:submit_pressed .teaser__button': '_submitPressed'
    },

    initialize: function (options) {
        this.vendorId = options.vendorId;
        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.domObjects.$body = options.$body;
        this.vendorCartModel = options.cartModel.getCart(this.vendorId);

        this.listenTo(this.vendorCartModel, 'invalid', this._alarmNoPostcode);

        HomeSearchView.prototype.initialize.apply(this, arguments);
    },

    unbind: function () {
        delete this.domObjects.$header;

        HomeSearchView.prototype.unbind.apply(this, arguments);
    },

    _search: function (data) {
        console.log('_search ', this.cid);
        if (_.isObject(data)) {
            this._disableInputNode();
            this._requestIsDeliverable(data)
                .done($.proxy(function (result) {
                    this._parseIsDeliverable(result, data);
                }, this))
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

    _updateCartModel: function (data) {
        this.vendorCartModel.set('location', {
            location_type: "polygon",
            latitude: data.lat,
            longitude: data.lng
        });
    },

    _requestIsDeliverable: function (data) {
        var deferred = $.get(
            Routing.generate('vendor_delivery_validation_by_gps', {
                vendorId: this.vendorId,
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
        return $.ajax({
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
        });
    },

    _alarmNoPostcode: function (event) {
        if (event.validationError === 'location_not_set') {
            this._showInputPopup(_.template($('#template-vendor-supply-postcode').html()));
        }
    },

    _parseIsDeliverable: function (response, data) {
        if (response.result) {
            this._saveLocation(data).always($.proxy(function () {
                this._updateLocation(data);
            }, this));
        }
        else {
            var url = Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                latitude: data.lat,
                longitude: data.lng,
                address: data.formattedAddress,
                postcode: data.postcode
            });

            var template = _.template($('#template-vendor-menu-nothing-found').html());
            this._showInputPopup(template({
                url: url
            }));
            this._enableInputNode();
        }
    },

    _updateLocation: function (data) {
        this._updateGeocodingBox(data);
        this._updateCartModel(data);
    },

    _applyNewLocationData: function (locationMeta, $input) {
        var data = this._getDataFromMeta(locationMeta);
        $input.val(data.formattedAddress);

        return data;
    }
});
