$(document).ready(function () {
    let options = {
        data: ["blue", "green", "pink", "red", "yellow"]
    };

    $("input[name='search']").easyAutocomplete(options);

    $("input[name='search']").change(function () {
        options = $.ajax({
            method: "get",
            url: '/video/titles',
            dataType: 'json',
        })
    });
});