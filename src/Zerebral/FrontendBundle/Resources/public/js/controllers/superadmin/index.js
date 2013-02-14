$(document).ready(function() {
    $('tr button.btn').click(function(event) {
        var button = $(event.target);
        var userName = button.data('full-name');
        var state = button.data('state');
        var url = button.data('path');
        if (state == 'blocked') {
            var message = 'unblock';
        } else {
            var message = 'block';
        }
        if (window.confirm('This action will ' + message + ' user "' + userName + '". Are you sure to continue?')) {
            $.ajax({
                url: url,
                method: 'post',
                dataType: 'json',
                complete: function() {
                    window.location.reload();
                }
            })
        }
    });
});