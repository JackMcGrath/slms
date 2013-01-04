var ZerebralFeedBlock = function(element, options) {
    var self = this;
    self.element = element;
    self.mainFormInput = element.find('#comment_input');
    self.mainForm = element.find('#ajaxFeedCommentForm');
    self.options = options;
};

ZerebralFeedBlock.prototype = {
    element: undefined,
    options: undefined,

    mainFormInput: undefined,


    init: function() {
        var self = this;
        this.mainFormInput.click(this.expandMainForm);
        this.element.find('.attach-link').click(this.setMainFormType);
        this.element.find('.attached-link-delete a').click(this.resetMainFormType);
        this.element.find('.comment-input').click(this.expandCommentForm);
        this.element.find('.form .buttons .cancel-link').click(function(event) {
            event.preventDefault();
            self.collapseMainForm(self.mainFormInput);
        });
        this.element.find('.comment .buttons .cancel-link').click(self.collapseCommentForm);
        this.mainForm.zerebralAjaxForm();
    },

    expandMainForm: function(event) {
        var input = $(event.target);
        input.data('background-image', input.css('background-image'));
        input.css('background-image', 'none').animate({
            width: 621,
            'margin-top': 20,
            'margin-bottom': 10,
            'margin-left': 20,
            'margin-right': 20,
            'padding-left': 6,
            'padding-right': 6,
            height: '+120'
        }, 300);
        input.parent().css('background-color', '#f3f3f3').find('.controls').show();
    },
    collapseMainForm: function(input) {
        input.parent().animate({'background-color': 'transparent'}).find('.controls').hide();;
        input.animate({
            width: 571,
            margin: 0,
            'padding-right': 96,
            height: 18
        }, 500, function() {
            input.css('background-image', input.data('background-image'));
        });

    },
    setMainFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        link.parent().parent().hide();
        link.parent().parent().parent().parent().find('#comment-type').val(link.parent().data('linkType'));
        link.parent().parent().parent().parent().find('.attached-link').slideDown();
    },
    resetMainFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        link.parent().parent().slideUp();
        link.parent().parent().parent().find('#comment-type').val('text');
        link.parent().parent().parent().find('.attach-links').show();
    },
    expandCommentForm: function(event) {
        var input = $(event.target);
        input.animate({
            height: '+60'
        });
        input.parent().find('.buttons').show();
    },
    collapseCommentForm: function(event) {
        event.preventDefault();
        var link = $(event.target);
        link.parent().hide();
        link.parents('form').find('.comment-input').animate({
            height: '18'
        });

    },
    _: ''
};

$.registry('zerebralFeedBlock', ZerebralFeedBlock, {
    methods: ['init']
});