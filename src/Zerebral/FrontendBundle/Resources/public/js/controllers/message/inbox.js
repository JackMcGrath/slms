$(document).ready(function(){
	$('input[name=delete]').click(function(e){
		if ($('.message-check:checked').length > 0) {
			if (window.confirm('Are you sure you want to delete selected messages?')) {

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

	$('input.select-all').checkAll({checkboxClass: 'input.message-check'});
});
