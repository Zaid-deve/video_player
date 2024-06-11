// dragable progress bar
let isDragging = false,
    lastUpdate = 0;

const attachDragEvents = (target) => {
    // Mouse events for desktop
    $('.progress-outer').on('mousedown', function (e) {
        isDragging = true;
        updateProgressBar(e.pageX);
    });

    $(document).on('mousemove', function (e) {
        if (isDragging && Date.now() - lastUpdate > 30) { // Adjust the debounce time as needed
            lastUpdate = Date.now();
            updateProgressBar(e.pageX);
        }
    });

    $(document).on('mouseup', function () {
        isDragging = false;
    });

    // Touch events for mobile
    $('.progress-outer').on('touchstart', function (e) {
        isDragging = true;
        updateProgressBar(e.originalEvent.touches[0].pageX);
    });

    $(document).on('touchmove', function (e) {
        if (isDragging && Date.now() - lastUpdate > 30) { // Adjust the debounce time as needed
            lastUpdate = Date.now();
            updateProgressBar(e.originalEvent.touches[0].pageX);
        }
    });

    $(document).on('touchend', function () {
        isDragging = false;
    });

    function updateProgressBar(pageX) {
        if (!isDragging) return; // Only update if actively dragging
        const progressWidth = $('.progress-outer').width(),
            clickX = pageX - $('.progress-outer').offset().left,
            clickPercentage = Math.min(Math.max(clickX / progressWidth, 0), 1); // Ensure the percentage is between 0 and 1

        // Set the video's currentTime
        const newTime = clickPercentage * target.duration;
        target.currentTime = newTime;
    }
}