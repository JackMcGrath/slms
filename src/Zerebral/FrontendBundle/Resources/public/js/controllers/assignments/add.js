$(document).ready(function(){
    // Dynamic files fields creating
    var collectionHolder = $('ul.files');
    // setup an "add a file" link
    var $addFileLink = $('<a href="#" class="add_file_link">Add a file</a>');
    var $newLinkLi = $('<li></li>').append($addFileLink);
    collectionHolder.append($newLinkLi);
    $addFileLink.on('click', function(e) {
        e.preventDefault();
        var prototype = collectionHolder.attr('data-prototype');
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on the current collection's length.
        var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);
        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<li></li>').append(newForm);
        $newLinkLi.before($newFormLi);
    });


    $('.icon-new-calendar').datepicker();
    $('.icon-new-clock').timepicker();
    $('textarea').wysihtml5();

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

    $('.optional-model').optionalModel();
});
