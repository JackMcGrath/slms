$(document).ready(function(){
    $('input.birthday, .icon-new-calendar').datepicker({
        endDate: '01/01/' + moment().format('YYYY'),
        startDate: '01/01/1900'
    });


    $('#show_avatar_file_field').click(function(e) {
        $(this).hide();
        $('div.hidden-avatar-div').show();
        $('#profile_user_avatar_description').hide();
        e.preventDefault();
    });


    $('.member-form').zerebralAjaxForm({
        beforeSend: function() {
            $('.member-form').find('input[type="submit"], a, textarea').attr('disabled', true);
        },
        complete: function() {
            $('.member-form').find('input[type="submit"], a, textarea').attr('disabled', false);
        }
    });

});

