$(document).ready(function() {
    $('i.notifications').click(function(e) {
        $('.notifications-popup').slideToggle('', popupOpenCallback);
    });
    $('body').click(function(e) {
        if (!$(e.target).closest('.notifications-popup').hasClass('notifications-popup') && !$(e.target).closest('i.notifications').hasClass('notifications')) {
            $('.notifications-popup').slideUp(popupOpenCallback);
        }
    });

    var popupOpenCallback = function(e) {
        if ($(this).css('display') == 'block') {
            $.ajax({
                url: '/notifications/unread-list',
                dataType: 'json',
                success: function(response) {
                    if (!response.has_errors) {
                        $('.notifications-popup .notifications-list').html(response.content);
                    } else {
                        $('.notifications-popup .notifications-list').html('<div class="loading">Oops, seems like unknown error has appeared!</div>');
                    }
                },
                error: function() { $('.notifications-popup .notifications-list').html('<div class="loading">Oops, seems like unknown error has appeared!</div>'); },
                beforeSend: function() {
                    $('.notifications-popup .notifications-list').html('<div class="loading">Loading...</div>');
                }
            });
        } else {
            $('i.notifications small').remove();
            $('.sidebar-nav .menu-item i small').remove();
            $('.notifications-popup .notifications-list').html('');
        }
    }
});