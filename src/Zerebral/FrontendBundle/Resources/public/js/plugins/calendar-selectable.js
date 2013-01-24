var calendarSelectable = function(element, options) {
	this.calendar = element;

	this.itemList = options.itemList;
	this.calculateAssignmentsCount = options.calculateAssignmentsCount ? options.calculateAssignmentsCount : false;

	this.startDate = options.startDate ? options.startDate : undefined;
	this.endDate = options.endDate ? options.endDate : undefined;
}

calendarSelectable.prototype = {
	calendar: undefined,
	startDate: undefined,
	endDate: undefined,
	itemList: undefined,
	calculateAssignmentsCount: false,

	init: function() {
		this.bind();

		if (this.startDate && this.endDate) {
			this.mark();
			this.apply();
		}
	},

	bind: function() {
		var self = this;
		this.calendar.find('.calendar-day a').click(function(e){
			e.preventDefault();

			if (self.startDate && !self.endDate) {
				// select endDate
				self.endDate = $(this).attr('date');
				self.rightInterval();
				self.mark();
				self.apply();
				self.saveToSession();
			} else {
				// select new interval. set startDate
				self.endDate = undefined;
				self.startDate = $(this).attr('date');
				$(this).closest('td').addClass('selected');
			}
		});

		this.calendar.find('p.reset a').click(function(e){
			e.preventDefault();
			self.startDate = undefined;
			self.endDate = undefined;
			self.unmark();
			self.apply();
			self.saveToSession();
		})
	},

	apply: function() {
		var self = this;
		if (self.startDate && self.endDate) {
			var items = $(this.itemList).find('.list-item ');
			items.hide();
			$.each(items, function(i, item) {
				if ($(item).attr('due-date') !== undefined) {
					var assignmentDates = $(item).attr('due-date').split(',');
					var assignmentsCount = 0;
					$.each(assignmentDates, function(i,date) {
						if (date >= self.startDate && date <= self.endDate) {
							if (self.calculateAssignmentsCount) {
								assignmentsCount++;
								$(item).find('.stat .assignments-count').html(assignmentsCount);
							}
							$(item).show();
						}
					});

				}
			});

		} else {
			$(this.itemList).find('.list-item ').show();
			if (self.calculateAssignmentsCount) {
				var badges = $(this.itemList).find('.list-item ').find('.stat .assignments-count');
				$.each(badges, function(i, badge) {
					$(badge).html($(badge).attr('default-count'));
				});
			}

		}
	},

	saveToSession: function() {
		var self = this;
		$.ajax({
			url: '/assignments/date-filter/set',
			dataType: 'json',
			data: {
				start_date: self.startDate,
				end_date: self.endDate
			},
			type: 'GET',
			success: function() {}
		});
	},

	unmark: function() {
		this.calendar.find('.calendar-day').removeClass('selected');
		this.calendar.find('.calendar-day').removeClass('selected-interval');
		this.calendar.find('.calendar-day').removeClass('selected-day');
		this.calendar.find('p.reset').hide();
	},

	mark: function() {
		this.unmark();
		this.rightInterval();

		if (this.startDate == this.endDate) {
			this.markSingleDay();
		} else {
			this.markInterval();
		}
		this.calendar.find('p.reset').show();
	},

	markSingleDay: function() {
		this.calendar.find('a[date=' + this.startDate + ']').closest('td').addClass('selected-day');
	},

	markInterval: function() {
		var self = this;
		var days = this.calendar.find('td.calendar-day');
		$.each(days, function(i, day){
			var dateLink = $(day).find('a');
			if (dateLink.attr('date') >= self.startDate && dateLink.attr('date') <= self.endDate) {
				$(day).addClass('selected-interval');
			}
		});
	},

	rightInterval: function() {
		if (this.startDate > this.endDate) {
			var startDate = this.endDate;
			this.endDate = this.startDate;
			this.startDate = startDate;
		}
	}
}

$.registry('calendarSelectable', calendarSelectable, {
	methods: ['init']
});