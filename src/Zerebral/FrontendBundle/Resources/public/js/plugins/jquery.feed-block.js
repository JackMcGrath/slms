var ZerebralCourseDetailFeedBlock = function(element, options) {
    var self = this;
    self.element = element;
    self.feedItemFormTextarea = element.find('.feed-item-textarea');
    self.feedItemForm = element.find('#ajaxFeedItemForm');
    self.feedItemFormDiv = element.find('.feed-item-form');

    self.itemsDiv = element.find('.feed-item');
    self.commentsDiv = element.find('.feed-item .comments');
    self.options = options;
};

ZerebralCourseDetailFeedBlock.prototype = {
    element: undefined,
    options: undefined,

    feedItemFormTextarea: undefined,
    feedItemForm: undefined,
    feedItemFormDiv: undefined,
    commentsDiv: undefined,
    itemsDiv: undefined,


    init: function() {
        var self = this;
        this.feedItemFormTextarea.click($.proxy(self.expandFeedItemForm, self));
        this.feedItemFormDiv.find('.buttons .cancel-link').click($.proxy(self.collapseFeedItemForm, self));
        this.feedItemFormDiv.find('.attach-link').click($.proxy(self.setFeedItemFormType, self));
        this.feedItemFormDiv.find('.attached-link-delete a').click($.proxy(self.resetMainFormType, self));



        this.commentsDiv.find('.comment-input').click($.proxy(self.expandCommentForm, self));
        this.commentsDiv.find('.comment .buttons .cancel-link').click($.proxy(self.collapseCommentForm, self));

        this.itemsDiv.find('.show-comment-form-link').click($.proxy(self.showCommentForm, self));

        this.feedItemForm.zerebralAjaxForm();
        $.each(this.commentsDiv.find('form'), function(index, value) {
            $(this).zerebralAjaxForm({
                success: $.proxy(self.addCommentBlock, this),
                error: function() {
                    alert('Oops, seems like unknown error has appeared!');
                },
                dataType: 'html'
            });
        })
    },

    expandFeedItemForm: function(event) {
        var textarea = $(event.target);
        textarea.data('background-image', textarea.css('background-image'));
        textarea.css('background-image', 'none').animate({
            width: 621,
            'margin-top': 20,
            'margin-bottom': 10,
            'margin-left': 20,
            'margin-right': 20,
            'padding-left': 6,
            'padding-right': 6,
            height: '+120'
        }, 300);
        this.feedItemForm.css('background-color', '#f3f3f3').find('.feed-item-form-controls').show();
    },
    collapseFeedItemForm: function(event) {
        event.preventDefault();

        var self = this;
        this.feedItemFormTextarea.parent().animate({'background-color': 'transparent'}).find('.feed-item-form-controls').hide();
        this.feedItemFormTextarea.animate({
            width: 571,
            margin: 0,
            'padding-right': 96,
            height: 18
        }, 500, function() {
            self.feedItemFormTextarea.css('background-image', self.feedItemFormTextarea.data('background-image'));
        });

    },
    setFeedItemFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedItemForm.find('.attach-links').hide();
        this.feedItemForm.find('input.comment-type').val(link.parent().data('linkType'));
        this.feedItemForm.find('.attached-link').slideDown();
    },
    resetMainFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedItemForm.find('.attached-link').slideUp();
        this.feedItemForm.find('input.comment-type').val('text');
        this.feedItemForm.find('.attach-links').show();
    },
    expandCommentForm: function(event) {
        var input = $(event.target);
        input.animate({
            height: '+60'
        }, 300);
        input.parent().find('.buttons').show();
    },
    collapseCommentForm: function(event) {
        event.preventDefault();
        var link = $(event.target);
        link.parent().hide();
        link.parents('form').find('.comment-input').animate({
            height: '18'
        }, 300).val('');

    },
    showCommentForm: function(event) {
        event.preventDefault();

        var link = $(event.target);
        link.parents('.feed-item').find('.comment.hidden').removeClass('hidden');
    },
    addCommentBlock: function(response) {
        $(this).parents('.comment').before(response);
        var commentsCount = $(this).parents('.feed-item').find('.show-comment-form-link').data('commentsCount');
        $(this).parents('.feed-item').find('.show-comment-form-link span').html((commentsCount + 1));
        $(this).find('.cancel-link').click();

    },
    _: ''
};

$.registry('zerebralCourseDetailFeedBlock', ZerebralCourseDetailFeedBlock, {
    methods: ['init']
});




//    ASSIGNMENT
var ZerebralAssignmentDetailFeedBlock = function(element, options) {
    var self = this;
    self.element = element;
    self.feedCommentFormDiv = element.find('.feed-comment-form');
    self.feedCommentForm = element.find('#ajaxFeedCommentForm');
    self.options = options;
};

ZerebralAssignmentDetailFeedBlock.prototype = {
    element: undefined,
    options: undefined,

    feedCommentFormDiv: undefined,
    feedCommentForm: undefined,


    init: function() {
        var self = this;
        this.feedCommentFormDiv.find('.attach-link').click($.proxy(self.setFeedItemFormType, self));
        this.feedCommentFormDiv.find('.attached-link-delete a').click($.proxy(self.resetMainFormType, self));

        this.feedCommentForm.zerebralAjaxForm();
    },

    setFeedItemFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedCommentForm.find('.attach-links').hide();
        this.feedCommentForm.find('input.comment-type').val(link.parent().data('linkType'));
        this.feedCommentForm.find('.attached-link').slideDown();
    },
    resetMainFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedCommentForm.find('.attached-link').slideUp();
        this.feedCommentForm.find('input.comment-type').val('text');
        this.feedCommentForm.find('.attach-links').show();
    },
    _: ''
};

$.registry('zerebralAssignmentDetailFeedBlock', ZerebralAssignmentDetailFeedBlock, {
    methods: ['init']
});