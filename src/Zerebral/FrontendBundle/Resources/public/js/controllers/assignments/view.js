$(document).ready(function(){
    $('.manage').hover(function(e) {
        $(this).find('.manage-buttons').css('visibility', 'visible').prev().hide();
    },function(e) {
        $(this).find('.manage-buttons').css('visibility', 'hidden').prev().show();
    });


    $('#uploadModal').on('show', function(e) {
        $(this).find('ul li').remove();
        $('#addUploadFile').click();
    });

    $('#addUploadFile').click(function(e) {
        e.preventDefault();

        var ul = $(e.target).parents('form').find('.uploadedFiles');
        var fileFieldsCount = ul.find('li').length;
        var uploadedFileLisCount = $('.solutions-widget li.file').length;
        var index = fileFieldsCount + uploadedFileLisCount;


        var newLi = $('<li class="control-group"></li>');
        var newFileInput = $('<input type="file" name="assignment_solution[files][' + index + '][uploadedFile]" />');
        var newDescInput = $('<input type="text" name="assignment_solution[files][' + index + '][description]" placeholder="Description (optional)" />');
        newLi.append(newFileInput).append(newDescInput);
        if (fileFieldsCount == 0) {
            ul.prepend(newLi);
        } else {
            $(e.target).prev().append(newLi);
        }
    });

    $('#submitSolutionsButton').click(function(e) {
        if (window.confirm('Dou you want to publish submit your solutions? You cannot undo this action!')) {

        } else {
            e.preventDefault();
        }
    });

    $('.solutions-widget i.file-info').tooltip();

    $(document).on('click', '.remove-uploaded-file', function(e) {
        e.preventDefault();
        var link = $(e.target).attr('href');
        if (window.confirm('Are you sure to delete this solution?')) {
            $.ajax({
                type: 'post',
                url: link,
                success: function() {
                    $(e.target).parent().slideUp(function() {
                        $(this).remove();
                    });
                },
                error: function(response) {
                    alert(response.statusText);
                }
            })
        }
    });

    $('#ajaxUploadSolutionsForm').zerebralAjaxForm();

});