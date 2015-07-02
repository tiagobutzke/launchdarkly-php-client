/**
 * model: LocationModel
 * options:
 * - geocodingService: GeocodingService
 */
var HomeSearchView = Backbone.View.extend({
    initialize: function (options) {
        console.log('HomeSearchView.initialize ', this.cid);
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.geocodingService.init(this.$('#postal_index_form_input'));

        this.isBelowMediumScreen = options.isBelowMediumScreen;

        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._applyNewLocationData);
        this.listenTo(this.geocodingService, 'autocomplete:not_found', this._notFound);

        this.postInit();
    },

    events: {
        'click .teaser__button': '_submitPressed',
        'submit': '_submitPressed',
        'focus #postal_index_form_input': '_hideTooltip',
        'blur #postal_index_form_input': '_hideTooltip',
        'click #postal_index_form_input': '_scrollToInput',
        'keydown #postal_index_form_input': '_inputChanged'
    },

    unbind: function() {
        this.$('#postal_index_form_input').tooltip('destroy');
        this.geocodingService.removeListeners(this.$('#postal_index_form_input'));
        this.stopListening();
        this.undelegateEvents();
        this.$('#postal_index_form_input').val('');
    },

    postInit: function() {
        this.model.set(this.model.defaults);
    },

    _notFound: function() {
        var value = this.$('#postal_index_form_input').val() || '';
        if (value !== '') {
            console.log('not found');
            this._showInputPopup(this.$('#postal_index_form_input').data('msg_error_not_found'));
        }

        this.model.set(this.model.defaults);

        return false;
    },

    _scrollToInput: function() {
        var md = new MobileDetect(window.navigator.userAgent);
        if (md.mobile()) {
            $('html, body').animate({
                scrollTop: this.$('#postal_index_form_input').offset().top - ($('.header').height() + 10)
            }, VOLO.configuration.anchorScrollSpeed);
        }
    },

    _inputChanged: function() {
        if(this.model.get('formattedAddress') !== this.$('#postal_index_form_input').val()) {
            this.model.set(this.model.defaults);
        }

        this._hideTooltip();
    },

    _submitPressed: function() {
        console.log('_submitPressed ', this.cid);
        console.log(this.model.toJSON());
        if (this.model.get('postcode') && this.model.get('city')) {
            this._afterSubmit();
        }

        if (this.$('#postal_index_form_input').val() === '') {
            this._showInputPopup(this.$('#postal_index_form_input').data('msg_error_empty'));
        }

        return false;
    },

    _afterSubmit: function() {
        var data = this.model.toJSON();
        Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
            city: data.city,
            address: data.address,
            longitude: data.longitude,
            latitude: data.latitude,
            postcode: data.postcode
        }));
    },

    _applyNewLocationData: function (locationMeta) {
        var data = this._getDataFromMeta(locationMeta);

        this._hideTooltip();

        this.$('#postal_index_form_input').val(data.formattedAddress);

        if (locationMeta.postcodeGuessed) {
            console.log('locationMeta.postcodeGuessed ', locationMeta.postcodeGuessed);
            this._showInputPopup(this.$('#postal_index_form_input').data('msg_you_probably_mean'));
        }

        this.model.set({
            formattedAddress: data.formattedAddress,  // address in input field
            latitude: data.lat,
            longitude: data.lng,
            postcode: data.postcode,
            city: data.city,
            address: data.postcode + ", " + data.city // address in query param
        });

        dataLayer.push({
            'zipcode': data.postcode,
            'city': data.city
        });
    },

    _getDataFromMeta: function (locationMeta) {
        var formattedAddress = locationMeta.formattedAddress;

        if (!formattedAddress.match(locationMeta.postalCode.value)) {
            formattedAddress = locationMeta.postalCode.value + ", " + locationMeta.city;
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
        var $postalIndexFormInput = this.$('#postal_index_form_input'),
            placement = this.isBelowMediumScreen() ? 'top' : 'bottom',
            $tooltip;

        if (!$postalIndexFormInput.hasClass('hide')) {
            console.log('_showInputPopup ', this.cid);
            $tooltip = this.$('#postal_index_form_input').tooltip({
                placement: placement,
                html: true,
                trigger: 'manual',
                title: text,
                animation: false
            });

            $tooltip.on('shown.bs.tooltip', function() {
                this.$('#postal_index_form_input').data('bs.tooltip').$tip.css('left', 0);
            }.bind(this));

            $tooltip.tooltip('show');
        }
    },

    _hideTooltip: function () {
        console.log('_hideTooltip');
        this.$('#postal_index_form_input').tooltip('destroy');
    },

    _enableInputNode: function () {
        this.$('#postal_index_form_input').css('opacity', '1').attr('disabled', false).focus();
    },

    _disableInputNode: function () {
        this.$('#postal_index_form_input').css('opacity', '.4').attr('disabled', 'true');
    }
});
