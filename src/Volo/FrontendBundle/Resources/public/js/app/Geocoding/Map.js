VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.Map = function() {
    _.bindAll(this);
};
_.extend(VOLO.Geocoding.Map.prototype, Backbone.Events, {
    //map options can be found at https://developers.google.com/maps/documentation/javascript/reference?hl=en#MapOptions
    insert: function($node, options) {
        this.$node = $node;
        this.map = new google.maps.Map($node[0], options);

        this.map.addListener('dragend', _.debounce(this._triggerDragEnd, 200, {leading: false}));
        this.map.addListener('dragstart', this._triggerDragStart);
    },

    setCenter: function(lat, lng) {
        var latLng = new google.maps.LatLng(lat, lng);

        this.map.setCenter(latLng);
    },

    getCenter: function() {
        return this.map.getCenter();
    },

    resize: function() {
        google.maps.event.trigger(this.map, 'resize');
    },

    cleanLocation: function() {
        this.setCenter(0, 0);
    },

    unbind: function() {
        this.$node.unbind();

        google.maps.event.clearInstanceListeners(this.map);
        google.maps.event.clearListeners(this.$node[0]);
    },

    _triggerDragEnd: function() {
        console.log('center changed');
        this.trigger('map:center-changed', this.getCenter());
    },

    _triggerDragStart: function() {
        this.trigger('map:drag-start');
    }
});
