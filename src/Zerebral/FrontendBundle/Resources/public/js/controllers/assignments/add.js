$(document).ready(function(){
    $('.icon-new-calendar').datepicker();
    $('.icon-new-clock').timepicker();
    $('textarea').wysihtml5();

    $('.show-input').on('click', showInput);
    $('.show-dropdown').on('click', showDropdown);

    $('#studentsModal').on('hidden', function () {
        $('.student_select').removeAttr('disabled').attr('checked', '1');
        var selectedItemsCount = $('.student-list input[type=checkbox]:checked').length;
        var allItemsCount = $('.student-list input[type=checkbox]').length;
        $('.student_select').parent().find('a').text(selectedItemsCount + '/' + allItemsCount + " students selected");
    });

    $('.student_select_all').on('change', function(e){
        $('.student_select').attr('disabled', '1');
        $('.student-list input[type=checkbox]').each(function(index, element){
            $(element).attr('checked', 1);
        });
    });

    $('.student-list tr').on('click', function(e){
        if($(e.target).prop("tagName") != "INPUT"){
            var checkbox = $(this).find('input');
            if(checkbox.is(':checked')){
                checkbox.removeAttr('checked');
            }else{
                checkbox.attr('checked', 1);
            }
        }
    });

    $('.toggle_selection input').on('change', function(e){
        if($(this).is(':checked')){
            $('.student-list input[type=checkbox]').each(function(index, element){
                $(element).attr('checked', 1);
            });
        }else{
            $('.student-list input[type=checkbox]').each(function(index, element){
                $(element).removeAttr('checked');
            });
        }
    });
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