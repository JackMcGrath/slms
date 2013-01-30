$(document).ready(function() {
//    $('textarea').wysihtml5();
//    var timer;
//
//    $('#upload_files_div').collectionFormType({
//        add: '.add_file_link',
//        remove: '.remove-uploaded-file',
//        item: '.file-item',
//        template: '#new_file_form'
//    });
//
//	var select = $('#message_to');
//
//	if (select.find('option').length && !select.val()) {
//		select.empty();
//	} else if (select.val()) {
//		$.each(select.find('option'), function(i, option){
//			if ($(option).attr('value') != $('#message_to').val()) {
//				$(option).remove();
//			}
//		});
//	}
//
//	select.ajaxChosen({
//		type: 'GET',
//		url: '/user/suggest',
//		dataType: 'json'
//	}, function (data) {
//		var results = [];
//
//		$.each(data.users, function (i, user) {
//			results.push({ value: user.id, text: user.name });
//		});
//
//		return results;
//	});
//
//    var getSuggest = function(process) {
//        $.ajax({
//            url: '/user/suggest',
//            type: "get",
//            data: {
//                username: $('#message_toName').val()
//            },
//            success: function(response) {
//                return process(response.users);
//            }
//        });
//    }
});
