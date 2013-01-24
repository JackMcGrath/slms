/*
 * USING
 *
 * $('#checkbox-that-check-all').checkAll('.class-of-checkboxes-need-to-check');
 */

var checkAll = function(element, options) {
	this.elementCheckAll = element;
	this.elementCheckSelector = options.checkboxClass;
	this.controlButtons = options.controlButtons;
}

checkAll.prototype = {
	elementCheckAll: undefined,
	elementCheck: undefined,
	elementCheckSelector: undefined,
	controlButtons: undefined,

	init: function() {
		var self = this;
		self.elementCheck = $(self.elementCheckSelector);

		self.elementCheckAll.change(function(){
			self.checkAllToggle(this);
		});

		self.elementCheck.change(function(){
			self.checkToggle();
		});
	},

	checkAll: function() {
		this.elementCheck.prop('checked', true);
		this.elementCheckAll.prop('checked', true);
	},

	uncheckAll: function() {
		this.elementCheck.prop('checked', false);
		this.elementCheckAll.prop('checked', false);
	},

	checkAllToggle: function(element) {
		if (element.checked) {
			this.checkAll();
			$(this.controlButtons).enable();
		} else {
			if ($(this.elementCheckSelector + ':checked').length > 0 && this.elementCheck.length > $(this.elementCheckSelector + ':checked').length) {
				this.checkAll();
				this.elementCheckAll.prop('checked', true);
				$(this.controlButtons).enable();
			} else {
				this.uncheckAll();
				$(this.controlButtons).prop('disabled', 'disabled');
			}
		}
		this.elementCheckAll.css('opacity', 1);
	},

	checkToggle: function() {
		if ($(this.elementCheckSelector + ':checked').length > 0) {
			this.elementCheckAll.prop('checked', true);
			$(this.controlButtons).enable();

			if ($(this.elementCheckSelector + ':checked').length == this.elementCheck.length) {
				this.elementCheckAll.css('opacity', 1);
			} else {
				this.elementCheckAll.css('opacity', 0.5);
			}
		} else {
			this.elementCheckAll.prop('checked', false);
			this.elementCheckAll.css('opacity', 1);
			$(this.controlButtons).prop('disabled', 'disabled');
		}
	}
}

$.registry('checkAll', checkAll, {
	methods: ['init']
});