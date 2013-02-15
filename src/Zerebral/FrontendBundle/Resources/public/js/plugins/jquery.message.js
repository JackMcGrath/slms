var PrivateMessages = function(element, options) {
    this.element = element;
    this.options = options;
};

PrivateMessages.prototype = {
    element: undefined,
    options: undefined,
    lastIndex: undefined,
    template: undefined,
    userId: undefined,
    recipients: undefined,

    init: function() {
        var self = this;
        $(this.options.openPopupLink).on('click', $.proxy(this.onShowPopup, this));
        this.initForm();

        this.bindPopup();
    },

    bindPopup: function() {
        var self = this;
        this.element.on('show', function(e) {
            var modalBody = $(this).find('.modal-body');
            modalBody.html('<p>Loading...</p>');

            $.ajax({
                url: self.userId ? '/messages/compose-form/' + self.userId : '/messages/compose-multiple-form?' + self.recipients,
                dataType: 'json',
                type: 'GET',
                success: function(response) {
                    if (!response.has_errors) {
                        modalBody.html(response.content);
                        self.initForm();
                    }
                }
            });
        });
        var popupForm = self.element.find('.message-form');
        popupForm.zerebralAjaxForm({
            onSuccess: function(response) {
                if (response.success) {
                    self.element.modal('hide');
                    $('body .main-list-block, body .main-content-block').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        'Message has been successfully sent.</div>')
                }
            },
            beforeSend: function() {
                popupForm.find('button[type="submit"], a, textarea').attr('disabled', true);
                var button = popupForm.find('button[type="submit"]');
                button.data('originalCaption', button.html()).html('Submitting');
            },
            complete: function() {
                popupForm.find('button[type="submit"], a, textarea').attr('disabled', false);
                var button = popupForm.find('button[type="submit"]');
                button.html(button.data('originalCaption'));
            }
        });
    },

    initForm: function() {
        this.element.find('textarea').wysihtml5();

        $('#upload_files_div').collectionFormType({
            add: '.add_file_link',
            remove: '.remove-uploaded-file',
            item: '.file-item',
            template: '#new_file_form'
        });

        var select = $('#message_to');

        if (select.find('option').length && !select.val()) {

        } else if (select.val()) {
//            $.each(select.find('option'), function(i, option){
//                if ($(option).attr('value') != $('#message_to').val()) {
//                    $(option).remove();
//                }
//            });
        }

        select.ajaxChosen({
            type: 'GET',
            url: '/user/suggest',
            dataType: 'json',
            multiple: true
        }, function (data) {
            var results = [];
            $.each(data.users, function (i, user) {
                results.push({ value: user.id, text: user.name });
            });
            return results;
        });

        select.bind('liszt:hiding_dropdown', function(event, chosen) {
            $('.chzn-drop ul.chzn-results').empty();
            $(".search-field > input, .chzn-search > input").data('prevVal', '');
        });

        $('#message_to_chzn li.search-field input').bind('keyup', function(e) {
            if ($(e.target).val().length == 0) {
                $('.chzn-drop ul.chzn-results').empty();
            }
        });

        var getSuggest = function(process) {
            $.ajax({
                url: '/user/suggest',
                type: "get",
                data: {
                    username: $('#message_toName').val()
                },
                success: function(response) {
                    return process(response.users);
                }
            });
        };
    },

    onShowPopup: function(e) {
        e.preventDefault();
        this.userId = $(e.target).attr('userId');
        var recipients = $(e.target).attr('recipients');
        this.recipients = 'recipients[]=' + (recipients ? recipients.replace(/,/g, '&recipients[]=') : '');
        $(this.element).modal('show');
    },

    _: ''
};

$.registry('privateMessages', PrivateMessages, {
    methods: ['init'],
    defaults: {
        openPopupLink: '[rel="message-popup"]'
    }
});