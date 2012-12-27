function Controller() {
}

Controller.prototype = {
    delayed:false,
    options:undefined,
    foldersWidget: undefined,


    init:function (options) {
        this.options = options || {};
        this.foldersWidget = new FoldersWidget(this, $('.right-navbar .block.folders'));
    }
};

var FoldersWidget = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

FoldersWidget.prototype = {
    bind:function () {
        var self = this;
        this.target.find('li').hover(function(e) {
            $(e.target).closest('li').find('.manage-buttons').css('visibility', 'visible').prev().hide();
        },function(e) {
            $(e.target).closest('li').find('.manage-buttons').css('visibility', 'hidden').prev().show();
        });

        this.target.find('a.delete').live('click', $.proxy(this.onDelete, this));
        this.target.find('a.edit').live('click', $.proxy(this.onEdit, this));

        $('.folder-form').zerebralAjaxForm();
    },

    onDelete: function() {
        return confirm('Do you really want to delete folder and all files in this folder?');
    },

    onEdit: function(e) {
        e.preventDefault();
        var self = this;
        $('#editFolderModal').modal('show');
        self.onOpenForm($(e.target).closest('li'));
    },

    onOpenForm: function($target) {
        $('#folder_id').val($target.attr('folderId'));
        $('#folder_course_id').val($target.attr('courseId'));
        $('#folder_name').val($target.attr('folderName'));
    }
};