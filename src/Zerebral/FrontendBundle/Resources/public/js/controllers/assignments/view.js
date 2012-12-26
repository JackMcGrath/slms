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

        var ul = $(e.target).parent();
        var liCount = ul.find('li').length;
        var newLi = $('<li><input type="file" name="solution[file][' + liCount + ']" /><input type="text" placeholder="Description (optional)" /></li>');
        if (liCount == 0) {
            ul.prepend(newLi);
        } else {
            $(e.target).prev().append(newLi);
        }

    });

});