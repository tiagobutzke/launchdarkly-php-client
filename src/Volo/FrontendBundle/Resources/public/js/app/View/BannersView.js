VOLO.BannersView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);
        this.iOSBanner = new VOLO.IOSBannerView({
            el: '.ios-smart-banner',
            $body: this.$el
        });
        this.floodBanner = new VOLO.FloodBannerView({
            el: '.flood-banner',
            $body: this.$el,
            locationModel: options.locationModel,
            model: options.floodBannerModel
        });

        this.floodBannerModel = options.floodBannerModel;
        this.locationModel = options.locationModel;

        this.listenTo(this.iOSBanner, 'ios-banner:hide', this._updateFloodBannerState);
        this.listenTo(this.floodBanner, 'flood-banner:hide', this._hideAllBanners);
        this.listenTo(this.locationModel, 'change', this._locationChanged);
    },

    render: function() {
        if (this.iOSBanner.shouldBeDisplayed()) {
            this.iOSBanner.show();
        } else {
            this.floodBanner.shouldBeDisplayed().then(function(result) {
                result && this.floodBanner.show();
            }.bind(this), this._hideAllBanners);
        }

        return this;
    },

    _locationChanged: function() {
        this.floodBannerModel.set('hiddenByUser', false);

        if (this.iOSBanner.shouldBeDisplayed()) {
            return;
        }

        this.floodBanner.shouldBeDisplayed().then(function(result) {
            result && this.floodBanner.show();
        }.bind(this), this._hideAllBanners);
    },

    _updateFloodBannerState: function() {
        var shouldBeDisplayed = this.floodBanner.shouldBeDisplayed();

        shouldBeDisplayed.then(function(result) {
            if (result) {
                this.floodBanner.show();
            } else {
                this._hideAllBanners();
            }
        }.bind(this), this._hideAllBanners);
    },

    _hideAllBanners: function() {
        this.$el.removeClass('show-banner');
        //resize called because sticky parts of site needs to be updated
        this.$el.resize();
    },


    unbind: function() {
        this.stopListening();
        this.undelegateEvents();

        this.iOSBanner.unbind();
        this.floodBanner.unbind();
    }
});
