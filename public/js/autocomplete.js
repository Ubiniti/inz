$(document).ready(function () {
    let options = {
        data: ["blue", "green", "pink", "red", "yellow"]
    };

    $("input[name='search']").easyAutocomplete(options);
});