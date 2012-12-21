$(document).ready(function() {
    $('.delete-confirm').click(function(e) {
        var message = $(e.target).closest('a').attr('confirm') || 'Are you sure you want to delete?'
        return confirm(message);
    })
});