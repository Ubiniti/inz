(function () {
    $(document).ready(function () {
        var progressBar = $(upload_progress);
        var successRoute = $(uploadForm).data('success');

        function progressHandling(e) {
            var percent = e.loaded / e.total * 100;

            progressBar.css('width', percent + '%');
        }

        $(uploadForm).on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(uploadForm);
            formData.append('add_video[file]', add_video_file.files[0]);

            $.ajax({
                url: $(this).action,
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', progressHandling, false);
                    }
                    return myXhr;
                },
                success: function(data) {
                    if (!successRoute) {
                        throw DOMException('data-success not defined in uploadForm');
                    }
                    window.location.replace(successRoute);
                },
                error: function(error) {
                    console.log('Error:');
                    console.log(error);
                }
            })

            return false;
        })
    })
})();