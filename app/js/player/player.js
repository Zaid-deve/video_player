$(function () {
    // data
    const video = $("#video-src"),
        progressWidth = $(".progress-inner"),
        videoCurrentTime = $(".video-current-time"),
        videoDuration = $(".video-duration");

    // player listeners 
    {
        $(".btn-play-video").click(function () {
            if (video[0].paused) {
                video[0].play();
                return;
            }
            video[0].pause();
        });

        $('.btn-req-fullscreen').click(function () {
            toggleFullScreen($('.video-container')[0]);
        });

        $('.btn-seek-back').click(function () {
            video[0].currentTime -= 10;
        });

        $('.btn-seek-forward').click(function () {
            video[0].currentTime += 10;
        });

        $('.video-container').dblclick(function (e) {
            if ($(e.target).closest('.video-container').length) {
                toggleFullScreen($(this)[0]);
            }
        });
    }
    attachPlayerEvents(video, progressWidth, videoCurrentTime, videoDuration);
    attachDragEvents(video[0]);

});
