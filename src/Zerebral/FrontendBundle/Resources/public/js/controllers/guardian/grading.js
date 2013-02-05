$(document).ready(function() {
    var oTable = $('.grading-table table').dataTable( {
        "sScrollY": "100%",
        "sScrollX": "100%",

        "bScrollCollapse": false,
        "bPaginate": false,
        "bSort": false,
        "bFilter": false,
        "bInfo" : false,
        "bAutoWidth" : false
    } );
    new FixedColumns( oTable , {
        "sLeftWidth": 'relative',
        "iLeftWidth": 24
    });

    $('.grading-table .value .hint').popover({
        html: true,
        placement: 'bottom',
        trigger: 'hover'
    });

});