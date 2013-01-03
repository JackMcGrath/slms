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
    selectedFolder: '',
    fileFieldIndex: 0,

    bind:function () {
        var self = this;

        this.target.find('#addUploadFile').bind('click', $.proxy(this.onAddFile, this));
        this.target.find('.upload-folder select').bind('change', $.proxy(this.onChangeFolder, this));
        this.target.find('li a.remove').live('click', $.proxy(this.onRemoveFile, this));

        this.target.on('show', function(e) {
            $(this).find('ul.uploadedFiles li').remove();
            self.fileFieldIndex = 0;
            self.onAddFile();
        });

        $('.upload-material').zerebralAjaxForm();
        $('.optional-model').optionalModel();
    },

    onAddFile: function() {
        var ul = this.target.find('.uploadedFiles');

        var newLi = $('<li class="control-group"></li>');
        var newFileInput = $('<input type="file" name="course_materials[courseMaterials][' + this.fileFieldIndex + '][file][uploadedFile]" />');
        var newDescInput = $('<input type="text" name="course_materials[courseMaterials][' + this.fileFieldIndex + '][description]" placeholder="Description (optional)" />');
        var courseIdInput = $('<input type="hidden" name="course_materials[courseMaterials][' + this.fileFieldIndex + '][courseId]" value="' + this.container.options.courseId + '" />');
        //var folderIdInput = $('<input type="hidden" class="upload-folder-id" name="course_materials[courseMaterials][' + this.fileFieldIndex + '][folderId]" value="' + this.selectedFolder + '" />');
        newLi.append(newFileInput).append(newDescInput).append(courseIdInput);
        if (this.fileFieldIndex == 0) {
            ul.prepend(newLi);
        } else {
            ul.find('li').last().after(newLi);
        }
        this.fileFieldIndex ++;
        this.showRemoveButton();
    },

    onRemoveFile: function(e) {
        e.preventDefault();
        $(e.target).parents('li').remove();
        this.fileFieldIndex --;
        this.showRemoveButton();

    },

    onChangeFolder: function(e) {
        var folderId = $(e.target).val();
        this.selectedFolder = folderId;
        this.target.find('.upload-folder-id').val(this.selectedFolder);
    },

    showRemoveButton: function() {
        this.target.find('.uploadedFiles li .remove').remove();
        if (this.fileFieldIndex > 1) {
            this.target.find('.uploadedFiles li').prepend('<a class="close remove" href="#">&times;</a>');
        }
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