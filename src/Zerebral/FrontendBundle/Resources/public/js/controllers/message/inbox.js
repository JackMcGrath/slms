$(document).ready(function(){
	$('a.select-all').click(function(e){
		e.preventDefault();
		checkAll();
	});
	$('a.deselect-all').click(function(e){
		e.preventDefault();
		uncheckAll();
	});

	$('input.select-all').change(function(e){
		if (this.checked) {
			if ($('.message-check:checked').length > 0) {
				e.preventDefault();
			}
			checkAll();
		} else {
			uncheckAll();
		}
	})

	checkAll = function() {
		$('.message-check').prop('checked', true);
		$('input.select-all').prop('checked', true);
	}

	uncheckAll = function() {
		$('.message-check').prop('checked', false);
		$('input.select-all').prop('checked', false);
	}
});
