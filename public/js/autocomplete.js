$(document).ready(function () {
    let titles = getTitles();

    $("input[name='search']").easyAutocomplete(titles);

    $("input[name='search']").change(function () {
        titles = getTitles();
    });
});

function getTitles() {
    options = $.ajax({
        method: "get",
        url: '/video/titles',
        dataType: 'json',
    });

    return options;
}