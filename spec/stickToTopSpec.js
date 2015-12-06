describe('A stickOnTop element', function () {
    'use strict';

    var stickToTopObject, options, parentElement, childElement,
        TOP_VALUE = 30,
        STARTING_POINT = 100,
        END_POINT = 500;

    function setMockScrollPosition(pixel) {
        if (stickToTopObject) {
            stickToTopObject.$window.scrollTop = function() {
                return pixel;
            }
        }
    }

    beforeEach(function () {
        parentElement = $("<section></section>");
        childElement = $("<div></div>");
        parentElement.append(childElement);

        options = {
            $container: parentElement,
            stickOnTopValueGetter: function() {
                return TOP_VALUE;
            },

            startingPointGetter: function() {
                return STARTING_POINT;
            },

            endPointGetter: function() {
                return END_POINT;
            }
        };

        stickToTopObject = new StickOnTop(options);
        stickToTopObject.init(childElement);
        window.document.body.style.minHeight = '9000px';
    });

    afterEach(function() {
        window.document.body.style.minHeight = '';
    });


    it('should be initialized', function() {
        expect(stickToTopObject).toBeDefined();
        expect(stickToTopObject).not.toEqual(null);
    });

    it('should make element sticky if scroll past starting point', function() {
        setMockScrollPosition(STARTING_POINT - TOP_VALUE + 1);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('fixed');
        expect($(childElement).css('top')).toEqual(TOP_VALUE + 'px');
    });

    it('should make element sticky if isActiveGetter is set and returns true', function() {
        options.isActiveGetter = function() {
            return true;
        };

        stickToTopObject = new StickOnTop(options);
        stickToTopObject.init(childElement);
        setMockScrollPosition(150);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('fixed');
        expect($(childElement).css('top')).toEqual(TOP_VALUE + 'px');

        setMockScrollPosition(400);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('fixed');
        expect($(childElement).css('top')).toEqual(TOP_VALUE + 'px');
    });

    it('should not make element sticky if activeGetter returns false', function() {
        options.isActiveGetter = function() {
            return false;
        };

        stickToTopObject = new StickOnTop(options);
        stickToTopObject.init(childElement);
        setMockScrollPosition(150);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('');
        expect($(childElement).css('top')).toEqual('');

        setMockScrollPosition(400);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('');
        expect($(childElement).css('top')).toEqual('');
    });

    it('should adjust position top if scroll past endpoint', function() {
        var pastEndPoint = 568;
        setMockScrollPosition(pastEndPoint);
        stickToTopObject.redraw();

        expect($(childElement).css('position')).toEqual('fixed');
        expect($(childElement).css('top')).toEqual(END_POINT - pastEndPoint + 'px');
    });
});
