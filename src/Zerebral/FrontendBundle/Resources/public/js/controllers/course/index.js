$(document).ready(function(){
    $('.user-courses .course').hover(function(e) {
        $(e.target).closest('.course').find('.manage-buttons').css('visibility', 'visible');
        //$(e.target).closest('.course').find('.manage-buttons').show();
    },function(e) {
        $(e.target).closest('.course').find('.manage-buttons').css('visibility', 'hidden');
        //$(e.target).closest('.course').find('.manage-buttons').hide();
    });
});