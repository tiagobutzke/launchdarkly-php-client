var VOLO = VOLO || {};

VOLO.VendorModel = Backbone.Model.extend({
    defaults: {
        metadata: {
            available_in: null
        }
    },

    initialize: function () {
        _.bindAll(this);
    },

    isOpen: function() {
        return this.get('metadata').available_in === null;
    }
});

VOLO.VendorCollection = Backbone.Collection.extend({
    model: VOLO.VendorModel,
    initialize: function (data, options) {
        _.bindAll(this);
        this.locationModel = options.locationModel;
        this.filterModel = options.filterModel;
        this.fuzzySearch = new Fuse([], {
            keys: ['name', 'description', 'cuisines.name', 'food_characteristics'],
            id: 'id',
            threshold: 0.25,
            distance: 1000
        });

    },

    url: function() {
        return Routing.generate(
            'api_vendors_list', {
                cuisine: this.filterModel.get('cuisines'),
                food_characteristic: this.filterModel.get('food_characteristics'),
                latitude: this.locationModel.get('latitude'),
                longitude: this.locationModel.get('longitude')
            }
        );
    },

    search: function (query) {
        this.fuzzySearch.set(this.toJSON());

        return this.fuzzySearch.search(query);
    }
});
