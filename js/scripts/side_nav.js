$(document).ready(function() {
    var side_nav = $("#side_nav")
    side_nav.find("li a").click(function(event){
        event.preventDefault();
        var row_to_show = $(this).attr('href');
        side_nav.find("li.selected").removeClass('selected');
        $(this).parent('li').addClass('selected');
        $('.row_container.shown').removeClass('shown').addClass('hidden');
        $(row_to_show).parent().removeClass('hidden').addClass('shown');
    });
});