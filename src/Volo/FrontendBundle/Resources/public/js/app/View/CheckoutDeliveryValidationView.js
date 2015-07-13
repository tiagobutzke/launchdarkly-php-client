var CheckoutDeliveryValidationView = Backbone.View.extend({
    events: {
        "submit": '_submit',
        'focus #postal_index_form_input': '_hideTooltip',
        'click': '_hideTooltip',
        'change #city, #postal_index_form_input': '_geoCodeAndValidateDelivery',
        "blur #address_line1, #address_line2": '_geoCodeAndValidateDelivery',
        "keyup #address_line1, #address_line2":'_validateForm'
    },

    initialize: function (options) {
        var $postalIndexFormInput = this.$('#postal_index_form_input');

        _.bindAll(this);
        // @TODO: re-enable this later when it works better
        //this.geocodingService = options.geocodingService;
        this.postalCodeGeocodingService = options.postalCodeGeocodingService;
        this.vendorId = $postalIndexFormInput.data('vendor_id');
        // @TODO: re-enable this later when it works better
        // this.geocodingService.init(this.$('#formatted_address'), []);
        var locationObject = {
            latitude:  options.locationModel.get('latitude'),
            longitude: options.locationModel.get('longitude')
        };
        //this.geocodingService.setLocation(locationObject);

        this.locationModel = options.locationModel;
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
        $postalIndexFormInput.tooltip({
            placement: 'top',
            html: true,
            trigger: 'manual'
        });
        this.tooltipAlignLeft($postalIndexFormInput);
        // @TODO: re-enable this later when it works better
        //this.listenTo(this.geocodingService, 'autocomplete:place_changed', this.onPlaceChanged);
        this._geoCodeAndValidateDelivery();
    },

    _geoCodeAndValidateDelivery: function () {
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
            this.$('#error_msg_delivery_not_saved').addClass('hide');

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

    _toggleSubmitButtonDisabled: function(disabledState) {
        console.log('_toggleSubmitButtonDisabled ', disabledState);
        this.$("#delivery_information_form_button").attr('disabled', disabledState);
    },

    _validateAddressFields: function () {
        var street = this.$('#address_line1').val(),
            houseNum = this.$('#address_line2').val(),
            streetValid = street !== '',
            houseNumValid = houseNum !== '';

        return streetValid && houseNumValid;
    },

    isValidForm: function() {
        return this.$('#address_line1').val().length > 0 && this.$('#address_line2').val().length > 0 &&
            this.$('#postal_index_form_input').val().length > 0 && this.$('#city').val().length > 0;
    },

    _validateForm: function() {
        this._toggleSubmitButtonDisabled(!this.isValidForm());
    },

    _geocodeAddress: function() {
        this._toggleSubmitButtonDisabled(true);
        if (!this.isValidForm()) {
            return;
        }

        this.postalCodeGeocodingService.geocodeAddress({
            address: this.$('#address_line1').val() + ' ' + this.$('#address_line2').val() + ', ' + this.$('#postal_index_form_input').val() + ', ' + this.$('#city').val(),

            success: function (result) {
                console.log(result);
                this.$("#address_latitude").val(result.lat());
                this.$("#address_longitude").val(result.lng());
            }.bind(this),

            error: function () {
                this.$("#address_latitude").val('');
                this.$("#address_longitude").val('');
            }.bind(this)
        });
    },

    _geocodePostalCode: function (locationData) {
        console.log('_geocodePostalCode ', this.cid, locationData);
        this._geocodeAddress();

        this.postalCodeGeocodingService.geocodeCenterPostalcode({
            address: locationData.postcode + ", " + locationData.city,
            postalCode: locationData.postcode,
            city: locationData.city,

            success: function (result) {
                this._validateDelivery({lat: result.lat(), lng: result.lng()});
            }.bind(this),

            error: function (results, status) {
                var postalCode;
                if (results && results.length > 0) {
                    postalCode = _.findWhere(results[0].address_components, {types: ['postal_code']});
                }

                if (_.isString(status) && status === 'ZERO_RESULTS') {
                    this._showInputPopup(this.$('#postal_index_form_input').data('validation-msg'), true);
                    this._toggleSubmitButtonDisabled(true);
                } else if (_.get(postalCode, 'long_name') === this.locationModel.get('postcode')) {
                    this._validateDelivery({lat: this.locationModel.get('latitude'), lng: this.locationModel.get('longitude')});
                } else {
                    this._toggleSubmitButtonDisabled(!this.isValidForm());
                }
            }.bind(this)
        });
    },

    _validateDelivery: function (locationData) {
        if ($('#delivery-modal').hasClass('in')) {
            return;
        }

        this._hideTooltip();

        var deliveryCheckData = {
            vendorId: this.vendorId,
            latitude: locationData.lat,
            longitude: locationData.lng
        };
        this.deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                if (resultData.result === true) {
                    if (this.$('#postal_index_form_input').data('is-blocking-popup')) {
                        this._hideTooltip();
                    }
                    this._toggleSubmitButtonDisabled(!this._validateAddressFields());
                } else {
                    this._showInputPopup(this.$('#postal_index_form_input').data('validation-msg'), true);
                    this._toggleSubmitButtonDisabled(true);
                }

            }.bind(this));
    }
});

_.extend(CheckoutDeliveryValidationView.prototype, VOLO.TooltipAlignMixin);
