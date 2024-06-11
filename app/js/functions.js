const isEmpty = (val) => {
    return [undefined, null, ''].includes(val);
}

const isValidEmail = (val) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
}

const isImg = (type) => {
    return type.startsWith('image') || ['jpeg', 'png', 'jpg'].includes(type);
}

const isVideo = (type) => {
    return type.startsWith('video') || type === "mp3";
}

const isVideoSizeExceeded = (size) => {
    return size > ((1024 * 1024) * 10)
}

const throwErr = (target, text) => {
    $(target).parent().addClass('show').find('.alert-text').text(text);
}

const handleErr = (resp) => {
    $(".err-container").addClass('show').find('.alert-text').text(resp);
}

const ajax = (url, data, successCallback, errorCallback, completeCallback) => {
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        processData: false,
        contentType: false,
        success: successCallback,
        error: errorCallback,
        complete: function (xhr, status) {
            if (typeof completeCallback === 'function') {
                completeCallback(xhr.responseText, status);
            }
        }
    });
}

function formatTime(timestamp) {
    if (isNaN(timestamp)) return "00:00"
    let hours = Math.floor(timestamp / 3600),
        rsec = timestamp % 3600,
        minutes = Math.floor(rsec / 60),
        seconds = rsec % 60;


    if (hours > 0) {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toFixed(0).toString().padStart(2, '0')}`;
    } else {
        return `${minutes.toString().padStart(2, '0')}:${seconds.toFixed(0).toString().padStart(2, '0')}`;
    }
}

function toggleFullScreen(element) {
    if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
        exitFullScreen();
    } else {
        requestFullScreen(element);
    }

    if (screen.orientation && screen.orientation.lock) {
        screen.orientation.lock('landscape');
    }
}

function requestFullScreen(element) {
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.mozRequestFullScreen) { /* Firefox */
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
        element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) { /* IE/Edge */
        element.msRequestFullscreen();
    }
}

function exitFullScreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) { /* Firefox */
        document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) { /* IE/Edge */
        document.msExitFullscreen();
    }
}

function toggleMenu() {
    const menu = $(".menu-left")
    menu.removeClass("d-md-block")

    if (menu.hasClass('d-none')) {
        menu.removeClass('d-none')
        $(".content-grid .grid-item").removeClass('col-lg-3');
        $(".content-grid .grid-item").addClass('col-lg-4');
    } else {
        menu.addClass('d-none')
        $(".content-grid .grid-item").addClass('col-lg-3');
        $(".content-grid .grid-item").removeClass('col-lg-4');
    }
}
