$(document).ready(function(){
    $('textarea').wysihtml5({stylesheets: [
        '/css/wysiwyg-custom.css'
    ]});
    $('.optional-model').optionalModel();

    $('.icon-new-clock').timepicker({
        defaultTime: 'value'
    });

    $('.icon-new-calendar').datepicker();

    var collectionHolder = $('#schedule-fields-list');
    collectionHolder.find('.form-inline').each(function() {
        addScheduleDayDeleteLink($(this));
    });

    $('#add-another-schedule').click(function() {

        var newWidget = collectionHolder.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, (collectionHolder.find('.form-inline').length));

        var newLi = jQuery('<div></div>').html(newWidget);
        addScheduleDayDeleteLink(newLi.find('.form-inline'));
        newLi.find('.icon-new-clock').timepicker({
            defaultTime: 'value'
        });
        newLi.appendTo(collectionHolder);

        return false;
    });
});

var addScheduleDayDeleteLink = function($tagFormLi) {
    var $removeFormA = $('<a href="#" class="delete"><i class="icon-small-trash-bin"></i>Delete</a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $tagFormLi.remove();
    });
}