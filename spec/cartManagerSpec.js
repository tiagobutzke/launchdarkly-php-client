'use strict';

describe("A cart manager", function() {
    var cart;
    var product;
    var cartManager;

    beforeEach(function() {
        product = {
            vendor_id: 1,
            variation_id: 2,
            groupOrderUserName: '',
            toppings: [
                {
                    id: 1
                },
                {
                    id: 2
                }
            ],
            choices: [
                {
                    id: 3
                },
                {
                    id: 4
                }
            ],
            product_variations: [
                {
                    id: 1
                }
            ]
        };

        cart = new Cart();
        cartManager = new CartManager();
    });

    it("adds a new product to the cart", function() {
        var selectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };
        var expected = _.cloneDeep(selectedProduct);

        cartManager.addProduct(cart, selectedProduct);

        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expected);
    });

    it("adds the same product with different quantity", function() {
        var firstSelectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [
                product.toppings[0]
            ],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };
        var expectedFirst = _.cloneDeep(firstSelectedProduct);

        var secondSelectedProduct = _.cloneDeep(firstSelectedProduct);
        secondSelectedProduct.quantity = 5;
        var expectedSecond = _.cloneDeep(secondSelectedProduct);
        expectedSecond.quantity = firstSelectedProduct.quantity + secondSelectedProduct.quantity

        cartManager.addProduct(cart, firstSelectedProduct);
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedFirst);

        cartManager.addProduct(cart, secondSelectedProduct);
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedSecond);
    });

    it("modifies the quantity for an existing product", function() {
        var firstSelectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [
                product.toppings[0]
            ],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };
        var expectedFirst = _.cloneDeep(firstSelectedProduct);

        var expectedSecond = _.cloneDeep(firstSelectedProduct);
        expectedSecond.quantity = 5;

        cartManager.addProduct(cart, firstSelectedProduct);
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedFirst);

        var addedProduct = cartManager.getProduct(cart, firstSelectedProduct);
        addedProduct.quantity = 5;
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedSecond);
    });

    it("adds the same product", function() {
        var firstSelectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [
                product.toppings[0]
            ],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };
        var expectedFirst = _.cloneDeep(firstSelectedProduct);

        var secondSelectedProduct = _.cloneDeep(firstSelectedProduct);

        cartManager.addProduct(cart, firstSelectedProduct);
        expect(cart.products).toContain(expectedFirst);

        cartManager.addProduct(cart, secondSelectedProduct);
        expectedFirst.quantity = firstSelectedProduct.quantity + secondSelectedProduct.quantity;
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedFirst);
    });

    it("adds 2 different products with the same quantity to the cart", function() {
        var firstSelectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [
                product.toppings[0]
            ],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };

        var secondSelectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [
                product.toppings[1]
            ],
            choices: [],
            product_variations: [
                {
                    id: 2
                }
            ]
        };

        var expectedFirst = _.cloneDeep(firstSelectedProduct);
        var expectedSecond = _.cloneDeep(secondSelectedProduct);

        cartManager.addProduct(cart, firstSelectedProduct);
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expectedFirst);

        cartManager.addProduct(cart, secondSelectedProduct);
        expect(cart.products.length).toBe(2);
        expect(cart.products).toContain(expectedFirst);
        expect(cart.products).toContain(expectedSecond);
    });

    it("removes a product from the cart", function() {
        var selectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [],
            choices: [],
            product_variations: [
                {
                    id: 2
                }
            ]
        };

        var expected = _.cloneDeep(selectedProduct);

        cartManager.addProduct(cart, selectedProduct);
        expect(cart.products.length).toBe(1);
        expect(cart.products).toContain(expected);

        selectedProduct.quantity = 0;
        expect(cart.products).toContain(expected);

        cartManager.addProduct(cart, selectedProduct);
        expect(cart.products.length).toBe(0);
    });

    it("gets a product from the cart", function() {
        var foundProduct;
        var selectedProduct = {
            variation_id: product.variation_id,
            vendor_id: product.vendor_id,
            quantity: 3,
            groupOrderUserName: '',
            toppings: [],
            choices: [],
            product_variations: [
                {
                    id: 1
                }
            ]
        };
        var expected = _.cloneDeep(selectedProduct);

        cart.products.push(selectedProduct);

        foundProduct = cartManager.getProduct(cart, selectedProduct);
        expect(cart.products.length).toBe(1);
        expect(foundProduct).toEqual(expected);
    });
});
