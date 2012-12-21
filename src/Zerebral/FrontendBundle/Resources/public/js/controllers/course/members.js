$(document).ready(function(){
    $('.member-form').on('submit', function(){
        $(this).find('.help-inline').remove();
        $(this).find('.control-group').removeClass('error');

        //validation to empty value
        if(!$.trim($(this).find('textarea').val()).length){
            addError($(this).find('.control-group'), 'This value should not be blank.');
            return false;
        }

        //email validation
        if(!multiEmail($.trim($(this).find('textarea').val()))){
            addError($(this).find('.control-group'), 'Please enter one ore more email addresses into the text box, separated by space or new lines.');
            return false;
        }


        return true;
    });
});

var addError = function(controlGroup, errorMessage){
        controlGroup.addClass('error');
        controlGroup.find('.controls').append('<span class="help-inline">'+errorMessage+'</span>');
};

var validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

var multiEmail = function (email_field) {
    var emails = email_field.replace(/\n/g, ' ').replace(/ +(?= )/g,'').split(' ');
    for (var i = 0; i < emails.length; i++) {
        if (!validateEmail(emails[i])) {
            return false;
        }
    }
    return true;
};