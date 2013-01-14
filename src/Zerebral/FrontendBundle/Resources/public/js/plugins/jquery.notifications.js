$(document).ready(function() {
    $('i.notifications').click(function(e) {
        $('.notifications-popup').slideToggle('', function(e) {
            if ($(this).css('display') == 'block') {
                $.ajax({
                    url: '/notifications/unread-list',
                    dataType: 'json',
                    success: function(response) {
                        if (!response.has_errors) {
                            $('.notifications-popup .notifications-list').html(response.content);
                        }
                    },
                    beforeSend: function() {
                        $('.notifications-popup .notifications-list').html('<div class="loading">Loading...</div>');
                    }
                });
            } else {
                $('i.notifications small').remove();
                $('.sidebar-nav .menu-item i small').remove();
                $('.notifications-popup .notifications-list').html('');
            }
        });
    });
});