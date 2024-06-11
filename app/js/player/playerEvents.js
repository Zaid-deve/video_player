const attachPlayerEvents = (video, progressWidth, videoCurrentTime, videoDuration) => {
    video.on("loadedmetadata", function () {
        $(".loader-top").addClass('d-none')
        $('.video-controls').show(300).removeClass('d-none')
        videoDuration.text(formatTime(video[0].duration))
    })

    video.on("ended pause", function () {
        $('.video-controls').show(300).removeClass('d-none')
        $(".btn-play-video").find('i')[0].className = `fa-solid fa-play`
    })

    video.on('waiting', function () {
        $(".buffer-icon").removeClass('d-none')
        $('.video-controls').show(100)
    })

    video.on('canplay', function () {
        $(".buffer-icon").addClass('d-none')
    })

    video.on("play", function () {
        setTimeout(() => {
            if (!video[0].paused && !video[0].waiting) {
                $('.video-controls').hide(300)
            }
        }, 1500)
        $(".btn-play-video").find('i')[0].className = `fa-solid fa-pause`
    })

    video.on("timeupdate", function () {
        const { currentTime, duration } = video[0],
            complete = (currentTime / duration * 100).toFixed(0)
        videoCurrentTime.text(formatTime(currentTime));
        videoDuration.text(formatTime(duration));
        progressWidth.css('width', `${complete}%`)
    })

    video[0].addEventListener('progress', function () {
        if (video[0].buffered.length > 0) {
            const bufferedEnd = video[0].buffered.end(video[0].buffered.length - 1);
            const duration = video[0].duration;
            if (duration > 0) {
                const percentLoaded = (bufferedEnd / duration) * 100;
                $(".porgress-end").css('width', percentLoaded.toFixed(2) + '%');
            }
        }
    });

    $(".video-container").click(function (e) {
        // if ($(e.target).parent(".video-controls")) return
        if ($('.video-controls').is(":visible")) {
            $('.video-controls').hide()
        } else {
            $('.video-controls').show()
        }
    })

    $(window).on("beforeunload",function(){
        sessionStorage.setItem("ubload",true)
        const data = `fileId=${fileId}&watch=${video[0].currentTime}&duration=${video[0].duration}`
        $.get('app/php/updateHistory.php?'+data);
    })
}