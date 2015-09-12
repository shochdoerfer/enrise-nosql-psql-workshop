(function ($) {
    $(function () {
        $( document ).on('click', 'p.rating span', function(e) {
            var parent = $(this).parent().parent();
            e.stopPropagation();

            $.post(
                "/rate",
                {productId: parent.attr('data-productId'), rating: $(this).attr('data-rating')},
                function (result) {
                    parent.html(result);
                }
            );
        });
    });
})(jQuery);
