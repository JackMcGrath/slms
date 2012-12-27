$(document).ready(function() {
    if (typeof(Controller) != 'undefined') {
        window.controller = new Controller();
        if (!window.controller.delayed)
            window.controller.init();

        $(document).trigger('init');
    }
    $('.delete-confirm').click(function(e) {
        var message = $(e.target).closest('a').attr('confirm') || 'Are you sure you want to delete?'
        return confirm(message);
    });
});