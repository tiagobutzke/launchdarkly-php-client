'use strict';

describe("topping model to json output", function() {
    it('default attributes values', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            options: [{
                id: 1,
                selected: false,
                name: 'option 1'
            }]
        });

        expect(toppingModel.toJSON()).toEqual({
            id: 10,
            name: 'topping 1',
            optionsVisible: false,
            quantity_minimum: null,
            quantity_maximum: null,
            options: [{
                id: 1,
                selected: false,
                name: 'option 1',
                buttonType: 'checkBox'
            }],
            selectedOptions: []
        });
    });

    it('custom attributes', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            quantity_minimum: 1,
            quantity_maximum: 2,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1'
            }]
        });

        toppingModel.setOptionsVisibility(true);

        expect(toppingModel.toJSON()).toEqual({
            id: 10,
            name: 'topping 1',
            optionsVisible: true,
            quantity_minimum: 1,
            quantity_maximum: 2,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1',
                buttonType: 'checkBox'
            }],
            selectedOptions: [{
                id: 1,
                selected: true,
                name: 'option 1',
                buttonType: 'checkBox'
            }]
        });
    });

});

describe("topping model setOptionsVisibility", function() {
    it('to true', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            options: [{
                id: 1,
                selected: false,
                name: 'option 1'
            }]
        });

        expect(toppingModel.toJSON()).toEqual({
            id: 10,
            name: 'topping 1',
            optionsVisible: false,
            quantity_minimum: null,
            quantity_maximum: null,
            options: [{
                id: 1,
                selected: false,
                name: 'option 1',
                buttonType: 'checkBox'
            }],
            selectedOptions: []
        });

        toppingModel.setOptionsVisibility(true);

        expect(toppingModel.toJSON()).toEqual({
            id: 10,
            name: 'topping 1',
            optionsVisible: true,
            quantity_minimum: null,
            quantity_maximum: null,
            options: [{
                id: 1,
                selected: false,
                name: 'option 1',
                buttonType: 'checkBox'
            }],
            selectedOptions: []
        });
    });
});

describe("topping model isCheckBoxList", function() {
    it('true with quantity maximum > 1', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            quantity_minimum: 0,
            quantity_maximum: 2,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1'
            }]
        });

        expect(toppingModel.isCheckBoxList()).toEqual(true);
    });

    it('true with quantity maximum 1', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            quantity_minimum: 0,
            quantity_maximum: 1,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1'
            }]
        });

        expect(toppingModel.isCheckBoxList()).toEqual(true);
    });
});

describe("topping model isSelectionRequired", function() {
    it('true with quantity minimum > 0', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            quantity_minimum: 1,
            quantity_maximum: 2,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1'
            }]
        });

        expect(toppingModel.isSelectionRequired()).toEqual(true);
    });

    it('true with quantity minimum = 0', function() {
        var toppingModel = new ToppingModel({
            "id": 10,
            "name": "topping 1",
            quantity_minimum: 0,
            quantity_maximum: 2,
            options: [{
                id: 1,
                selected: true,
                name: 'option 1'
            }]
        });

        expect(toppingModel.isSelectionRequired()).toEqual(false);
    });
});
