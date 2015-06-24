var CheckoutDeliveryValidationView = Backbone.View.extend({
    events: {
        "submit": '_submit',
        'focus #postal_index_form_input': '_hideTooltip',
        'click': '_hideTooltip',
        'change #postal_index_form_input': '_geoCodeAndValidateDelivery',
        'change #city': '_geoCodeAndValidateDelivery'
    },

    initialize: function (options) {
        _.bindAll(this);
        // @TODO: re-enable this later when it works better
        //this.geocodingService = options.geocodingService;
        this.postalCodeGeocodingService = options.postalCodeGeocodingService;
        this.vendorId = this.$('#postal_index_form_input').data('vendor_id');
        // @TODO: re-enable this later when it works better
        // this.geocodingService.init(this.$('#formatted_address'), []);
        var locationObject = {
            latitude:  options.locationModel.get('latitude'),
            longitude: options.locationModel.get('longitude')
        };
        //this.geocodingService.setLocation(locationObject);

        this.postalCodeGeocodingService.setLocation(locationObject);
        this.deliveryCheck = options.deliveryCheck;
        this._jsValidationView = new ValidationView({
            el: this.el,
            constraints: {
                "customer_address[postcode]": {
                    presence: true
                },
                "customer_address[city]": {
                    presence: true
                },
                "customer_address[address_line1]": {
                    presence: true
                },
                "customer_address[address_line2]": {
                    presence: true
                }
            }
        });
        this.$('#postal_index_form_input').tooltip({
            placement: 'top',
            html: true,
            trigger: 'manual'
        });
        // @TODO: re-enable this later when it works better
        //this.listenTo(this.geocodingService, 'autocomplete:place_changed', this.onPlaceChanged);

        this.$("#delivery_information_form_button").attr('disabled', true);

        this._geoCodeAndValidateDelivery();
    },

    _geoCodeAndValidateDelivery: function() {
        this._geocodePostalCode({
            city: this.$('#city').val(),
            postcode: this.$('#postal_index_form_input').val()
        });
    },

    unbind: function () {
        this._jsValidationView.unbind();
        //this.geocodingService.removeListeners(this.$('#postal_index_form_input'));
        this.stopListening();
        this.undelegateEvents();
    },

    _submit: function () {
        var submitAllowed = $(document.activeElement).attr('id') !== 'formatted_address' &&
            !this.$("#delivery_information_form_button").attr('disabled');

        if (submitAllowed) {
            this.trigger('submit:successful_before', {
                deliveryTime: $('#order-delivery-time').val()
            });
        }

        return submitAllowed;
    },

    onPlaceChanged: function (locationMeta) {
        var data = this._getDataFromMeta(locationMeta);
        var addressLine1 = data.street + ' ' + data.building;

        this._hideTooltip();

        this.$("#city").val(data.city);
        this.$('#postal_index_form_input').val(data.postcode);
        this.$('#formatted_address').val($.trim(addressLine1));
        this.$('#address_line1').val(data.street);
        this.$('#address_line2').val(data.building);
        this.$('#address_latitude').val(data.lat);
        this.$('#address_longitude').val(data.lng);
        this._geocodePostalCode(data);
    },

    _getDataFromMeta: function (locationMeta) {
        console.log(locationMeta);
        var postCode = (locationMeta.postalCode && locationMeta.postalCode.value) || this.$('#postal_index_form_input').val();

        var formattedAddress = postCode + ", " + locationMeta.city;

        return {
            formattedAddress: formattedAddress,
            postcode: postCode,
            building: locationMeta.building,
            street: locationMeta.street,
            lat: locationMeta.lat,
            lng: locationMeta.lng,
            city: locationMeta.city
        };
    },

    _hideTooltip: function () {
        this.$('#postal_index_form_input').tooltip('hide');
    },

    _showInputPopup: function (text, isBlocking) {
        this.$('#postal_index_form_input').attr({
            'data-is-blocking-popup': _.isUndefined(isBlocking) ? false : isBlocking,
            'title': text
        }).tooltip('fixTitle');

        this.$('#postal_index_form_input').tooltip('show');
    },

    _validateAddressFields: function () {
        if (this.$('#address_line1').val() === '' || this.$('#address_line2').val() === '') {
            // this._showInputPopup(this.$('#formatted_address').data('msg_ensure_full_address'));
        }
    },

    _geocodePostalCode: function (locationData) {
        var that = this;
        this.postalCodeGeocodingService.geoCodePostalCode({
            postalCode: locationData.postcode,
            city: locationData.city,
            success: function (result) {
                that._validateDelivery({lat: result.lat(), lng: result.lng()});
            },
            error: function (results, status) {
                if (_.isString(status) && status === 'ZERO_RESULTS') {
                    that.$("#delivery_information_form_button").attr('disabled', true);
                    that._showInputPopup(that.$('#postal_index_form_input').data('validation-msg'), true);
                } else {
                    // if Google can't geo-code it, who are we to stop the user!!!, just consider it valid man :)
                    that.$("#delivery_information_form_button").attr('disabled', false);
                }
            }
        });
    },

    _validateDelivery: function (locationData) {
        var continueButton = this.$("#delivery_information_form_button");

        if ($('#delivery-modal').hasClass('in')) {
            return;
        }

        continueButton.attr('disabled', true);
        this._hideTooltip();

        var deliveryCheckData = {
            vendorId: this.vendorId,
            latitude: locationData.lat,
            longitude: locationData.lng
        };
        this.deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                if (resultData.result === true) {
                    continueButton.attr('disabled', false);
                    if (this.$('#postal_index_form_input').data('is-blocking-popup')) {
                        this._hideTooltip();
                    }
                    this._validateAddressFields();
                } else {
                    this._showInputPopup(this.$('#postal_index_form_input').data('validation-msg'), true);
                }
            }.bind(this));
    }
});
