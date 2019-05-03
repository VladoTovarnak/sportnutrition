/* reorder elements for mobile view */
$(window).on('load resize', function() {
    if ($(window).width() < 641) {
        $('#sidebar').insertAfter('#main .left:eq(0)');
        $('#sidebar').show();
    } else {
        $('#sidebar').insertBefore('#main')
    }
});


var hamburger = $('a#hamburger-icon');
var hamburgerBox = $('div.mobileMenuIcon');
hamburgerBox.click(function() {
    hamburger.toggleClass('active');
    $('ul#menu').toggleClass('hideMobileOnly');
    $('ul.menu').toggleClass('hideMobileOnly')
    $('ul.submenu').toggleClass('hideMobileOnly')
    $('.mobileOverlay').toggleClass('grayOverlay')
    $("html, body").animate({ scrollTop: 0 }, "fast");
    return false;
});