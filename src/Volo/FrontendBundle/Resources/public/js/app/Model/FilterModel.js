VOLO.FilterModel = Backbone.Model.extend({
    defaults: {
        cuisines: '',
        food_characteristics: ''
    }
});

VOLO.VendorModel = Backbone.Model.extend({
    defaults: {
    }
});

VOLO.filterModel = new VOLO.FilterModel();

VOLO.FilterVendorCollection = Backbone.Collection.extend({
    model: VOLO.VendorModel,
    initialize: function (data, options) {
        _.bindAll(this);
    },

    url: function() {
        return Routing.generate('api_restaurants_list', {cuisine: VOLO.filterModel.get('cuisines'), food_characteristic: VOLO.filterModel.get('food_characteristics')});
    },

    parse: function(response, options) {
        return response.items;
    }
});
