$(document).ready(function(){
	$('input[name=delete]').click(function(e){
		if ($('.message-check:checked').length > 0) {
			if (window.confirm('Are you sure you want to delete selected messages?')) {
				$(this).closest('form').submit();
			} else {
				e.preventDefault();
			}
		}
	});

	$('input[type=submit]').click(function(e){
		if ($('.message-check:checked').length == 0) {
			e.preventDefault();
		}
	});

	$('input.select-all').change(function(e){
		if (this.checked) {
			checkAll();
		} else {
			if ($('.message-check:checked').length > 0 && $('.message-check').length > $('.message-check:checked').length) {
				checkAll();
				$('input.select-all').prop('checked', true);
			} else {
				uncheckAll();
			}
		}
		$('input.select-all').css('opacity', 1);
	})

	$('input.message-check').change(function(){
		if ($('.message-check:checked').length > 0) {
			$('input.select-all').prop('checked', true);

			if ($('.message-check:checked').length == $('.message-check').length) {
				$('input.select-all').css('opacity', 1);
			} else {
				$('input.select-all').css('opacity', 0.5);
			}
		} else {
			$('input.select-all').prop('checked', false);
			$('input.select-all').css('opacity', 1);
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
