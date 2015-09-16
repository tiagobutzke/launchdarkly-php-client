if (window.trackJs) {
    ["View", "Model", "Collection", "Router"].forEach(function(klass) {
        var Klass = Backbone[klass];
        Backbone[klass] = Klass.extend({
            constructor: function() {
                // NOTE: This allows you to set _trackJs = false for any individual object
                //       that you want excluded from tracking
                if (typeof this._trackJs === "undefined") {
                    this._trackJs = true;
                }

                if (this._trackJs) {
                    // Additional parameters are excluded from watching. Constructors and Comparators
                    // have a lot of edge-cases that are difficult to wrap so we'll ignore them.
                    window.trackJs.watchAll(this, "model", "constructor", "comparator");
                }

                return Klass.prototype.constructor.apply(this, arguments);
            }
        });
    });
}
