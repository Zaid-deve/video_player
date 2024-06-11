$(function () {
    const finp = $("#fileSelInp"),
        title = $("#_vtitle"),
        des = $("#_vdes");

    finp.on("change", (e) => {
        const files = e.target.files
        if (files.length > 0) handleFile(files[0]);
    })

    $("#_vtitle, #_vdes").each((i, f) => {
        let maxLen = i == 0 ? 100 : 500;
        $(f).on('input', function () {
            let val = $(this).val()
            if (val.length > maxLen) {
                val = val.slice(0, maxLen)
                $(this).val(val)
            }

            $(this).next().next().text(`${val.length} of ${maxLen}`)
        })
    })

    // $(".upload-container").on('drop', function (e) {
    //     e.preventDefault();
    //     if (e.dataTransfer) {
    //         handleFile(e.dataTransfer)
    //     }
    // })

    // file handler
    const handleFile = (file) => {
        let fileErr = "";
        if (file) {
            const { name, size, type } = file;

            let ext = name.split('.').pop(),
                maxSize = (1024 * 1024) * 10;

            if (ext == "mp4" || type.startsWith('video')) {
                if (size > maxSize) {
                    fileErr = "File Too Large, Max 10MB Allowed !";
                } else {
                    const blob = URL.createObjectURL(file);
                    $(".upload-form-row").addClass('slide');
                    $("#uploadPrevVideo").attr("src", blob)
                    $("#uploadPrevVideo").on('loadedmetadata', function () {
                        duration = $(this)[0].duration
                        previewVideoThumbnail($("#uploadPrevVideo")[0])
                        title.val(name.slice(0, 100)).next().next().text(`${Math.min(100, name.length)} of 100`)
                    })
                }
            } else fileErr = "Invalid Video Format";
        }

        if (fileErr) {
            finp.val('');
        }
        finp.next().addClass('text-danger').text(fileErr)
    }


    const previewVideoThumbnail = (src) => {
        let canvas = document.createElement('canvas');
        src.oncanplay = function () {
            canvas.getContext('2d').drawImage(src, 0, 0, src.width, src.height);
            $('#uploadPrevThumbnail').attr('src', canvas.toDataURL('image/jpeg'))
        }
    }

    $('#uploadForm').on("reset", () => {
        $(".upload-form-row").removeClass('slide');
        $("#uploadPrevVideo").attr("src", '');
        $('#uploadPrevThumbnail').attr('src', '')
    })


    $(".btn-play").click(function () {
        $("#uploadPrevVideo")[0].play()
    })
    $("#uploadPrevVideo").on("playing", function () {
        $(".thumbnail-container,.prev-control").addClass('d-none')
    })
    $("#uploadPrevVideo").click(function () {
        $(".thumbnail-container,.prev-control").removeClass('d-none')
        $(this)[0].pause()
    })

    $("#selThumbnailInp").change(function (e) {
        const file = e.target.files[0]
        let thumbErr = "";
        if (file) {
            let formats = ['jpeg', 'png', 'jpg', 'webp'];
            if (file.name.startsWith('image') || !formats.includes(file.name.split().pop)) {
                if (file.size > ((1024 * 1024) * 2)) {
                    thumbErr = "To Large Thumbnail, Only 2MB Allowed !";
                } else {
                    const blob = URL.createObjectURL(file);
                    $('#uploadPrevThumbnail').attr('src', blob)
                }
            } else {
                thumbErr = "Un-supported Thumbnail Type Only " + formats.join(',') + " Allowed !";
            }
        }

        $(".thumbnail-err").text(thumbErr);
    })

    $("#uploadForm").submit(function (e) {
        if (duration > 0) {
            $(this).append(`<input type='text' name='fileDuration' value='${duration}' hidden>`)
        }
        if (title.val() === "") {
            e.preventDefault()
            title.next().text("title cannot be empty !")
            return
        }
        $("#loader").removeClass('d-none').addClass('d-flex')
    })
})