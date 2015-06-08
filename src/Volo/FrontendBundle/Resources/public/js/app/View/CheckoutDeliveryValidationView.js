var CheckoutDeliveryValidationView = Backbone.View.extend({
    events: {},

    initialize: function (options) {
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.vendorId = this.$('#postal_index_form_input').data('vendor_id');
        this.geocodingService.init(this.$('#address_line1'), []);
        this.deliveryCheck = options.deliveryCheck;
        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
    },

    unbind: function () {
        this.geocodingService.removeListeners(this.$('#postal_index_form_input'));
        this.stopListening();
        this.undelegateEvents();
    },

    _tabPressed: function () {
        this._getNewLocation(this.$('#postal_index_form_input')).fail(this._notFound);
    },

    _notFound: function () {
        console.log('not found');
    },

    _getNewLocation: function ($input) {
        var deferred = $.Deferred();
        var continueButton = this.$("#delivery_information_form_button");
        this.geocodingService.getLocation($input)
            .fail(deferred.reject, this)
            .done(function (locationMeta) {
                var data = this._getDataFromMeta(locationMeta, $input);
                var addressLine1 = data.street + ' ' + data.building;

                this.$("#city").val(data.city);
                this.$('#postal_index_form_input').val(data.postcode);
                this._validateDelivery(
                    data,
                    continueButton
                );
                this.$('#address_line1').val($.trim(addressLine1));
                this.$('#address_line2').val(data.building);
                this.$('#address_latitude').val(data.lat);
                this.$('#address_longitude').val(data.lng);
                deferred.resolve(data);
            }.bind(this));

        return deferred;
    },

    _getDataFromMeta: function (locationMeta, $input) {
        console.log(locationMeta);
        var formattedAddress = locationMeta.formattedAddress;

        var postCode = (locationMeta.postalCode && locationMeta.postalCode.value) || $input.val();

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

    _validateDelivery: function (locationData, continueButton) {
        if ($('#delivery-modal').hasClass('in')) {
            return;
        }

        continueButton.attr('disabled', true);

        var deliveryCheckData = {
            vendorId: this.vendorId,
            latitude: locationData.lat,
            longitude: locationData.lng
        };
        this.deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                if (resultData.result === true) {
                    continueButton.attr('disabled', false);
                } else {
                    var view = new CheckoutNoDeliveryView({
                        el: '.modal-dialogs',
                        locationData: locationData
                    });
                    view.render(); //render dialog
                    $('#delivery-modal').modal();
                }
            });
    }
});

var CheckoutNoDeliveryView = Backbone.View.extend({
    initialize: function (options) {
        this.template = _.template($('#template-delivery-modal').html());
        this.locationData = options.locationData;
    },

    events: {
        'click .delivery-error__find-restaurants': '_findRestaurants',
        'click .modal-close-button': '_closeModal'
    },

    render: function () {
        this.$el.html(this.template());

        return this;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    },

    _findRestaurants: function () {
        var data = this.locationData;
        this.undelegateEvents();
        Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
            city: data.city,
            address: data.formattedAddress,
            longitude: data.lng,
            latitude: data.lat,
            postcode: data.postcode
        }));
    },

    _closeModal: function () {
        this.unbind();
        delete this.locationData;
        this.$('#delivery-modal').modal('hide');
    }
});

