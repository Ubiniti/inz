(function () {
    var videoHash = $('#player').data('videoHash');
    var reqUrl = "/watch/" + videoHash + "/rate";

    $("#like_btn").click(function () {
        $.ajax({
            url: reqUrl,
            method : 'post',
            data : {
                rate: 1
            }
        })
        .done(function (res) {
            console.log(res);
        });
    });
    $("#dislike_btn").click(function () {
        $.ajax({
            url: reqUrl,
            method : 'post',
            data : {
                rate: 0
            }
        })
        .done(function (res) {
            console.log(res);
        });
    });
}());