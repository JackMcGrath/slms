$(document).ready(function(){
    $('textarea').wysihtml5();

	$('#show-hidden').click(function(e) {
		e.preventDefault();
		$('.message').show();
		$(this).closest('div').remove();
	});

	// Dynamic files fields creating
	var addAttachmentField = function(e) {
		if (e) {
			e.preventDefault();
		}
		var prototype = $('div#attachments_div').attr('data-prototype');
		var newForm = $(prototype.replace(/__name__/g, $('div#upload_files_div div').length));
		newForm.find('input').attr('name', newForm.find('input').attr('name') + '[uploadedFile]');
		$('div#upload_files_div').append(newForm);
	}

	$('.add_file_link').on('click', addAttachmentField);


	$(document).on('click', '.remove-uploaded-file', function(e) {
		e.preventDefault();
		if (window.confirm('Are you sure to delete this attachment?')) {
			$(e.target).parents('div.file-item').remove();
		}
	});
});
