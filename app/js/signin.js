$(function () {
    const email = $("#_email"),
        pass = $("#_pass"),
        rem_me = $("#remember_me");

    $("#signinForm").submit(function (e) {
        e.preventDefault();
        let emailValid = isValidEmail(email.val()),
            passValid = !isEmpty(pass.val())

        // reset errors
        $("#_email, #_pass").next().text('');

        if (!emailValid) {
            email.next().text("Invalid Email Address !");
        }

        if (!passValid) {
            pass.next().text("Invalid Password, Password Field Cannot Be Empty !")
        }

        if (emailValid && passValid) {
            $(".btn-submit").attr('disabled', true).fadeTo(.5);
            $("#_email, #_pass").attr('disabled', true);


            const data = new FormData();
            data.append('email', email.val());
            data.append('pass', pass.val());
            data.append("remember_me", rem_me[0].checked?"on":'off');

            ajax('../php/handleSignin.php', data, (resp) => {
                if (resp == "success") {
                    location.replace('account.php');
                    return;
                }
                handleErr(resp);
            }, () => handleErr('something went wrong'), () => {
                $(".btn-submit").attr('disabled', false).fadeTo(1);
                $("#_email, #_pass").attr('disabled', false)
            });
        }
    });

})