$(document).ready(function(){
    $('.form-signup .radiobox input[checked="checked"]').closest('label').addClass('selected');
    $('.form-signup .radiobox input').change(function(e) {
        $(e.target).parents('.radiobox').find('label').removeClass('selected');
        $(e.target).closest('label').addClass('selected');
    });

});