$('a.mobileMenuIcon').on('click', function() {
    $('ul#menu').toggleClass('hideMobileOnly');
    $('ul.menu').toggleClass('hideMobileOnly')
});

/* reorder elements for mobile view */
$(window).on('load resize', function() {
    if ($(window).width()<641) {
        $('#sidebar').insertAfter('#main .left:eq(0)');
        $('#sidebar').show();
    }
    else {
        $('#sidebar').insertBefore('#main')
    }
});
