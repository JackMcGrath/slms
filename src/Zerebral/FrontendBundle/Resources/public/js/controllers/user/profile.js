$(document).ready(function(){
    $('input.birthday, .icon-new-calendar').datepicker();


    $('#show_avatar_file_field').click(function(e) {
        $(this).hide();
        $('div.hidden-avatar-div').show();
        e.preventDefault();
    });

});

