var VOLO = VOLO || {};

VOLO.CartErrorModalView = Backbone.View.extend({
    events: {
        'hide.bs.modal': 'unbind'
    },

    supportedErrors: [
        'ApiInvalidParameterException',
        'ApiObjectDoesNotExistException'
    ],

    initialize: function (options) {
        console.log('CartErrorModalView.initialize', this.cid);
        _.bindAll(this);

        this.vendor_id = options.vendorId;

        this.template = _.template($('#template-cart-invalid-products-list').html());

        this.$el.modal({show: false, backdrop: 'static'});
    },

    unbind: function () {
        this._reset();
        this.stopListening();
        this.undelegateEvents();
    },

    _reset: function () {
        this._resetErrorHeadline();
        this._resetErrorMessage();
    },

    _resetErrorHeadline: function () {
        this.$('.modal__h4').empty();
    },

    _setErrorHeadline: function (headline) {
        this.$('.modal__h4').text(headline);
    },

    _resetErrorMessage: function () {
        this.$('.modal-error-cart__error-message').empty();
    },

    _setErrorMessage: function (message) {
        this.$('.modal-error-cart__error-message').html(message);
    },

    displayError: function (errorObject) {
        var errorHeadline = this.$('.modal__h4').data('default-error-headline'),
            errorMessage = this.$('.modal-error-cart__error-message').data('default-error-message');

        if (errorObject.ApiProductInvalidForVendorException) {
            errorMessage = this.template({
                invalidProducts: _.map(errorObject.invalidProducts, function (product) {
                    return product.get('name');
                })
            });
        }

        this._setErrorHeadline(errorHeadline);
        this._setErrorMessage(errorMessage);

        this.$el.modal('show');
    }
});
