$(document).ready(function() {
    var assignments = $('#test');

    var filterAssignments = function(criteria) {
        if (criteria == 'all') {
            assignments.find('div').slideDown();
        } else {
            var collectionToShow = assignments.find('div.list-item[data-due=' + criteria + ']');
            var collectionToHide = assignments.find('div.list-item').not('[data-due=' + criteria + ']');

            collectionToShow.slideDown();
            collectionToHide.slideUp();
        }
    };

    $('.pull-left input').click(function() {
        filterAssignments($(this).val());
    });
});