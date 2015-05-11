
var Flipper  = function(contanier) {
    var flipper = contanier.find('.flipper');
    if(flipper.length) {
        $(contanier).mouseover(function() {
                if(!flipper.hasClass('flipped')) {
                    flipper.addClass('flipped');
                }
            })
            .mouseout(function() {
                if(flipper.hasClass('flipped')) {
                    flipper.removeClass('flipped');
                }
            });
    }
};

