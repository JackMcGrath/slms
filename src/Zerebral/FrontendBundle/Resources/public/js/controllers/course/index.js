$(document).ready(function(){
    $('.items-list .list-item').hover(function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'visible');
    },function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'hidden');
    });
});