function MenuManagement() {
    if ($('#hidespan').length > 0) $('#hidespan').remove();
    if (/Android|iPhone|webOS/i.test(window.navigator.userAgent)) $('.side-nav, #id-right').addClass('is-mobile');
    else {
        $('.side-nav, #id-right').removeClass('is-mobile');
        if (window.matchMedia("(orientation: landscape)").matches || (window.matchMedia("(orientation: portrait)").matches && window.matchMedia("(min-width: 1024px)").matches)) {
            $('.side-nav').addClass('shrink-menu').append('<span id="hidespan" onclick="updateCookie()" class="fas fa-chevron-left"></span>');
            shrinkMenu();
        } else $('.side-nav, #id-right').addClass('is-mobile');
    }
}

function updateCookie() {
    $.cookie('shrink_menu') === '0' ? $.cookie('shrink_menu', '1') : $.cookie('shrink_menu', '0');
    shrinkMenu();
}

function shrinkMenu() {
    let hidespan = $('#hidespan');
    $('#hidespan').remove();
    hidespan.attr('class', 'fas fa-chevron-' + ($.cookie('shrink_menu') === '0' ? 'right' : 'left'));
    if ($.cookie('shrink_menu') === '0') {
        $('.side-nav, #id-right').addClass('close').removeClass('open');
        $('div.vmenu').prepend(hidespan);
    } else {
        $('.side-nav, #id-right').addClass('open').removeClass('close');
        $('.side-nav').append(hidespan);
    }
}

$(document).ready(function () {
    $('.menu_titre > a.vmenu').each(function() { // pass trought menu item to add span to hide text and keep icon
        if ($(this).find('i[class*="fa-"], span[class*="fa-"]').length > 0) {
            $(this).contents().filter(function(){
                return this.nodeType === 3 && $.trim(this.nodeValue) !== '';
            }).wrap('<span class="hidden-menu-text"></span>');
        }
    });

    MenuManagement();
    $(window).resize(function () { MenuManagement(); });
});
