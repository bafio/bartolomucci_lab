$(document).ready(function() {
    $("#icon-nav").click(function(){
        if($(this).hasClass('clicked')) {
            $(this).removeClass('clicked');
            $('#main_nav').slideUp('fast');
        } else {
            $(this).addClass('clicked');
            $('#main_nav').slideDown('fast');
        }
    });

});