$(document).ready(function() {

    /* open fancybox when click on fancybox-class images  */
    $("img.fancybox").click(function() {
        $.fancybox.open($(this).attr('src'));
    });

    /* start nivoSlider gallery */
    var slider = $('#slider');
    var pauseTime = 3000;
    var slider_pausetime = slider.data('pausetime');
    if(slider_pausetime) {
        pauseTime = slider_pausetime;
    }
    if( slider.length > 0 ) {
        slider.nivoSlider({
            pauseTime: pauseTime,
            effect: 'fade',
            directionNav: false,
            controlNav: false,
            randomStart: true
        });
    }

    /* alumni anchor in staff page */
    $('#alumni-anchor').click(function(){$('#alumni-anchor_target').click();});

});