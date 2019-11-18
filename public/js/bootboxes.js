import Bootbox from "./Bootbox.js";

$(document).ready(function () {
    $('.bootbox-confirm-button').click(function (e) {
        e.preventDefault();
        Bootbox.confirm($(this).data('title'), this);
    });
    $('.bootbox-dialog-button').click(function (e) {
        e.preventDefault();
        Bootbox.dialog($(this).data('title'), this);
    });
});

