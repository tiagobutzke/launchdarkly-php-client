var CartItemModel = Backbone.Model.extend({
    defaults: {
        product_variation_id: null,
        name: "",
        variation_name: "",
        total_price_before_discount: 0,
        total_price: 0,
        quantity: 0,
        toppings: [],
        choices: [],
        group_order_user_name: null,
        group_order_user_code: null,
        special_instructions: '',
        description: ""
    },

    initialize: function() {
        this.toppings = new ToppingCollection(_.cloneDeep(this.get('toppings')));
    },

    isValid: function() {
        var notValidTopping = this.toppings.find(function(topping) {
            return !topping.isValid();
        });

        return !notValidTopping;
    },

    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.call(this);

        json.toppings = this.toppings.toJSON();

        return json;
    },

    transformToppingsToServerFormat: function() {
        var selectedToppings = this.getSelectedToppings();

        this.toppings = new ToppingCollection(selectedToppings);
    },

    transformToppingsToMenuFormat: function(menuToppings) {
        var clone = _.cloneDeep(menuToppings),
            newToppings = _.each(clone, function (topping) {
                _.each(topping.options, function (option) {
                    if (_.findWhere(this.toppings.toJSON(), {id: option.id})) {
                        option.selected = true;
                    }
                }.bind(this));

                return topping;
            }.bind(this));

        this.toppings = new ToppingCollection(newToppings);
    },

    getSelectedToppings: function() {
        return _.chain(this.toppings.toJSON())
            .map(function(item) {
                return item.options || [];
            })
            .reduce(function(memo, item) {
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

    isSimilar: function(objectToCompare) {
        var compareArrays = function(arr1, arr2) {
                return _.isMatch(arr1, arr2) && _.isMatch(arr2, arr1);
            },
            sameVariation = this.get('product_variation_id') === objectToCompare.get('product_variation_id'),
            sameToppings = compareArrays(this.toppings.toJSON(), objectToCompare.toppings.toJSON()),
            sameInstructions = this.get('special_instructions') === objectToCompare.get('special_instructions');

        return sameVariation && sameToppings && sameInstructions;
    },

    clone: function() {
        var clone = Backbone.Model.prototype.clone.call(this);
        clone.toppings = new ToppingCollection(this.toppings.toJSON());

        return clone;
    }
});

CartItemModel.createFromMenuItem = function(menuItem) {
    var productVariation = menuItem.product_variations[0];

    return new CartItemModel({
        product_variation_id: productVariation.id,
        name: menuItem.name,
        variation_name: productVariation.name,
        total_price_before_discount: productVariation.price_before_discount,
        total_price: productVariation.price,
        quantity: 1,
        toppings: productVariation.toppings,
        choices: productVariation.choices,
        group_order_user_name: null,
        group_order_user_code: null,
        description: menuItem.description
    });
};

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
        "order_time": null,
        "voucher": null,
        location: {
            "location_type": "polygon",
            "latitude": null,
            "longitude": null
        }
    },
    idAttribute: 'vendor_id',

    initialize: function() {
        _.bindAll(this);
        this.products = new CartProductCollection();
        this._xhr = null;
        this.timeoutReference = null;
    },

    getProductsCount: function() {
        var count = 0;
        this.products.each(function(product) {
            count += product.get('quantity');
        });

        return parseInt(count, 10);
    },

    getProductsIds: function() {
        var ids = [];

        this.products.each(function(product) {
            ids.push(product.get('product_variation_id'));
        });

        return ids;
    },

    validate: function(attrs) {
        if (_.isNull(attrs.location.latitude) || _.isNull(attrs.location.longitude) || _.isNull(attrs.location.location_type)) {
            return 'location_not_set';
        }
    },

    updateLocationIfDeliverable: function (data) {
        console.log('updateLocationIfDeliverable ', this.cid);
        var xhr = $.get(
            Routing.generate('vendor_delivery_validation_by_gps', {
                vendorId: this.get('vendor_id'),
                latitude: data.latitude,
                longitude: data.longitude
            })
        );

        xhr.done(function (response) {
            if (response.result) {
                this.set('location', {
                    location_type: "polygon",
                    latitude: data.latitude,
                    longitude: data.longitude
                });
            } else {
                this.set('location', {
                    location_type: "polygon",
                    latitude: null,
                    longitude: null
                });
            }
        }.bind(this));

        return xhr;
    },

    updateCart: function() {
        console.log('CartModel.updateCart ', this.cid);
        this.trigger('cart:dirty');

        if (this._xhr) {
            this._xhr.abort();
        }

        if (this.timeoutReference) {
            clearTimeout(this.timeoutReference);
        }
        this.timeoutReference = setTimeout(this._sendRequest, 500);
    },

    _sendRequest: function() {
        var orderTime = this.get('order_time'),
            data = {
                products: this.products.toJSON(),
                vendor_id: this.id,
                location: this.get('location')
            };

        data.order_time = orderTime ? orderTime : moment().tz(VOLO.configuration.timeZone).format();
        data.voucher = _.isString(this.get('voucher')) ? [this.get('voucher')] : [];

        this._xhr = this.collection.cart.dataProvider.calculateCart(data).done(function(calculatedData) {
            this.collection.cart.parse(calculatedData);
            this.trigger('cart:ready');
            console.log('cart:ready fired');
            this._xhr = null;
            this.timeoutReference = null;
        }.bind(this)).error(function(jqXHR) {
            if (jqXHR.statusText !== 'abort') {
                this.trigger('cart:error', jqXHR.responseJSON);
                console.log('cart:error fired');
                this.trigger('cart:ready');
            }
        }.bind(this));
    },

    addItem: function(newProduct) {
        var clone = newProduct.clone(),
            similarProduct;

        clone.transformToppingsToServerFormat();
        similarProduct = this.findSimilarProduct(clone);

        if (_.isObject(similarProduct)) {
            similarProduct.set('quantity', parseInt(similarProduct.get('quantity') + newProduct.get('quantity'), 10));
        } else {
            this.products.unshift(clone);
        }

        this.updateCart();
    },

    updateItem: function(itemToUpdate, updatedItem) {
        var clone = updatedItem.clone();
        clone.transformToppingsToServerFormat();

        if (updatedItem.get('quantity') === 0) {
            this.removeItem(itemToUpdate);
        } else {
            var productFromCart = this.findSimilarProduct(clone);

            if (productFromCart && productFromCart.cid !== itemToUpdate.cid) {
                this.removeItem(itemToUpdate);
                productFromCart.set('quantity', productFromCart.get('quantity') + updatedItem.get('quantity'));
            } else {
                itemToUpdate.toppings.set(updatedItem.getSelectedToppings());
                itemToUpdate.set('quantity', updatedItem.get('quantity'));
                itemToUpdate.set('special_instructions', updatedItem.get('special_instructions'));
            }
        }

        this.updateCart();
    },

    removeItem: function(productToRemove) {
        this.products.remove(productToRemove);
        this.updateCart();
    },

    increaseQuantity: function(product, quantityDifference) {
        var oldQuantity = product.get('quantity'),
            newQuantity = parseInt(oldQuantity + quantityDifference, 10);

        if (newQuantity <= 0) {
            this.products.remove(product);
        } else {
            product.set('quantity', newQuantity);
        }

        this.updateCart();
    },

    findSimilarProduct: function(productToSearch) {
        return this.products.find(function(product) {
            return product.isSimilar(productToSearch);
        });
    },

    isSubtotalLessMinOrderAmount: function() {
        return this.get('subtotal') < this.get('minimum_order_amount');
    },

    isSubtotalGreaterZero: function() {
        return this.get('subtotal') > 0;
    },

    isSubtotalIsZero: function() {
        return this.get('subtotal') === 0;
    },

    isSubtotalGreaterEqualMinOrderAmount: function() {
        return this.get('subtotal') >= this.get('minimum_order_amount');
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
        this.vendorCarts = this.vendorCarts || new VendorCartCollection([], {
            cart: this
        });
    },

    parse: function (cart) {
        if (_.isUndefined(this.vendorCarts)) {
            this.vendorCarts = new VendorCartCollection([], {
                cart: this
            });
        }
        if (_.isObject(cart) && _.isArray(cart.vendorCart)) {
            cart = _.cloneDeep(cart);
            var vendorCarts = cart.vendorCart;
            delete cart.vendorCart;
            this.set(cart);

            _.each(vendorCarts, function (vendorCart) {
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
                this.getCart(vendorCart.vendor_id).set(
                    'voucher',
                    cart.voucher.length > 0 ? cart.voucher[0].code : null
                );
                this.getCart(vendorCart.vendor_id).set('order_time', cart.order_time);
            }, this);

            //clear all carts
            if (vendorCarts.length === 0) {
                this.vendorCarts.each(function(vendorCart) {
                    vendorCart.set(cart);
                    vendorCart.products.reset();
                });
            }
        }
    },

    getCart: function(vendorId) {
        if (_.isUndefined(this.vendorCarts.get(vendorId))) {
            this.vendorCarts.add({vendor_id: vendorId}, {cart: this});
        }

        return this.vendorCarts.get(vendorId);
    },

    emptyCart: function(vendorId) {
        this.vendorCarts.remove(vendorId);
    }
});
