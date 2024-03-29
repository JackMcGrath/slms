function Controller() {
}

Controller.prototype = {
    delayed:false,
    options:undefined,
    studentsPopup: undefined,

    init:function (options) {
        this.options = options || {};
        this.studentsPopupSelector = $('#studentsModal');
        this.studentsPopup = new StudentsPopup(this, this.studentsPopupSelector);

        $('.icon-new-calendar').datepicker();
        $('.icon-new-clock').timepicker({
            defaultTime: 'value'
        });
        $('textarea').wysihtml5({stylesheets: ['/css/wysiwyg-custom.css']});
        $('.optional-model').optionalModel();

        this.bind();
    },

    bind: function() {
        this.switchThresholdByValue($('.grade-type').val());

        this.bindUploadFiles();
        $('.student_select_all').live('change', $.proxy(this.onSelectAll, this));
        $('select.grade-type').live('change', $.proxy(this.onChangeGradeType, this))
    },

    onSelectAll: function() {
        $('.student_select').attr('disabled', '1');
        $('.student-list input[type=checkbox]').each(function(index, element){
            $(element).attr('checked', 1);
        });
    },

    bindUploadFiles: function() {
        $('#upload_files_div').collectionFormType({
            add: '.add_file_link',
            remove: '.remove-uploaded-file',
            item: '.file-item',
            template: '#new_file_form'
        });
    },

    onChangeGradeType: function(e) {
        this.switchThresholdByValue($(e.target).val());
        $(e.target).parents('.form-inline').find('.help-inline').remove();
    },

    switchThresholdByValue: function(value) {
        if (value == 'numeric') {
            $('.threshold').parents('.control-group').show().find('input').removeAttr('disabled');
        } else {
            $('.threshold').parents('.control-group').hide().find('input').attr('disabled', 'disabled');
        }
    }
};

var StudentsPopup = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

StudentsPopup.prototype = {
    bind:function () {
        var self = this;

        this.target.find('.student-list tr').live('click', $.proxy(this.onClickUserRow, this));
        this.target.find('.save-assign').live('click', $.proxy(this.onSavePopup, this));
        //this.target.find('.toggle_selection input').live('change', $.proxy(this.onSelectUser, this));

        $('.toggle_selection input').checkAll({checkboxClass: '.student-list input[type="checkbox"]'});

        this.target.on('hide', function() {
            self.resetFormManual();
        })
    },

    onClickUserRow: function(e) {
        if ($(e.target).prop("tagName") != "INPUT"){
            var checkbox = $(e.target).closest('tr').find('input');
            if(checkbox.is(':checked')){
                checkbox.removeAttr('checked');
            }else{
                checkbox.attr('checked', 1);
            }
        }
    },

    onSavePopup: function(e) {
        e.preventDefault();
        $('.student_select').removeAttr('disabled').attr('checked', '1');
        var selectedItemsCount = $('.student-list input[type=checkbox]:checked').length;
        var allItemsCount = $('.student-list input[type=checkbox]').length;
        $('.student_select').parent().find('a').text(selectedItemsCount + '/' + allItemsCount + " students selected");

        $.each(this.target.find('.student-list').find('input'), function(index, el) {
            var $el = $(el);
            if ($el.attr('checked') == 'checked') {
                $el.addClass('checked');
            } else {
                $el.removeClass('checked');
            }
        });

        this.target.modal('hide');
    },

    resetFormManual: function() {
        $.each(this.target.find('.student-list').find('input'), function(index, el) {
            var $el = $(el);
            if ($el.hasClass('checked')) {
                $el.attr('checked', 'checked');
            } else {
                $el.removeAttr('checked');
            }
        });
    }
};
