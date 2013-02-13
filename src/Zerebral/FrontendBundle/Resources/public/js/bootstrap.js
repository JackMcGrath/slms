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

    $('[rel="tooltip"]').tooltip({html: true});

    $.ajaxSetup({
        complete: function(response) {
            //@TODO: TBD about better way to determine AJAX redirect
            if (response.responseText.substr(0, 15) == '<!DOCTYPE html>') {
                window.location = '/';
            }
        }
    });

    $('#composeMessageModal').privateMessages(); //popup
    $('div.message-form').privateMessages(); //compose

    $('input, textarea').placeholder();
});