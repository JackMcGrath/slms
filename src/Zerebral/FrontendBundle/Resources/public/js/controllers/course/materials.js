function Controller() {
}

Controller.prototype = {
    delayed:true,
    options:undefined,
    foldersWidget: undefined,


    init:function (options) {
        this.options = options || {};
        this.foldersWidget = new FoldersWidget(this, $('.right-navbar .block.folders'));
        this.upload = new UploadForm(this, $('#uploadMaterialModal'));
        this.sorting = new Sorting(this, $('.right-navbar .sorting'));
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
        $('.top-buttons .create-folder').bind('click', $.proxy(this.onEdit, this))

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

        var form = $('#editFolderModal form');
        form.find('.control-group').removeClass('error');
        form.find('.control-group .help-inline').remove();
        // if rename folder
        if ($target.length) {
            form.attr('action', form.data('action-edit') + '/' + $target.attr('folderId'));
            $('#folder_name').val($target.attr('folderName'));
            form.find('h3').text('Rename folder');
        } else {
            form.attr('action', form.data('action-add'));
            form.find('h3').text('Create new folder');
        }

    }
};

var UploadForm = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

UploadForm.prototype = {

    bind:function () {
        $('.upload-material').zerebralAjaxForm();
        $('.optional-model').optionalModel();

        $('.uploadedFiles').collectionFormType({
            add: '#addUploadFile',
            remove: '.remove-uploaded-file',
            item: '.file-item',
            template: '#new_material_form'
        });
    }
};

var Sorting = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

Sorting.prototype = {
    bind:function () {
        var self = this;
        this.target.find('li input[type="radio"]').bind('change', $.proxy(this.onChangeSorting, this));
    },

    onChangeSorting: function(e) {
        this.target.find('form').submit();
    }
};