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
                name: 'option 1'
            }],
            selectedOptions: [{
                id: 1,
                selected: true,
                name: 'option 1'
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
                name: 'option 1'
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
                name: 'option 1'
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

describe("topping model toggleToppingOptionSelection", function() {
    var canSelect, canUnselect, toppingModel, option, secondOption, isCheckBoxList;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            id: 10,
            name: "topping 1",
            options: [
                {
                    id: 1,
                    selected: false,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: false,
                    name: 'option 2'
                }
            ]
        });

        option = toppingModel.options.at(0);
        secondOption = toppingModel.options.at(1);

        spyOn(toppingModel, '_isToppingSelectable').and.callFake(function() {
            return canSelect;
        });

        spyOn(toppingModel, '_isToppingUnselectable').and.callFake(function() {
            return canUnselect;
        });

        spyOn(toppingModel, 'isCheckBoxList').and.callFake(function() {
            return isCheckBoxList;
        });
    });

    it('should unselect option, if it can and send event', function(done) {
        canUnselect = true;
        option.set('selected', true);

        toppingModel.on('topping:validateOptions', function() {
            expect(option.get('selected')).toEqual(false);
            done();
        });

        toppingModel.toggleToppingOptionSelection(option);
    });

    it('should not unselect option, if it can not', function(done) {
        option.set('selected', true);
        canUnselect = false;

        toppingModel.on('topping:toggleDenied', function() {
            expect(option.get('selected')).toEqual(true);
            done();
        });

        toppingModel.toggleToppingOptionSelection(option);
    });

    it('should select checkbox and leave the rest untouched', function(done) {
        secondOption.set('selected', true);
        canSelect = true;
        isCheckBoxList = true;

        toppingModel.on('topping:validateOptions', function() {
            expect(option.get('selected')).toEqual(true);
            expect(secondOption.get('selected')).toEqual(true);

            done();
        });

        toppingModel.toggleToppingOptionSelection(option);
    });

    it('should select radio and uncheck the rest', function(done) {
        secondOption.set('selected', true);
        canSelect = true;
        isCheckBoxList = false;

        toppingModel.on('topping:validateOptions', function() {
            expect(option.get('selected')).toEqual(true);
            expect(secondOption.get('selected')).toEqual(false);

            done();
        });

        toppingModel.toggleToppingOptionSelection(option);
    });


    it('should not select option, if it can not', function(done) {
        option.set('selected', false);
        canSelect = false;

        toppingModel.on('topping:toggleDenied', function() {
            expect(option.get('selected')).toEqual(false);
            done();
        });

        toppingModel.toggleToppingOptionSelection(option);
    });
});

describe('topping model isValid', function() {
    var toppingModel;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            quantity_maximum: null,
            quantity_minimum: null,
            id: 10,
            name: 'topping 1',
            options: [
                {
                    id: 1,
                    selected: true,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: false,
                    name: 'option 2'
                }
            ]
        });
    });

    it('should be valid, if all requirements are met', function() {
        toppingModel.set('quantity_maximum', 2);
        toppingModel.set('quantity_minimum', 1);

        expect(toppingModel.isValid()).toEqual(true);
    });

    it('should not be valid, if minimum is higher than selected option count', function() {
        toppingModel.set('quantity_maximum', 2);
        toppingModel.set('quantity_minimum', 2);

        expect(toppingModel.isValid()).toEqual(false);
    });

    it('should not be valid, if maximum is lower than selected option count', function() {
        toppingModel.set('quantity_maximum', 0);
        toppingModel.set('quantity_minimum', 2);

        expect(toppingModel.isValid()).toEqual(false);
    });

    it('should be valid, if minimum is higher than options count', function() {
        toppingModel.set('quantity_maximum', 5);
        toppingModel.set('quantity_minimum', 5);

        _.invoke(toppingModel.options.models, 'set', {'selected': true});

        expect(toppingModel.isValid()).toEqual(true);
    });
});

describe('topping model _isToppingSelectable', function() {
    var toppingModel, isCheckBoxList;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            quantity_maximum: null,
            quantity_minimum: null,
            id: 10,
            name: 'topping 1',
            options: [
                {
                    id: 1,
                    selected: true,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: false,
                    name: 'option 2'
                }
            ]
        });

        spyOn(toppingModel, 'isCheckBoxList').and.callFake(function() {
            return isCheckBoxList;
        });
    });

    it('should be always true for radio', function() {
        isCheckBoxList = false;

        toppingModel.set('quantity_maximum', 0);
        expect(toppingModel._isToppingSelectable()).toEqual(true);

        toppingModel.set('quantity_maximum', 5);
        expect(toppingModel._isToppingSelectable()).toEqual(true);
    });

    it('should be false for check box, if quantity max is reached', function() {
        isCheckBoxList = true;

        toppingModel.set('quantity_maximum', 1);
        expect(toppingModel._isToppingSelectable()).toEqual(false);

        toppingModel.set('quantity_maximum', 0);
        expect(toppingModel._isToppingSelectable()).toEqual(false);
    });

    it('should be true for check box, if quantity max is higher', function() {
        isCheckBoxList = true;

        toppingModel.set('quantity_maximum', 2);
        expect(toppingModel._isToppingSelectable()).toEqual(true);
    });
});

describe('topping model _isToppingUnselectable', function() {
    var toppingModel;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            quantity_maximum: null,
            quantity_minimum: null,
            id: 10,
            name: 'topping 1',
            options: [
                {
                    id: 1,
                    selected: true,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: true,
                    name: 'option 2'
                },
                {
                    id: 3,
                    selected: false,
                    name: 'option 3'
                }
            ]
        });
    });

    it('should be true, if the selection is not required', function() {
        toppingModel.set('quantity_minimum', 0);

        expect(toppingModel._isToppingUnselectable()).toEqual(true);
    });


    it('should return true, if minimum quantity is lower then selected', function() {
        toppingModel.set('quantity_minimum', 1);
        toppingModel.set('quantity_maximum', 2);

        expect(toppingModel._isToppingUnselectable()).toEqual(true);
    });

    it('should return false, if minimum number of options are checked', function() {
        toppingModel.set('quantity_minimum', 2);

        expect(toppingModel._isToppingUnselectable()).toEqual(false);
    });

    it('should return false, if minimum quantity equals options count', function() {
        toppingModel.set('quantity_minimum', 3);
        _.invoke(toppingModel.options.models, 'set', {'selected': false});

        expect(toppingModel._isToppingUnselectable()).toEqual(false);
    });

    it('should return false, if max equals min and that equals 1', function() {
        toppingModel.set('quantity_minimum', 1);
        toppingModel.set('quantity_maximum', 1);

        expect(toppingModel._isToppingUnselectable()).toEqual(false);
    });

    it('should return true, if max equals min', function() {
        toppingModel.set('quantity_minimum', 2);
        toppingModel.set('quantity_maximum', 2);

        expect(toppingModel._isToppingUnselectable()).toEqual(true);
    });

    it('should return false, if max equals min and options count', function() {
        toppingModel.set('quantity_minimum', 3);
        toppingModel.set('quantity_maximum', 3);

        expect(toppingModel._isToppingUnselectable()).toEqual(false);
    });
});

describe('topping model areOptionsVisible', function() {
    var toppingModel;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            id: 1,
            name: 'topping 1',
            options: [],
            optionsVisible: false
        });
    });

    it('should return visibility state correctly', function() {
        toppingModel.set('optionsVisible', false);
        expect(toppingModel.areOptionsVisible()).toEqual(false);

        toppingModel.set('optionsVisible', true);
        expect(toppingModel.areOptionsVisible()).toEqual(true);
    });
});

describe('topping option model', function() {
    var optionModel;
    beforeEach(function() {
        optionModel = new ToppingOptionModel({
            selected: false
        })
    });

    it('should set selection correctly', function() {
        optionModel.setSelection(true);
        expect(optionModel.isSelected()).toEqual(true);

        optionModel.setSelection(false);
        expect(optionModel.isSelected()).toEqual(false);
    });
});

describe('topping model initial _setInitialSelection', function() {
    var toppingModel;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            quantity_maximum: null,
            quantity_minimum: null,
            id: 10,
            name: 'topping 1',
            options: [
                {
                    id: 1,
                    selected: false,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: false,
                    name: 'option 2'
                },
                {
                    id: 3,
                    selected: false,
                    name: 'option 3'
                }
            ]
        });
    });

    it('should select all options, if minimum is heigher than options length and selected items', function() {
        toppingModel.set('quantity_minimum', 4);
        _.each(toppingModel.options.models, toppingModel._setInitialSelection, this);
        expect(toppingModel._getSelectedItems().length).toEqual(3);
    });

    it('should select nothing, if minimum is below options count', function() {
        toppingModel.set('quantity_minimum', 1);
        _.each(toppingModel.options.models, toppingModel._setInitialSelection, this);
        expect(toppingModel._getSelectedItems().length).toEqual(0);
    });
});

describe('topping model _getSelectedItems', function() {
    var toppingModel;

    beforeEach(function() {
        toppingModel = new ToppingModel({
            quantity_maximum: null,
            quantity_minimum: null,
            id: 10,
            name: 'topping 1',
            options: [
                {
                    id: 1,
                    selected: false,
                    name: 'option 1'
                },
                {
                    id: 2,
                    selected: false,
                    name: 'option 2'
                },
                {
                    id: 3,
                    selected: false,
                    name: 'option 3'
                }
            ]
        });
    });

    it('should return selected items', function() {
        toppingModel.options.at('1').setSelection(true);
        expect(toppingModel._getSelectedItems().length).toEqual(1);
        expect(toppingModel._getSelectedItems()[0].get('id')).toEqual(2);
    });

    it('should return empty array, if nothing is selected', function() {
        expect(toppingModel._getSelectedItems().length).toEqual(0);
    });
});
