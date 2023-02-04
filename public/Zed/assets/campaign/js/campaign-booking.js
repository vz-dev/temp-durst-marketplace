$(document).ready(function () {
    groups = {};

    $('.gallery').each(
        function() {
            let id = parseInt($(this).attr('data-group'), 10);
            if (!groups[id]) {
                groups[id] = [];
            }
            groups[id].push(this);
        }
    );

    $.each(groups, function() {
        $(this).magnificPopup(
            {
                type: 'image',
                closeBtnInside: false,
                closeOnContentClick: true,
                gallery: {
                    enabled: true
                },
                image: {
                    verticalFit: true,
                    titleSrc: function(item) {
                        return '<a class="image-source-link" href="' + item.src + '" target="_blank">' +
                            '<i class="fa fa-download"></i> ' +
                            'Download ' + item.el.attr('title') + '</a>';
                    }
                }
            }
        );
    });
});
