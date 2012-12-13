$(document).ready(function(){
    $('.show-input').on('click', showInput);
    $('.show-dropdown').on('click', showDropdown);
});

var showInput = function(e){
    e.preventDefault();

    $(this).closest('.controls').find('input').removeAttr('disabled').show();
    $(this).parent().find('.show-dropdown').show();
    $(this).hide();
    $(this).closest('.controls').find('select').attr('disabled', 'disabled').hide();
};

var showDropdown = function(e){
    e.preventDefault();

    $(this).closest('.controls').find('input').attr('disabled', 'disabled').hide();
    $(this).parent().find('.show-input').show();
    $(this).hide();
    $(this).closest('.controls').find('select').removeAttr('disabled').show();
};