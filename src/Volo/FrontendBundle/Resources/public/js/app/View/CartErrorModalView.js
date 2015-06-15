var CartErrorModalView = Backbone.View.extend({
    events: {
        'hide.bs.modal': '_reloadPage'
    },

    supportedErrors: [
        'ApiInvalidParameterException',
        'ApiObjectDoesNotExistException'
    ],

    initialize: function () {
        console.log('CartErrorModalView.initialize', this.cid);
        _.bindAll(this);

        this.vendor_id = this.$el.data().vendor_id;

        this.$el.modal({show: false});

        this.listenTo(this.model.getCart(this.vendor_id), 'cart:error', this._cartCalculationErrorShowModal, this);
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    _cartCalculationErrorShowModal: function (data) {
        if (_.isUndefined(data) ||
            (_.isObject(data) && _.indexOf(this.supportedErrors, data.error.errors.exception_type) !== -1)
        ) {
            this.$el.modal('show');
        }
    },

    _reloadPage: function () {
        location.reload();
    }
});
