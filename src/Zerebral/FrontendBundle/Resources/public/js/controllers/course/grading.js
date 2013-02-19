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
    }
};

var GradingPopup = function (container, target) {
    this.container = container;
    this.target = target;
    this.sliderSelector = '.grade-slider .slider';
    this.maxGrade = 100;
    this.assignment = undefined;
    this.bind();
};

GradingPopup.prototype = {
    bind:function () {
        var self = this;

        this.container.table.find('a[data-toggle="modal"]').live('click', $.proxy(this.onShowPopup, this));
        this.target.find('.grade-pass button').live('click', $.proxy(this.onChangePassValue, this));
        this.container.gradingPopupSelector.find('input.grade-value').live('change', $.proxy(this.onChangeGradeNumber, this));
        this.container.gradingPopupSelector.find('input.grade-value').live('keypress', $.proxy(this.onTypoGradePress, this));
//        this.container.gradingPopupSelector.find('input.grade-value').live('keyup', $.proxy(this.onTypoGradeUp, this));

        //this.container.table.find('a[data-toggle="modal"]').live('click', function(e) { alert('click');self.studentAssignment = $(e.target).closest('td').attr('studentAssignment'); console.log($(e.target).closest('td').attr('studentAssignment')); })

        this.container.gradingPopupSelector.on('show', function(e) {
            self.renderPopup(self.studentAssignmentId, self, this);
        });
        this.container.gradingPopupSelector.on('hide', function(e) {
            var grade = self.container.table.find('td[studentAssignment="' + self.studentAssignmentId + '"] .grade-value');
            var value = grade.attr('value');
            self.container.gradingPopupSelector.find('.next-prev').html('');
            this.assignment = undefined;
            self.updateGradeInTable(value, grade);
        });

        $('.grading-form').zerebralAjaxForm({
            onSuccess: function(response) {
                if (response.success) {
                    var assignmentTd = self.container.table.find('td[studentAssignment="' + response.content.Id + '"]');
                    if (response.content.Grading == null) {
                        assignmentTd.addClass('new').html('<a href="#" data-toggle="modal" data-toggle="modal"><i class="icon-new-plus-add"></i></a>');
                    } else if (!assignmentTd.find('.grade-level').length) {
                        assignmentTd.html('<div class="grade-value"></div><div class="edit hide"><a href="" class="" data-toggle="modal">Edit</a></div>');
                    }
                    assignmentTd.find('.grade-value').attr('value', response.content.Grading);
                    if (typeof(response.nextPrev) == 'object' && response.nextPrev.nextId) {
                        self.renderPopup(response.nextPrev.nextId, self, self.container.gradingPopupSelector);
                        self.updateGradeInTable(response.content.Grading, assignmentTd.find('.grade-value'));
                    } else {
                        self.container.gradingPopupSelector.modal('hide');
                    }

                }
            }
        });
    },

    updateGradeInTable: function(value, grade) {
        if (value != null && typeof(value) != 'undefined') {
            if (grade.closest('td').hasClass('pass')) {
                if (value == 1) {
                    grade.removeClass('hover').html('<i class="icon-new-passed"></i>');
                } else {
                    grade.removeClass('hover').html('<i class="icon-new-fail"></i>');
                }
            } else {
                grade.removeClass('hover').html(value);
            }
        }
    },

    renderPopup: function(studentAssignmentId, self, $this) {
        self.nextPrevUnbind();
        var modalBody = $($this).find('.modal-body');
        modalBody.html('<p>Loading...</p>');

        var gradingForm = $($this).find('.grading-form');
        gradingForm.attr('action', gradingForm.attr('edit-action') + '/' + studentAssignmentId);

        $.ajax({
            url: '/grading/student-assignment/' + studentAssignmentId,
            dataType: 'json',
            type: 'GET',
            success: function(response) {
                if (!response.has_errors) {
                    modalBody.html(response.content);
                    self.container.gradingPopupSelector.find('.next-prev').html(self.generateNextPrevHtml(response.nextPrev));
                    self.assignment = response.assignment;
                    self.sliderBind();
                    self.nextPrevBind();

                    if (typeof(response.nextPrev) == 'object' && response.nextPrev.nextId == null) {
                        self.container.gradingPopupSelector.find('.modal-footer button.continue').attr('disabled', 'disabled');
                    } else {
                        self.container.gradingPopupSelector.find('.modal-footer button.continue').removeAttr('disabled');
                    }
                }
            }
        });
    },

    nextPrevBind: function() {
        var self = this;
        var links = $(this.container.gradingPopupSelector).find('.next-prev');
        links.find('.prev, .next').live('click', function(e) {
            e.preventDefault();

            self.renderPopup($(e.target).attr('studentAssignmentId'), self, self.container.gradingPopupSelector);

        })
    },

    nextPrevUnbind: function() {
        var self = this;
        var links = $(this.container.gradingPopupSelector).find('.next-prev');
        links.find('.prev, .next').die('click');
    },

    onShowPopup: function(e) {
        this.studentAssignmentId = $(e.target).closest('td').attr('studentAssignment');
        this.container.gradingPopupSelector.modal('show');
    },

    sliderBind: function() {
        var self = this;
        var gradeValue = $('input.grade-value').val();

        $(this.sliderSelector).slider({
            max: self.maxGrade,
            step: 1,
            value: gradeValue ? gradeValue : 0,
            range: "min",
            change: function(e, ui) {
                $('input.grade-value').val(ui.value);
            },
            slide: function(e, ui) {
                self.changeSliderColor(ui.value);
                $('input.grade-value').val(ui.value);
            }
        });
        self.changeSliderColor(gradeValue); //event start in ui-slider do not have initial value
    },

    changeSliderColor: function(value) {
        if (this.assignment.Threshold <= value) {
            this.target.find('.slider .ui-slider-range').css('background-color', '#dff0d8');
        } else {
            this.target.find('.slider .ui-slider-range').css('background-color', '#f2dede');
        }
    },

    onChangeGradeNumber: function(e) {
        var value = $(e.target).val();
        this.changeGradeNumber(value);
        this.changeSliderColor(value)
    },

    changeGradeNumber: function(value) {
        $(this.sliderSelector).slider({
            value: value
        })
    },

    onTypoGradePress: function(e) {
        var charCode = (e.which) ? e.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    },

    onChangePassValue: function(e) {
        var button = $(e.target);
        var value = button.attr('value');

        button.parents('.grade-pass').find('button').removeClass('btn-danger').removeClass('btn-success');
        button.addClass(value == 1 ? 'btn-success' : 'btn-danger');

        this.target.find('.grade-pass-value').val(value);
    },

    generateNextPrevHtml: function(nextPrevData) {
        var prevLink = nextPrevData.prevId ? '<a href="#" class="prev" studentAssignmentId="' + nextPrevData.prevId + '"><span>&#706;&#706;</span> Prev</a> ' : '';
        var nextLink = nextPrevData.nextId ? ' <a href="#" class="next" studentAssignmentId="' + nextPrevData.nextId + '">Next <span>&#707;&#707;</span></a>' : '';
        var html = '<div class="prev-row">' + prevLink + '</div><b>' + nextPrevData.currentNumber + '</b>' + ' of ' + nextPrevData.totalCount + '<div class="next-row">' + nextLink + '</div>';
        return html;
    }
};