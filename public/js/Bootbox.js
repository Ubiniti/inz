export default class Bootbox {
    static confirm(text, a) {
        let url = $(a).attr('href');
        bootbox.confirm({
            message: text,
            centerVertical: true,
            buttons: {
                cancel: {
                    label: 'Anuluj',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: 'OK',
                    className: 'btn-success'
                },
            },
            callback: function (result) {
                if (result === true) {
                    window.location.replace(url)
                }
            }
        });
    }

    static dialog(text,a) {
        let url = $(a).attr('href');
        let secondaryUrl = $(a).data("secondaryurl");
        bootbox.dialog({
            message: text,
            centerVertical: true,
            buttons: {
                confirm: {
                    label: 'OK',
                    className: 'btn-success',
                    callback: function(){
                        window.location.replace(url);
                    }
                },
                secondary: {
                    label: 'Demo filmu',
                    className: 'btn-primary',
                    callback: function(){
                        window.location.replace(secondaryUrl);
                    }
                },
                cancel: {
                    label: 'Anuluj',
                    className: 'btn-secondary',
                    callback: function(){
                        return false;
                    }
                }
            }
        });
    }

}