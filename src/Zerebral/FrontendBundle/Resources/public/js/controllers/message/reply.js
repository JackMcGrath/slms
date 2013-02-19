$(document).ready(function(){
    $('textarea').wysihtml5({stylesheets: []});

	$('#show-hidden').click(function(e) {
		e.preventDefault();
		$('.message').show();
		$(this).closest('div').remove();
	});

	$('#upload_files_div').collectionFormType({
		add: '.add_file_link',
		remove: '.remove-uploaded-file',
		item: '.file-item',
		template: '#new_file_form'
	});
});
