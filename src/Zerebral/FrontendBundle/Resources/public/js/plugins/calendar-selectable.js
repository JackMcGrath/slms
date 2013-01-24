/*
 * @todo set real start/end date
 */

var calendarSelectable = function(element, options) {
	this.calendar = element;
}

calendarSelectable.prototype = {
	calendar: undefined,
	startDate: undefined,
	endDate: undefined,

	init: function() {
		this.bind();
		this.startDate = '2013-01-20';
		this.endDate = '2013-02-20';

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
				self.mark();
				// @todo save;
			} else {
				// select new interval. set startDate
				self.endDate = undefined;
				self.startDate = $(this).attr('date');
				$(this).closest('td').addClass('selected');
			}
		});

		this.calendar.find('p.reset a').click(function(e){
			e.preventDefault();
			self.unmark();
			// @todo save;
		})
	},

	unmark: function() {
		this.calendar.find('.calendar-day').removeClass('selected');
		this.calendar.find('.calendar-day').removeClass('selected-interval');
		this.calendar.find('.calendar-day').removeClass('selected-day');
		this.calendar.find('p.reset').hide();
	},

	mark: function() {
		this.unmark();

		if (this.startDate > this.endDate) {
			var startDate = this.endDate;
			this.endDate = this.startDate;
			this.startDate = startDate;
		}

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
	}
}

$.registry('calendarSelectable', calendarSelectable, {
	methods: ['init']
});