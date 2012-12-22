$(document).ready(function() {
    $('.items-list .list-item').hover(function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'visible').prev().hide();
    },function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'hidden').prev().show();
    });

    $('.calendar-widget a[rel="tooltip"]').tooltip({html: true});
});