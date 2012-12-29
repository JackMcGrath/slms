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


    $('#comment_input').click(function(e) {
        $(e.target).css('background-image', 'none').animate({
            width: 621,
//            top: 60,
            'margin-top': 20,
            'margin-bottom': 10,
            'margin-left': 20,
            'margin-right': 20,
            'padding-left': 6,
            'padding-right': 6,
            height: '+120'
        }, 300);
        $(e.target).parent().css('background-color', '#f3f3f3').find('.controls').show();
    });

    $('.attach-link').click(function(e) {
        e.preventDefault();
        $(e.target).parent().parent().hide();
        $(e.target).parent().parent().parent().parent().find('#comment-type').val($(e.target).parent().data('linkType'));
        $(e.target).parent().parent().parent().parent().find('.attached-link').slideDown();
    });

    $('.attached-link-delete a').click(function(e) {
        e.preventDefault();
        $(e.target).parent().parent().slideUp();
        $(e.target).parent().parent().parent().find('#comment-type').val('text');
        $(e.target).parent().parent().parent().find('.attach-links').show();

    });
    $('#ajaxFeedCommentForm').zerebralAjaxForm();

    $('#ajaxUploadSolutionsForm').zerebralAjaxForm();

});