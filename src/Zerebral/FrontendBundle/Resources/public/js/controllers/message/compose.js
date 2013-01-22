$(document).ready(function() {
    $('textarea').wysihtml5();
    var timer;

    $('#upload_files_div').collectionFormType({
        add: '.add_file_link',
        remove: '.remove-uploaded-file',
        item: '.file-item',
        template: '#new_file_form'
    });

    $('#message_toName').typeahead({
        minLength: 1,
        source: function(query, process) {
            clearTimeout(timer);
            timer = setTimeout(function() {
                getSuggest(process);
            }, 300);

        }
    });

    var getSuggest = function(process) {
        $.ajax({
            url: '/user/suggest',
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
