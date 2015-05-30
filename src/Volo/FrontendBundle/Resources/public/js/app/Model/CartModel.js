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
        "discount_text": null,
        "orderTime": null
    },
    idAttribute: 'vendor_id',

    initialize: function() {
        this.products = new CartProductCollection();
        this._xhr = null;
        this._lastUpdate = Date.now();
    },

    _updateCart: function() {
        console.log('CartModel._updateCart ', this.cid);
        this.trigger('cart:dirty');
        this._lastUpdate = Date.now();

        if (this._xhr) {
            this._xhr.abort();
        }

        setTimeout(function() {
            if (Date.now() - this._lastUpdate > 490) {
                var date = _.isDate(this.get('orderTime')) ? this.get('orderTime') : new Date();

                this._xhr = this.collection.cart.dataProvider.calculateCart({
                    products: this.products.toJSON(),
                    vendor_id: this.id,
                    orderTime: date.toISOString(),
                    location: {
                        "location_type": "polygon",
                        "latitude": 52.5237282,
                        "longitude": 13.3908286
                    }
                }).done(function(calculatedData) {
                    this.collection.cart.parse(calculatedData);
                    this.trigger('cart:ready');
                    this._xhr = null;
                }.bind(this));
            }
        }.bind(this), 500);
    },

    addItem: function(newProduct, quantity) {
        newProduct.product_variations[0].toppings = this.getSelectedToppingsFromProduct(newProduct);
        console.log('CartModel.addItem ', this.cid);
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
        this._updateCart();
    },

    getSelectedToppingsFromProduct: function(product) {
        return _.chain(product.product_variations[0].toppings)
            .map(function (item) {
                return item.options || [];
            })
            .reduce(function (memo, item) {
                return memo.concat(item);
            }, [])
            .where({selected: true})
            .map(function(option) {
                return {
                    id: option.id,
                    type: 'full',
                    name: option.name
                };
            })
            .value();
    },

    findSimilarProduct: function(productToSearch) {
        var compareArrays = function(arr1, arr2) {
            return _.isMatch(arr1, arr2) && _.isMatch(arr2, arr1);
        };

        return this.products.find(function(product) {
            product = product.toJSON();

            var productVariation = productToSearch.product_variations[0],
                sameVariation = product.product_variation_id === productVariation.id,
                sameToppings = compareArrays(product.toppings, productVariation.toppings),
                sameChoices = compareArrays(product.choices, productVariation.choices);

            return sameVariation && sameChoices && sameToppings;
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
        this.vendorCart = this.vendorCart || new VendorCartCollection([], {
            cart: this
        });
    },

    parse: function (cart) {
        if (_.isUndefined(this.vendorCart)) {
            this.vendorCart = new VendorCartCollection([], {
                cart: this
            });
        }
        if (_.isObject(cart) && _.isArray(cart.vendorCart)) {
            cart = _.cloneDeep(cart);
            var vendorCart = cart.vendorCart;
            delete cart.vendorCart;
            this.set(cart);

            _.each(vendorCart, function (vendorCart) {
                vendorCart.products = vendorCart.products.map(function(product) {
                    product.toppings = product.toppings.map(function(topping) {
                        return {
                            id: topping.id,
                            type: 'full',
                            name: topping.name
                        };
                    });

                    return product;
                });

                this.getCart(vendorCart.vendor_id).products.set(vendorCart.products);
                delete vendorCart.products;
                this.getCart(vendorCart.vendor_id).set(vendorCart);
            }, this);
        }
    },

    getCart: function(vendorId) {
        if (_.isUndefined(this.vendorCart.get(vendorId))) {
            this.vendorCart.add({vendor_id: vendorId}, {cart: this});
        }

        return this.vendorCart.get(vendorId);
    }
});
