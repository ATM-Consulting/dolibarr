$(document).ready(function () {
    $('a.pictopreview.documentpreview').on("click", function (e) {
        let decimalMatch = window.navigator.userAgent.match(/Version\/17\.([0-9])/); // get decimal on IOS version
        if (decimalMatch && decimalMatch[1]) {
            let decimalVersion = parseInt(decimalMatch[1]);
            if (decimalVersion > 2) {
                e.stopImmediatePropagation(); // block other script
                e.preventDefault(); // block other script
                window.open($(this).attr('href'), '_blank'); // open <a> href on a new tab
            }
        }
    });
});