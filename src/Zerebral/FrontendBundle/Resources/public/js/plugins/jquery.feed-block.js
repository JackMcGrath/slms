var ZerebralCourseDetailFeedBlock = function(element, options) {
    var self = this;
    self.element = element;
    self.feedItemFormTextarea = element.find('.feed-item-textarea');
    self.feedItemForm = element.find('#ajaxFeedItemForm');
    self.feedItemFormDiv = element.find('.feed-item-form');

    self.feedItemAlertBlock = element.find('.feed-item-alert-block');

    self.feedItemsDiv = element.find('.feed-items');
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
    feedItemsDiv: undefined,


    timeOffset: null,

    ajaxInProgress: false,
    errorHasAppeared: 0,


    init: function() {
        var self = this;
        this.feedItemFormTextarea.click($.proxy(self.expandFeedItemForm, self));
        this.feedItemFormDiv.find('.buttons .cancel-link').click($.proxy(self.collapseFeedItemForm, self));
        this.feedItemFormDiv.find('.attach-link').click($.proxy(self.setFeedItemFormType, self));
        this.feedItemFormDiv.find('.attached-link-delete a').click($.proxy(self.resetMainFormType, self));


        this.feedItemsDiv.on('click', '.comment-input', $.proxy(self.expandCommentForm, self));
        this.feedItemsDiv.on('click', '.comment .buttons .cancel-link', $.proxy(self.collapseCommentForm, self));
        this.feedItemsDiv.on('click', '.show-comment-form-link', $.proxy(self.showCommentForm, self));
        this.feedItemsDiv.on('click', 'a.delete-link.delete-feed-item', $.proxy(self.deleteItemBlock, self));
        this.feedItemsDiv.on('click', 'a.delete-link.delete-comment', $.proxy(self.deleteCommentBlock, self));

        this.commentsDiv.on('click', 'a.load-more-link', $.proxy(self.loadComments, self));

        self.timeOffset = moment(this.feedItemsDiv.data('serverTime'), 'YYYY-MM-DD HH:mm:ss').diff(moment(), 'seconds');
        self.updateFeed();
        setInterval($.proxy(self.updateFeed, self), 5000);

        this.feedItemForm.zerebralAjaxForm({
            dataType: 'json',
            beforeSend: function() {
                self.ajaxInProgress = true;
                self.feedItemForm.find('.control-group').removeClass('error');
                self.feedItemFormTextarea.attr('disabled', true);
                self.feedItemForm.find('.attached-link-field').attr('disabled', true);
                self.feedItemForm.find('input[type="submit"]').attr('disabled', true).val('   Posting...    ');
                self.feedItemForm.find('a.cancel-link').hide();
                self.feedItemForm.find('.attached-link-delete').hide();
                self.feedItemAlertBlock.slideUp('fast', function() {
                    self.feedItemAlertBlock.find('ul > li').remove();
                });
            },
            success: function(response) {
                if (response['has_errors']) {
                    self.feedItemAlertBlock.slideDown();
                    var ul = self.feedItemAlertBlock.find('ul');
                    for (var fieldName in response['errors']) {
                        var field = self.feedItemForm.find('[name^="' + fieldName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]').last();
                        field.parents('.control-group').addClass('error');
                        ul.append($('<li>' + response['errors'][fieldName][0] + '</li>'));
                    }
                } else {
                    self.feedItemsDiv.data('lastItemId', response['lastItemId']);
                    self.addItemBlock(response['content'], true);
                }
            },
            error: function() { alert('Oops, seems like unknown error has appeared!'); },
            complete: function() {
                self.feedItemFormTextarea.attr('disabled', false);
                self.feedItemForm.find('.attached-link-field').attr('disabled', false);
                self.feedItemForm.find('input[type="submit"]').attr('disabled', false).val('Post message');
                self.feedItemForm.find('a.cancel-link').show();
                self.feedItemForm.find('.attached-link-delete').show();
                self.ajaxInProgress = false;
            }
        });

        $.each(this.commentsDiv.find('form'), function(index, value) {
            var form = $(this);
            $(this).zerebralAjaxForm({
                beforeSend: function() {
                    form.find('textarea').attr('disabled', true);
                    form.find('input[type="submit"]').attr('disabled', true);
                    form.find('a.cancel-link').hide();
                },
                data: { feedType: 'course' },
                success: function(response) {
                    if (response['has_errors']) {
                        for (var fieldName in response['errors']) {
                            var field = form.find('[name^="' + fieldName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]').last();
                            field.parents('.control-group').addClass('error');
                        }
                    } else {
                        $.proxy(self.addCommentBlock, form, response['content'])();
                    }
                },
                error: function() { alert('Oops, seems like unknown error has appeared!'); },
                complete: function() {
                    form.find('textarea').attr('disabled', false);
                    form.find('input[type="submit"]').attr('disabled', false);
                    form.find('a.cancel-link').show();
                },
                dataType: 'json'
            });
        })
    },

    updateFeed: function() {
        var self = this;

        if (!self.ajaxInProgress && self.errorHasAppeared < 2) {
            $.ajax({
                dataType: 'json',
                url: self.feedItemsDiv.data('checkoutUrl'),
                data: {
                    lastItemId: self.feedItemsDiv.data('lastItemId')
                },
                success: function(response) {
                    if (response.success) {
                        self.feedItemsDiv.data('lastItemId', response['lastItemId']);
                        $.proxy(self.addItemBlock(response['content'], false), self);
                    }
                },
                error: function() { self.errorHasAppeared++; }
            });
        }

        var currentTime = moment().add('seconds', self.timeOffset);
        var timestamps = this.feedItemsDiv.find('span.timestamp, div.timestamp>span.gray');
        $.each(timestamps, function(index, value) {
            var itemDate = moment($(value).data('date'), 'YYYY-MM-DD HH:mm:ss');
            var humanDate = itemDate.from(currentTime);
            $(value).html(humanDate);
        });
    },

    loadComments: function(event) {
        event.preventDefault();
        var link = $(event.target);
        if (!link.data('disabled')) {
            var loadingSpan = link.prev('span.load-more-link');
            link.hide();
            loadingSpan.removeClass('hidden');
            $.ajax({
                type: 'get',
                url: link.attr('href'),
                data: {
                    lastCommentId: link.data('lastCommentId')
                },
                success: function(response) {
                    if (response.success) {
                        link.data('lastCommentId', response['lastCommentId']);
                        link.parents('.comment').after(response.content);
                        link.data('loadedCount', (link.data('loadedCount') + response['loadedCount']));

                        if (link.data('loadedCount') >= link.data('totalCount')) {
                            link.html('No more comments').data('disabled', true);
                            link.parents('.comment').delay(500).slideUp('fast', function() {
                                link.parents('.comment').remove();
                            });
                        }
                    } else {
                        this.error(response);
                    }
                },
                error: function() { alert('Oops, seems like unknown error has appeared!'); },
                complete: function() {
                    loadingSpan.addClass('hidden');
                    link.show();
                },
                dataType: 'json'
            });
        }
    },

    expandFeedItemForm: function() {
        this.feedItemFormTextarea.data('background-image', this.feedItemFormTextarea.css('background-image'));
        this.feedItemFormTextarea.css('background-image', 'none').animate({
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
        if (event) {
            event.preventDefault();
        }

        var self = this;
        self.resetMainFormType();
        self.feedItemForm.find('.control-group').removeClass('error');
        this.feedItemFormTextarea.val('').parents('form').animate({'background-color': 'transparent'}).find('.feed-item-form-controls').hide();
        this.feedItemFormTextarea.animate({
            width: 571,
            margin: 0,
            'padding-right': 96,
            height: 18
        }, 500, function() {
            self.feedItemFormTextarea.css('background-image', self.feedItemFormTextarea.data('background-image'));
        });

        self.feedItemAlertBlock.slideUp('fast', function() {
            self.feedItemAlertBlock.find('ul > li').remove();
        });

    },
    setFeedItemFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedItemForm.find('.attach-links').hide();
        this.feedItemForm.find('input.comment-type').val(link.parent().data('linkType'));
        this.feedItemForm.find('.attached-link').slideDown();
        this.feedItemForm.find('.attached-link-field').val('');
        switch (link.parent().data('linkType')) {
            case 'video': {
                this.feedItemForm.find('.attached-link-field').attr('placeholder', 'Insert link to YouTube or Vimeo video page...');
                break;
            }
            case 'website': {
                this.feedItemForm.find('.attached-link-field').attr('placeholder', 'Insert link to website...');
                break;
            }
            case 'image': {
                this.feedItemForm.find('.attached-link-field').attr('placeholder', 'Insert link to image...');
                break;
            }
            default: {
                this.feedItemForm.find('.attached-link-field').attr('placeholder', '');
                break;
            }
        }
    },
    resetMainFormType: function(event) {
        if (event) {
            event.preventDefault();
        }
        this.feedItemForm.find('.attached-link').slideUp();
        this.feedItemForm.find('input.comment-type').val('text');
        this.feedItemForm.find('.attach-links').show();
        this.feedItemForm.find('.attached-link .control-group').removeClass('error');
    },

    expandCommentForm: function(event) {
        var input = $(event.target);
        input.animate({
            height: '+60'
        }, 300);
        input.parents('form').find('.buttons').show();
    },
    collapseCommentForm: function(event) {
        event.preventDefault();
        var link = $(event.target);
        link.parent().hide();
        link.parents('form').find('.comment-input').parents('.control-group').removeClass('error').end().animate({
            height: '18'
        }, 300).val('');

    },
    showCommentForm: function(event) {
        event.preventDefault();

        var link = $(event.target);
        link.parents('.feed-item').find('.comment.hidden').removeClass('hidden');
    },
    addItemBlock: function(response, collapseForm) {
        var self = this;
        var itemBlock = $(response);
        var form = itemBlock.find('form');
        itemBlock.find('form').zerebralAjaxForm({
            beforeSend: function() {
                form.find('textarea').attr('disabled', true);
                form.find('input[type="submit"]').attr('disabled', true);
                form.find('a.cancel-link').hide();
            },
            data: { feedType: 'course' },
            success: function(response) {
                if (response['has_errors']) {
                    for (var fieldName in response['errors']) {
                        var field = form.find('[name^="' + fieldName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]').last();
                        field.parents('.control-group').addClass('error');
                    }
                } else {
                    $.proxy(self.addCommentBlock, form, response['content'])();
                }
            },
            error: function() { alert('Oops, seems like unknown error has appeared!'); },
            complete: function() {
                form.find('textarea').attr('disabled', false);
                form.find('input[type="submit"]').attr('disabled', false);
                form.find('a.cancel-link').show();
            },
            dataType: 'json'

        });
        this.feedItemsDiv.find('.empty').remove().end().prepend(itemBlock);
        if (collapseForm) {
            this.collapseFeedItemForm();
        }
    },
    deleteItemBlock: function(event) {
        event.preventDefault();
        var link = $(event.target);
        if (window.confirm('Are you sure to delete post?')) {
            var url = link.attr('href');
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    link.parents('.feed-item').slideUp('fast', function() {
                        link.parents('.feed-item').remove();
                    });
                },
                error: function() {alert('Oops, seems like unknown error has appeared!') }
            })
        }
    },
    addCommentBlock: function(response) {
        $(this).parents('.comment').before(response);
        var commentsCount = $(this).parents('.feed-item').find('.show-comment-form-link').data('commentsCount');
        $(this).parents('.feed-item').find('.show-comment-form-link span').html((commentsCount + 1));
        $(this).parents('.feed-item').find('.show-comment-form-link').data('commentsCount', commentsCount + 1)
        $(this).find('.cancel-link').click();

    },
    deleteCommentBlock: function(event) {
        event.preventDefault();
        var link = $(event.target);
        if (window.confirm('Are you sure to delete comment?')) {
            var url = link.attr('href');
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    var commentsCount = link.parents('.feed-item').find('.show-comment-form-link').data('commentsCount');
                    link.parents('.feed-item').find('.show-comment-form-link span').html((commentsCount - 1));
                    link.parents('.feed-item').find('.show-comment-form-link').data('commentsCount', commentsCount - 1);
                    link.parents('.comment').slideUp('fast', function() {
                        link.parents('.comment').remove();
                    });
                },
                error: function() {alert('Oops, seems like unknown error has appeared!') }
            })
        }
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
    self.feedCommentsDiv = element.find('.comments');
    self.feedCommentAlertBlock = element.find('.feed-comment-alert-block');
    self.options = options;
};

ZerebralAssignmentDetailFeedBlock.prototype = {
    element: undefined,
    options: undefined,

    feedCommentFormDiv: undefined,
    feedCommentForm: undefined,
    feedCommentsDiv: undefined,
    timeOffset: null,

    ajaxInProgress: false,
    errorHasAppeared: 0,


        init: function() {
        var self = this;
        this.feedCommentFormDiv.find('.attach-link').click($.proxy(self.setFeedItemFormType, self));
        this.feedCommentFormDiv.find('.attached-link-delete a').click($.proxy(self.resetMainFormType, self));

        this.feedCommentsDiv.on('click', 'a.delete-link', $.proxy(self.deleteCommentBlock, self));

        self.timeOffset = moment(this.feedCommentsDiv.data('serverTime'), 'YYYY-MM-DD HH:mm:ss').diff(moment(), 'seconds');
        self.updateFeed();
        setInterval($.proxy(self.updateFeed, self), 5000);


        this.feedCommentForm.zerebralAjaxForm({
            data: { feedType: 'assignment' },
            beforeSend: function() {
                self.ajaxInProgress = true;
                self.feedCommentForm.find('.control-group').removeClass('error');
                self.feedCommentForm.find('textarea').attr('disabled', true);
                self.feedCommentForm.find('.attached-link-field').attr('disabled', true);
                self.feedCommentForm.find('input[type="submit"]').attr('disabled', true).val('   Posting...    ');
                self.feedCommentForm.find('.attached-link-delete').hide();
                self.feedCommentAlertBlock.slideUp('fast', function() {
                    self.feedCommentAlertBlock.find('ul > li').remove();
                });
            },
            success: function(response) {
                //$.proxy(self.addCommentBlock, this),
                if (response['has_errors']) {
                    self.feedCommentAlertBlock.slideDown();
                    var ul = self.feedCommentAlertBlock.find('ul');
                    for (var fieldName in response['errors']) {
                        var field = self.feedCommentForm.find('[name^="' + fieldName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]').last();
                        field.parents('.control-group').addClass('error');
                        ul.append($('<li>' + response['errors'][fieldName][0] + '</li>'));
                    }
                } else {
                    self.addCommentBlock(response['content']);
                    self.feedCommentsDiv.data('lastCommentId', response['lastCommentId']);
                }
            },
            error: function() { alert('Oops, seems like unknown error has appeared!'); },
            complete: function() {
                self.feedCommentForm.find('textarea').attr('disabled', false);
                self.feedCommentForm.find('.attached-link-field').attr('disabled', false);
                self.feedCommentForm.find('input[type="submit"]').attr('disabled', false).val('Post message');
                self.feedCommentForm.find('.attached-link-delete').show();
                self.ajaxInProgress = false;
            },
            dataType: 'json'
        });

    },

    setFeedItemFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedCommentForm.find('.attach-links').hide();
        this.feedCommentForm.find('input.comment-type').val(link.parent().data('linkType'));
        this.feedCommentForm.find('.attached-link').slideDown();
        switch (link.parent().data('linkType')) {
            case 'video': {
                this.feedCommentForm.find('.attached-link-field').attr('placeholder', 'Insert link to YouTube or Vimeo video page...');
                break;
            }
            case 'website': {
                this.feedCommentForm.find('.attached-link-field').attr('placeholder', 'Insert link to website...');
                break;
            }
            case 'image': {
                this.feedCommentForm.find('.attached-link-field').attr('placeholder', 'Insert link to image...');
                break;
            }
            default: {
                this.feedCommentForm.find('.attached-link-field').attr('placeholder', '');
                break;
            }
        }
    },

    updateFeed: function() {
        var self = this;
        if (!self.ajaxInProgress && self.errorHasAppeared < 2) {
            $.ajax({
                dataType: 'json',
                url: self.feedCommentsDiv.data('checkoutUrl'),
                data: {
                    lastCommentId: self.feedCommentsDiv.data('lastCommentId')
                },
                success: function(response) {
                    if (response.success) {
                        self.feedCommentsDiv.data('lastCommentId', response['lastCommentId']);
                        var commentBlock = $(response['content']);
                        commentBlock.css('display', 'none');
                        if (self.feedCommentsDiv.find('.comment:last').length > 0) {
                            self.feedCommentsDiv.find('.comment:last').after(commentBlock);
                        } else {
                            self.feedCommentsDiv.find('.empty').remove().end().append(commentBlock);
                        }
                        commentBlock.slideDown();
                    }
                },
                error: function() { self.errorHasAppeared++; }
            });
        }

        var currentTime = moment().add('seconds', self.timeOffset);
        var timestamps = self.feedCommentsDiv.find('span.timestamp');
        $.each(timestamps, function(index, value) {
            var date = moment($(value).data('date'), 'YYYY-MM-DD HH:mm:ss');
            var humanDate = date.from(currentTime);
            $(value).html(humanDate);
        });
    },

    resetMainFormType: function(event) {
        event.preventDefault();
        var link = $(event.target);
        this.feedCommentForm.find('.attached-link').slideUp();
        this.feedCommentForm.find('input.comment-type').val('text');
        this.feedCommentForm.find('.attach-links').show();
        this.feedCommentForm.find('.attached-link-field').val('');
    },
    addCommentBlock: function(response) {
        var commentBlock = $(response);
        commentBlock.css('display', 'none');
        if (this.feedCommentsDiv.find('.comment:last').length > 0) {
            this.feedCommentsDiv.find('.comment:last').after(commentBlock);
        } else {
            this.feedCommentsDiv.find('.empty').remove().end().append(commentBlock);
        }
        commentBlock.slideDown();
        this.feedCommentFormDiv.find('textarea').val('');
        this.feedCommentFormDiv.find('.attached-link-delete a').click();

    },
    // @todo: Implement showing "empty comments" message if delete last comment
    deleteCommentBlock: function(event) {
        event.preventDefault();
        var link = $(event.target);
        if (window.confirm('Are you sure to delete comment?')) {
            var url = link.attr('href');
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    link.parents('.comment').slideUp('fast', function() {
                        link.parents('.comment').remove();
                    });
                },
                error: function() {alert('Oops, seems like unknown error has appeared!') }
            })
        }
    },
    _: ''
};

$.registry('zerebralAssignmentDetailFeedBlock', ZerebralAssignmentDetailFeedBlock, {
    methods: ['init']
});