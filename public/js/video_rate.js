(function () {
    var videoHash = $('#player').data('videoHash');
    var reqUrl = $('#thumbs-container').data('action');

    var thumbUpIcon = $('#like_btn i');
    var thumbDownIcon = $('#dislike_btn i');
    var thumbUpCount = $('#thumbs_up');
    var thumbDownCount = $('#thumbs_down');

    function clearRatingIconsClasses() {

        thumbUpIcon.removeClass('far');
        thumbDownIcon.removeClass('far');
        thumbUpIcon.removeClass('fas');
        thumbDownIcon.removeClass('fas');
    }

    function activeUp() {

        clearRatingIconsClasses();

        thumbUpIcon.addClass('fas');
        thumbDownIcon.addClass('far');
    }

    function activeDown() {

        clearRatingIconsClasses();
        
        thumbUpIcon.addClass('far');
        thumbDownIcon.addClass('fas');
    }

    $("#like_btn").click(function () {
        $.ajax({
            url: reqUrl,
            method : 'post',
            data : {
                rate: 1
            }
        })
        .done(function (res) {
            var rating = JSON.parse(res);
            thumbUpCount.text(rating.up);
            thumbDownCount.text(rating.down);
            activeUp();
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
            var rating = JSON.parse(res);
            thumbUpCount.text(rating.up);
            thumbDownCount.text(rating.down);
            activeDown();
        });
    });
}());