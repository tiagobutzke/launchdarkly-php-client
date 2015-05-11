$(document).on('ready page:load', function() {
    var stats = $("#stats__dish");

    if(stats.length) {
        new RevealOnScroll(stats, $('.stats'), 0.3, 0.18, 65);

        var statsCommentCache = $(".stats__comment");
        new NumberScroller(
            $(".numbers__scroller"),
            function() { return statsCommentCache.offset().top; },
            21
        );

        $('.city').each(function() {
            var city = $(this),
                cityTitle = city.find('.city__title'),
                cityName = cityTitle.html();

            city.mouseover(function() {
                    cityTitle.html('Browse restaurants');
                })
                .mouseout(function() {
                    cityTitle.html(cityName);
                });

            new Flipper(city);
        });
    }
});
