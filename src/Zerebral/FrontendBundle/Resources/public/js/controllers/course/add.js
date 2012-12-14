$(document).ready(function(){
    $('.show-input').on('click', showInput);
    $('.show-dropdown').on('click', showDropdown);
    $('textarea').wysihtml5();
    $('.optional-model').optionalModel();
});