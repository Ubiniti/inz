(function () {
    console.log('asdasdasd');
    $(document).ready(function () {
        var playerWrapper = $('.player-wrapper');
        var videoPlayer =  $('.demo-player');
        var overlay = $('.demo-overlay');
        var demoOverlayTitle = $('.demo-buy');

        function switchScreen() {
            overlay.toggleClass('d-none');
            demoOverlayTitle.toggleClass('d-none');
            videoPlayer.removeAttr('controls');
        }

        console.log(videoPlayer);

        videoPlayer.on('ended', function () {
            console.log('Finished plaing video');
            switchScreen();
        });
    });
})();