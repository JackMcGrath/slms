$(document).ready(function(){
    $('.member-form').zerebralAjaxForm({
        beforeSend: function() {
            $('.member-form').find('input[type="submit"], a, textarea').attr('disabled', true);
        },
        complete: function() {
            $('.member-form').find('input[type="submit"], a, textarea').attr('disabled', false);
        }
    });
});