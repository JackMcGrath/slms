$(document).ready(function(){
    $('.manage').hover(function(e) {
        $(this).find('.manage-buttons').css('visibility', 'visible').prev().hide();
    },function(e) {
        $(this).find('.manage-buttons').css('visibility', 'hidden').prev().show();
    });

    $('.uploadedFiles').collectionFormType({
        add: '#addUploadFile',
        remove: '.remove-uploaded-file',
        item: '.file-item',
        template: '#solution_form'
    });

    $('#submitSolutionsButton').click(function(e) {
        if (window.confirm('Dou you want to publish submit your solutions? You cannot undo this action!')) {
        } else {
            e.preventDefault();
        }
    });

    $('.solutions-widget i.file-info').tooltip();

    $('#ajaxUploadSolutionsForm').zerebralAjaxForm({
        dataType: 'text'
    });

    $('.assignment-details-feed').zerebralAssignmentDetailFeedBlock();


    if (location.hash == '#assignmentFeed') {
        $.scrollTo('#assignmentFeed', 100, {offset:{top:-50}});
    }
});