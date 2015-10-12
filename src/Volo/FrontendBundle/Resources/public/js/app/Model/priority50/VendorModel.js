VOLO = VOLO || {};

VOLO.VendorModel = Backbone.Model.extend({
    defaults: {
        metadata: {
            availableIn: null
        }
    },

    initialize: function () {
        _.bindAll(this);
    },

    isOpen: function() {
        return this.get('metadata').availableIn === null;
    }
});
