$(document).ready(function(){
    // Dynamic files fields creating
    var addAttachmentField = function(e) {
        if (e) {
            e.preventDefault();
        }
        var prototype = $('div#attachments_div').attr('data-prototype');
        var newForm = $(prototype.replace(/__name__/g, $('div#upload_files_div div').length));
        newForm.find('input').attr('name', newForm.find('input').attr('name') + '[uploadedFile]');
        $('div#upload_files_div').append(newForm);
    }

    $('.add_file_link').on('click', addAttachmentField);


    $('.icon-new-calendar').datepicker();
    $('.icon-new-clock').timepicker({
        defaultTime: 'value'
    });
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
