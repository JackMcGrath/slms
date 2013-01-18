$(document).ready(function(){
    $('textarea').wysihtml5();

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

	$('#message_toName').typeahead({
		minLength: 3,
		source: function(query, process) {
			$.ajax({
				url: '/messages/suggest-user/',
				type: "get",
				data: {
					username: $('#message_toName').val()
				},
				success: function(response) {
					return process(response.users);
				}
			});
		}
	});
});
