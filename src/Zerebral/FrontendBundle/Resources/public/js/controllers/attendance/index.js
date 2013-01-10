$(document).ready(function(){
    $('.pick-date .date').datepicker().on('changeDate', function(e) {
        location.href = '?date=' + (e.date.getTime() - (e.date.getTimezoneOffset() * 60000))/ 1000;
    });
});