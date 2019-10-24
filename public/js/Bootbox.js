export default class Bootbox {
    static confirm(text, a) {
        let url = $(a).attr('href');
        bootbox.confirm({
            message: text,
            centerVertical: true,
            buttons: {
                confirm: {
                    label: 'OK',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Anuluj',
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if (result === true) {
                    $.ajax({
                        url: url,
                        success:  setTimeout( function () {
                            location.reload();
                        }, 100)
                    });
                }
            }
        });
    }
}