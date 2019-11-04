(function () {
    $(document).ready(function () {
        var progressBar = $(upload_progress);
        var successRoute = $(uploadForm).data('success');

        function progressHandling(e) {
            var percent = e.loaded / e.total * 100;

            progressBar.css('width', percent + '%');
        }

        $(uploadForm).on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(uploadForm);
            formData.append('add_video[file]', add_video_file.files[0]);

            var formContainer = $('.form-container');
            formContainer.addClass('disabled');

            var spinner = '<div style="position:absolute; '
                +
                'width: '
                + formContainer.width() + 'px; '
                +
                'display:block;" class="d-flex justify-content-center text-center">' +
                '<div class="spinner-border" style="width: 15rem; height: 15rem;" " role="status">' +
                '<span class="sr-only">Trwa ładowanie filmu - może potrwać to do kilku minut. Prosimy o cierpliwość...</span></div></div>';

            formContainer.append(spinner);


            $.ajax({
                url: $(this).action,
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', progressHandling, false);
                    }
                    return myXhr;
                },
                success: function (data) {
                    if (!successRoute) {
                        throw DOMException('data-success not defined in uploadForm');
                    }
                    formContainer.removeClass('disabled');
                    window.location.replace(successRoute);
                },
                error: function (error) {
                    console.log('Error:');
                    console.log(error);
                }
            })

            return false;
        })
    })
})();