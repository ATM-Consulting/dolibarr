/* JS CODE TO ENABLE reposition management (does not work if a redirect is done after action of submission) */
jQuery(document).ready(function () {
    const url = window.location.href.split(/[?#]/)[0];
    /* If page_y set, we set scollbar with it */
    page_y = getParameterByName('page_y', 0); /* search in GET parameter */
    if (page_y == 0) page_y = jQuery("#page_y").text(); /* search in POST parameter that is filed at bottom of page */
    if (page_y > 0) {
        console.log("scroll-reposition : page_y found is " + page_y);
        $('#id-right').scrollTop(page_y);
    } else if (
        sessionStorage.getItem('prevScrollPosY')
        && sessionStorage.getItem('prevScrollUrl') === url
        && !getParameterByName('action', 0)
    ) {
        $('#id-right').scrollTop(sessionStorage.getItem('prevScrollPosY'));
        sessionStorage.removeItem('prevScrollPosY');
        sessionStorage.removeItem('prevScrollUrl');
    }

    /* Set handler to add page_y param on output (click on href links or submit button) */
    jQuery(".reposition").click(function () {
        let page_y = $('#id-right').scrollTop();

        if (page_y > 0) {
            if (this.href) {
                console.log("We click on tag with .reposition class. this.ref was " + this.href);
                let hrefarray = this.href.split("#", 2);
                hrefarray[0] = hrefarray[0].replace(/&page_y=(\d+)/, '');
                this.href = hrefarray[0] + '&page_y=' + page_y;
                console.log("scroll-reposition : We click on tag with .reposition class. this.ref is now " + this.href);
            } else {
                console.log("scroll-reposition : We click on tag with .reposition class but element is not an <a> html tag, so we try to update input form field with name=page_y with value " + page_y);
                jQuery("input[type=hidden][name=page_y]").val(page_y);
            }
            sessionStorage.setItem('prevScrollPosY', page_y);
            sessionStorage.setItem('prevScrollUrl', url);
        }
    });

    // #176
    if ( $.cookie('SR-advance-prod-object-id') === getIdfromURL(window.location.href) ) {
        page_y = $.cookie('SR-advance-prod-pagey');
        console.log("SR-Cookie : page_y found is " + page_y);
        $('#id-right').scrollTop(page_y);
    }
});

// #176
$(document).on("click", ".advance-prod-search-list-action-btn.--addProductToLine" , function(event) {
    if ($.cookie('SR-advance-prod-pagey')) {
        page_y =  parseInt($.cookie('SR-advance-prod-pagey')) + 50;
    } else {
        page_y = $('#id-right').scrollTop();
    }

    if (page_y > 0) {
        $.cookie('SR-advance-prod-object-id' , getIdfromURL(window.location.href));
        $.cookie('SR-advance-prod-pagey' , page_y);
    }
});

// #176
/**
 *
 * @param {string} url
 * @returns {string} returns the "id" parameter contained in the url
 */
function getIdfromURL(url) {
    url = new URL(url);
    let params = new URLSearchParams(url.search);
    let id = params.get('id');

    return id;
}