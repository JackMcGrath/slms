$(document).ready(function() {
    if (/class/.test(location.hash)) {
        $.scrollTo(location.hash, 100, {offset:{top:-30}});
    }
});