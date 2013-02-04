$(document).ready(function() {
    var oTable = $('.attendance-table table').dataTable( {
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

});