$(document).ready(function () {
    $('.custom-file-input').change( function () {
        let filename= this.value.split('\\').pop();
        $(this).next('.custom-file-label').html(filename);
    });
});