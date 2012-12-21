$(document).ready(function(){
    $('.items-list .list-item').hover(function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'visible').prev().hide();
    },function(e) {
        $(e.target).closest('.list-item').find('.manage-buttons').css('visibility', 'hidden').prev().show();
    });

    $('.calendar-widget a[rel="tooltip"]').tooltip({html: true});

    var validationResult = false;
    $('.invite-form').on('submit', function(){
        var self = this;
        $(this).find('.help-inline').remove();
        $(this).find('.control-group').removeClass('error');

        //validation to empty value
        if(!$.trim($(this).find('input').val()).length){
            validationResult = false;
            addError($(this).find('.control-group'), 'This value should not be blank.');
            return false;
        }

        if(!validationResult){
            $.get($(this).data('validate-url').replace('__ACCESS_CODE__', $(this).find('input').val()), function(data){
                validationResult = false;
                if(data.success){
                    validationResult = true;
                    $('.invite-form').submit();
                }else{
                    addError($(self).find('.control-group'), 'Wrong access code.');
                }
            });
        }

        return validationResult;
    });
});

var addError = function(controlGroup, errorMessage){
    controlGroup.addClass('error');
    controlGroup.find('.controls').append('<span class="help-inline">'+errorMessage+'</span>');
};