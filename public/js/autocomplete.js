$(document).ready(function () {
    let options = {
        url: location.protocol + '//' + location.host + '/video/titles',
        getValue: "title",
        list: {
            match: {
                enabled: true
            }
        },
        theme: "square"
    };

    $("input[name='search']").easyAutocomplete(options);
});