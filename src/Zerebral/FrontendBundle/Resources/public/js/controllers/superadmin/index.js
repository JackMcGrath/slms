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


    $('#usersListFilter').change(function(event) {
        var value = $(this).find('option:selected').val();
        var url = new URI(window.location);
        url.removeQuery('filter');
        if (value != 'all') {
            url.addQuery('filter', value);
        }
        window.location = url;
    });

    $('#usersListSearchButton').click(function() {
        var value = $('#usersListSearch').val().trim();
        var url = new URI(window.location);
        url.removeQuery('search');
        if (value.length > 0) {
            url.addQuery('search', value);
        }
        window.location = url;
    });

    $('#usersListSearchClearButton').click(function() {
        $('#usersListSearch').val('');
        $('#usersListSearchButton').click();
    });
});