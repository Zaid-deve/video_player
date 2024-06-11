$(function () {
    $(".search-toggler").click(function () {
        $(".search-bar").removeClass('d-none')
        $('#header-search-inp').val('').focus()
        $(".search-bar").show(300)
    })

    $('#header-search-inp').on("blur",function(){
        if($(this).val()!=='')return;
        $(".search-bar").addClass('d-none')
    })
    let debounceTimeout;
    $('#header-search-inp').on('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function () {
            let qry = $('#header-search-inp').val().trim();
            if (!qry) {
                $(".search-results").addClass('d-none')
                $(".search-results").html('')
                return;
            }

            $.get(`http://localhost/video_player/app/php/search.php?qry=${qry}`, function (resp) {
                $(".search-results").removeClass('d-none')
                $(".search-results").html(resp)
            })
        }, 300);
    })
})