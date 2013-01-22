function Controller() {
}

Controller.prototype = {
    delayed:false,
    options:undefined,
    gradingPopup: undefined,
    table: undefined,

    init:function (options) {
        this.options = options || {};
        this.table = $('table.grading');
        this.gradingPopupSelector = $('#gradingModal');
        this.gradingPopup = new GradingPopup(this, this.gradingPopupSelector);

        this.bindScrolledTable();

        this.bindHover();
    },

    bindScrolledTable: function() {
        var oTable = $('table.grading').dataTable( {
            "sScrollY": "100%",
            "sScrollX": "100%",

            "bScrollCollapse": false,
            "bPaginate": false,
            "bSort": false,
            "bFilter": false,
            "bInfo" : false,
            "bAutoWidth" : false

        } );
        new FixedColumns( oTable , {
            "sLeftWidth": 'relative',
            "iLeftWidth": 20
        });
    },

    bindHover: function() {
        $('.grade-value').hover(function(e) {
            $(e.target).addClass('hover')
                .html('<a href="" class="" data-toggle="modal">Edit</a>');
        }, function(e) {
            $(e.target).removeClass('hover')
                .html($(e.target).attr('value'));
        });
    }
};

var GradingPopup = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

GradingPopup.prototype = {
    bind:function () {
        var self = this;
        this.container.table.find('a[data-toggle="modal"]').live('click', $.proxy(this.onShowPopup, this));

        //this.container.table.find('a[data-toggle="modal"]').live('click', function(e) { alert('click');self.studentAssignment = $(e.target).closest('td').attr('studentAssignment'); console.log($(e.target).closest('td').attr('studentAssignment')); })

        this.container.gradingPopupSelector.on('show', function(e) {
            var modalBody = $(this).find('.modal-body');
            modalBody.html('<p>Loading...</p>');

            var gradingForm = $(this).find('.grading-form');
            gradingForm.attr('action', gradingForm.attr('edit-action') + '/' + self.studentAssignmentId);

            $.ajax({
                url: '/grading/student-assignment/' + self.studentAssignmentId,
                dataType: 'json',
                type: 'GET',
                success: function(response) {
                    if (!response.has_errors) {
                        modalBody.html(response.content);
                    }
                }
            });
        });
        this.container.gradingPopupSelector.on('hide', function(e) {
            var grade = self.container.table.find('td[studentAssignment="' + self.studentAssignmentId + '"] .grade-value');
            var value = grade.attr('value');
            if (value != null && typeof(value) != 'undefined')
                grade.removeClass('hover').html(value);
        });

        $('.grading-form').zerebralAjaxForm({
            onSuccess: function(response) {
                if (response.success) {
                    var assignmentTd = self.container.table.find('td[studentAssignment="' + response.content.Id + '"]');
                    if (response.content.Grading == null) {
                        assignmentTd.addClass('new').html('<a href="#" data-toggle="modal" data-toggle="modal"><i class="icon-new-plus-add"></i></a>');
                    } else if (!assignmentTd.find('.grade-level').length) {
                        assignmentTd.html('<div class="grade-value"></div>');
                    }
                    assignmentTd.find('.grade-value').attr('value', response.content.Grading);
                    self.container.gradingPopupSelector.modal('hide');
                    self.container.bindHover();
                }
            }
        });
    },

    onShowPopup: function(e) {
        this.studentAssignmentId = $(e.target).closest('td').attr('studentAssignment');
        this.container.gradingPopupSelector.modal('show');
    }
};