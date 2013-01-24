var calendarSelectable = function(element, options) {
	this.calendar = element;

	this.itemList = options.itemList;
	this.calculateAssigmentsCount = options.calculateAssigmentsCount ? options.calculateAssigmentsCount : false;
}

calendarSelectable.prototype = {
	calendar: undefined,
	startDate: undefined,
	endDate: undefined,
	itemList: undefined,
	calculateAssigmentsCount: false,

	init: function() {
		this.bind();

		if (this.startDate && this.endDate) {
			this.mark();
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
		})
	},

	apply: function() {
		var self = this;
		if (self.startDate && self.endDate) {
			var items = $(this.itemList).find('.list-item ');
			items.hide();
			$.each(items, function(i, item) {
				if ($(item).attr('due-date') !== undefined) {
					var assigmentDates = $(item).attr('due-date').split(',');
					var assigmentsCount = 0;
					$.each(assigmentDates, function(i,date) {
						if (date >= self.startDate && date <= self.endDate) {
							if (self.calculateAssigmentsCount) {
								assigmentsCount++;
								$(item).find('.stat .assigments-count').html(assigmentsCount);
							}
							$(item).show();
						}
					});

				}
			});

		} else {
			$(this.itemList).find('.list-item ').show();
			if (self.calculateAssigmentsCount) {
				var badges = $(this.itemList).find('.list-item ').find('.stat .assigments-count');
				$.each(badges, function(i, badge) {
					$(badge).html($(badge).attr('default-count'));
				});
			}

		}
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