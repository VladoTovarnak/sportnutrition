$('a.mobileMenuIcon').on('click', function() {
    $('ul#menu').toggleClass('hideMobileOnly');
    $('ul.menu').toggleClass('hideMobileOnly')
});

/* reorder elements for mobile view */
$(window).on('load resize', function() {
    if ($(window).width()<768) {
        $('#sidebar').insertAfter('#main .right')
    }
    else {
        $('#sidebar').insertBefore('#main')
    }
});
