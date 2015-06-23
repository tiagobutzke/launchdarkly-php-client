var CheckoutDeliveryInformationView = Backbone.View.extend({
    events: {
        "click .delivery_address": 'changeAddress',
        "click #add_new_address_link": 'addAddressLink',
        "click #select_other_address_link": 'selectOtherAddressLink',
        "submit #delivery_information_form": 'addAddress'
    },

    initialize: function () {
        _.bindAll();

        if (this.model.get('is_guest_user')) {
            return; // as guest, nothing has to be done.
        }

        this.template = _.template(this.$('#template-delivery_address').html());
        this.addresses = this.$('#delivery-information-list').data('addresses');

        this.showForm = this.addresses.length === 0;
        this.showList = false;

        if (this.addresses.length > 0) {
            this.model.set('address_id', _.last(this.addresses).id);
        }

        this.listenTo(this.model, 'change:address_id', this.render, this);
    },

    render: function () {
        var selectedAddressId,
            $selectedAddress,
            $selectedAddressParent;

        this.$('#delivery-information-list').html('');
        _.forEach(this.addresses, function (address) {
            this.$('#delivery-information-list').append(this.template(address));
        }.bind(this));

        this.$('#select_other_address_link').toggleClass('hide', this.addresses.length < 2);
        this.$('#add_new_address_form').toggleClass('hide', !this.showForm);
        this.$el.toggleClass('delivery_information_list-shown', this.showForm);
        this.$('#delivery-information-list-wrapper').toggleClass('hide', this.showForm);
        this.$('#delivery-information-list').toggleClass('hide', !this.showList);

        selectedAddressId = this.model.get('address_id');
        if (!_.isNull(selectedAddressId)) {
            this.$('.selected-address').empty();
            $selectedAddress = this.$('.delivery_address[data-id="' + selectedAddressId + '"]');
            $selectedAddressParent = $selectedAddress.parent();
            this.$('.selected-address').append($selectedAddress);
            $selectedAddressParent.remove();
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    changeAddress: function (event) {
        this.showList = false;
        this.model.set('address_id', $(event.currentTarget).data('id'));
        this.$('#select_other_address_link').toggleClass('address-selection--open', this.showList);
        this.$('.selected-address').toggle(!this.showList);
    },

    addAddressLink: function (event) {
        this.showForm = !this.showForm;
        this.render();
        
        event.preventDefault();
    },

    selectOtherAddressLink: function (event) {
        this.showList = !this.showList;
        this.$('#select_other_address_link').toggleClass('address-selection--open', this.showList);
        this.$('.selected-address').toggle(!this.showList);
        this.render();

        event.preventDefault();
    },

    addAddress: function (event) {
        $.post(
            Routing.generate('checkout_create_address'),
            this.$('#delivery_information_form').serialize()
        ).done(function (data) {
                this.addresses = data;
                this.addresses.sort(function(a, b) {
                    return a.id - b.id;
                });

                this.showForm = false;
                this.showList = false;
                
                this.model.set('address_id', _.last(this.addresses).id);
            }.bind(this));

        event.preventDefault();
    }
});
