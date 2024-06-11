$(function () {
    const uname = $("#_uname"),
        bio = $("#_bio"),
        profileImg = $("#profileSelInp");

    let upload_err = "";

    profileImg.on('change', function (e) {
        const files = e.target.files;

        if (!files.length) return;
        const file = files[0],
            maxSize = (1024 * 1024) * 2

        if (isImg(file.type) || isImg(file.name.split('.').pop())) {
            if (file.size > maxSize) {
                upload_err = "profile size should be less than or equal to 2mb";
            } else {
                const blob = URL.createObjectURL(file);
                $(".profileSelPrev").attr("src", blob);
                $('.profile-add-icon').addClass('d-none')
                if ($("#_uname").val() != '') $(".btn-submit").removeClass('d-none').focus();
            }
        } else upload_err = "invalid image type !";

        if (upload_err) {
            $(".file-err").text(upload_err)
            $(this).val('');
        } else $(".file-err").text('')
    })

    $("#_uname,#_bio").each((i, f) => {
        $(f).change(function () {
            if ($("#_uname").val() != '') $(".btn-submit").removeClass('d-none').focus();
            else $(".btn-submit").addClass('d-none')
        })
    })

    $("#accountForm").submit(function (e) {
        e.preventDefault();
        let userNameValid = /^[a-zA-Z0-9_]{1,20}$/.test(uname.val()),
            bioValid = bio.val().length < 500

        // reset errors
        $("#_uname,#_bio").next().text('');

        if (!userNameValid) {
            uname.next().text("Invalid Username. It must be 1 to 20 characters and can only contain letters, numbers, or underscores.");
        }

        if (!bio) {
            bio.next().text("Bio Can Be Maximum 500 Characters")
        }

        if (bioValid && userNameValid) {
            const data = new FormData();
            data.append('uname', uname.val());
            data.append('bio', bio.val().trim());
            if (profileImg.val()) data.append('profile', profileImg[0].files[0]);

            ajax('../php/editAccount.php', data, (resp) => {
                if (resp == "success") {
                    location.replace('account.php');
                    return;
                }
                handleErr(resp);
            }, () => handleErr('something went wrong'));
        }
    });

})