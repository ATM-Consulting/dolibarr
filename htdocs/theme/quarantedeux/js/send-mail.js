$(document).ready(function() {
    if(window.location.href.includes('action=presend&mode=init#formmail')) {
        $('.tabsAction').remove();
        $('.center').css('class','tabsAction');
    }
});