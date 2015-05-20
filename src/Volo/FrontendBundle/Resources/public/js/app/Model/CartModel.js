var CartItemModel = Backbone.Model.extend({
    defaults: {
        "product_variation_id": null,
        "name": "",
        "variation_name": "",
        "total_price_before_discount": 0,
        "total_price": 0,
        "quantity": 0,
        "toppings": [],
        "choices": [],
        "group_order_user_name": null,
        "group_order_user_code": null
    }
});

var CartProductCollection = Backbone.Collection.extend({
    model: CartItemModel
});

var VendorCartModel = Backbone.Model.extend({
    defaults: {
        "subtotal": 0,
        "subtotal_before_discount": 0,
        "subtotal_after_product_discount": 0,
        "subtotal_after_discount": 0,
        "subtotal_after_discount_and_delivery_fee": 0,
        "subtotal_after_discount_and_service_fee": 0,
        "subtotal_after_discount_and_delivery_fee_and_service_fee": 0,
        "total_value": 0,
        "group_joiner_total": 0,
        "container_charge": 0,
        "delivery_fee": 0,
        "vat_total": 0,
        "voucher_total": 0,
        "discount_total": 0,
        "delivery_fee_discount": 0,
        "service_tax_total": 0,
        "service_fee_total": 0,
        "vendor_id": null,
        "minimum_order_amount": 0,
        "minimum_order_amount_difference": 0,
        "discount_text": null
    },
    idAttribute: 'vendor_id',

    initialize: function() {
        this.products = new CartProductCollection();
        this._xhr = null;
        this._lastClick = Date.now();
    },

    addItem: function(newProduct, quantity) {
        this.trigger('cart:dirty');
        this._updateProducts(newProduct, quantity);
        this._lastClick = Date.now();

        if (this._xhr) {
            this._xhr.abort();
        }

        setTimeout(function() {
            if (Date.now() - this._lastClick > 490) {
                this._updateCart();
            }
        }.bind(this), 500);
    },

    _updateCart: function() {
        this._xhr = this.collection.cart.dataProvider.calculateCart({
            products: this.products.toJSON(),
            vendor_id: this.id,
            location: {
                "location_type": "polygon",
                "latitude": 52.5237282,
                "longitude": 13.3908286
            }
        }).done(function(calculatedData) {
            var vendorCart = _.cloneDeep(calculatedData.vendorCart[0]);
            delete calculatedData.vendorCart;

            this.collection.cart.set(calculatedData);
            this.products.set(vendorCart.products);
            this.trigger('cart:ready');
        }.bind(this));
    },

    _updateProducts: function(newProduct, quantity) {
        var foundProduct = this.findSimilarProduct(newProduct);
        if (_.isObject(foundProduct)) {
            foundProduct.set('quantity', parseInt(foundProduct.get('quantity') + quantity), 10);
        }

        if (_.isUndefined(foundProduct)) {
            var productVariation = newProduct.product_variations[0];
            this.products.add((new CartItemModel({
                product_variation_id: productVariation.id,
                quantity: quantity,
                toppings: _.cloneDeep(productVariation.toppings),
                choices: _.cloneDeep(productVariation.choices),
                name: newProduct.name
            })).toJSON());
        }
    },

    findSimilarProduct: function(productToSearch) {
        return this.products.find(function(product) {
            return _.isMatch(product.toJSON(), {
                product_variation_id: productToSearch.product_variations[0].id,
                toppings: productToSearch.product_variations[0].toppings,
                choices: productToSearch.product_variations[0].choices
            });
        });
    }
});

var VendorCartCollection = Backbone.Collection.extend({
    model: VendorCartModel,
    initialize: function(models, options) {
        this.cart = options.cart;
    }
});

var CartModel = Backbone.Model.extend({
    defaults: {
        "subtotal": 0,
        "subtotal_before_discount": 0,
        "subtotal_after_product_discount": 0,
        "subtotal_after_discount": 0,
        "subtotal_after_discount_and_delivery_fee": 0,
        "subtotal_after_discount_and_service_fee": 0,
        "subtotal_after_discount_and_delivery_fee_and_service_fee": 0,
        "total_value": 0,
        "group_joiner_total": 0,
        "container_charge": 0,
        "delivery_fee": 0,
        "vat_total": 0,
        "voucher_total": 0,
        "discount_total": 0,
        "delivery_fee_discount": 0,
        "service_tax_total": 0,
        "service_fee_total": 0,
        "voucher": []
    },

    initialize: function(data, options) {
        this.dataProvider = options.dataProvider;
        this.vendorCart = new VendorCartCollection([], {
            cart: this
        });
        if (_.isObject(data) && _.isArray(data.vendorCart)) {
            this.set(data);
            this.getCartVendor(options.vendor_id).products.set(data.vendorCart[0].products);
        }
    },

    getCartVendor: function(vendor_id) {
        if (_.isUndefined(this.vendorCart.get(vendor_id))) {
            this.vendorCart.add({vendor_id: vendor_id}, {cart: this});
        }

        return this.vendorCart.get(vendor_id);
    }
});
