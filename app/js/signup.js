$(function () {
    const email = $("#_email"),
        pass = $("#_pass"),
        uname = $("#_uname"),
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
            }
        } else upload_err = "invalid image type !";

        if (upload_err) {
            $(".file-err").text(upload_err)
            $(this).val('')
        } else $(".file-err").text('')
    })

    $("#signupForm").submit(function (e) {
        e.preventDefault();
        let emailValid = isValidEmail(email.val()),
            passValid = pass.val().length > 8,
            userNameValid = /^[a-zA-Z0-9_]{1,20}$/.test(uname.val()); // Compulsory and 1 to 20 characters

        // reset errors
        $("#_email, #_pass, #_uname").next().text('');

        if (!userNameValid) {
            uname.next().text("Invalid Username. It must be 1 to 20 characters and can only contain letters, numbers, or underscores.");
        }

        if (!emailValid) {
            email.next().text("Invalid Email Address !");
        }

        if (!passValid) {
            pass.next().text("Invalid Password, It Must be Of Minimum 8 Characters !").addClass('text-danger');
        }

        if (emailValid && passValid && userNameValid) {
            $(".btn-submit").attr('disabled', true).fadeTo(.5);
            $("#_email, #_pass, #_uname").attr('disabled', true)
            const data = new FormData();
            data.append('email', email.val());
            data.append('pass', pass.val()); // Changed from email.val() to pass.val()
            data.append('uname', uname.val());
            data.append('profile', profileImg[0].files[0]);

            ajax('../php/handleSignup.php', data, (resp) => {
                if (resp == "success") {
                    location.replace('account.php');
                    return;
                }
                handleErr(resp);
            }, () => handleErr('something went wrong'), () => {
                $(".btn-submit").attr('disabled', false).fadeTo(1);
                $("#_email, #_pass, #_uname").attr('disabled', false)
            });
        }
    });

})