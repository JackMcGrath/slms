$(document).ready(function(){
    $('.pick-date .date').datepicker().on('changeDate', function(e) {
        location.href = '?date=' + (e.date.getTime() - (e.date.getTimezoneOffset() * 60000))/ 1000;
    });

    $('th.status input[type="radio"]').change(function(e) {
        $('td input[type="radio"]').removeAttr('checked');
        $('td[statusName="'+$(e.target).val()+'"] input[type="radio"]').attr('checked', 'checked');
    });

    $('td.status input[type="radio"]').change(function(e) {
        var statusName = $(e.target).parents('td').attr('statusName');
        var studentsCount = $('table tr.student').length;
        if ($('td[statusName="' + statusName + '"] input:checked').length == studentsCount) {
            $('th[statusName="' + statusName + '"] input[type="radio"]').attr('checked', 'checked');
        } else {
            $('th input[type="radio"]').removeAttr('checked');
        }
    });
});