function setErrorState(elem, err) {
    err = err !== undefined ? err : '';
    elem.append('<span class="glyphicon glyphicon-remove form-control-feedback cmt-tooltip2" title="' + err + '"></span>');
    elem.addClass('has-error has-feedback');
}

function setSuccessState(elem) {
    elem.append('<span class="glyphicon glyphicon-ok form-control-feedback cmt-tooltip2" title="ok"></span>');
    elem.addClass('has-success has-feedback');
}

function clearState(form) {
    form.find(".form-group").each(function() {
        $(this).attr('class', 'form-group');
        $(this).find('.form-control-feedback').remove();
    });
}

function setValidationResult(form, data) {
    if(data.errors !== undefined) {
        var ecount = 0;//error counter
        form.find(".form-control").each(function() {
            if (data.errors[$(this).attr('name')] !== undefined) {
                setErrorState($(this).closest('li'), data.errors[$(this).attr('name')]);
                ecount++;
            } else {
                setSuccessState($(this).closest('li'));
            }
        });
        Tipped.create('.cmt-tooltip2',{ containment: 'viewport', position: 'bottom', inline: true });
        return ecount == 0;
    } else {
        return false;
    }
}

function forgotPassword() {
    $("#forgotModal .validation-error").remove();
    var btns = $("#forgotModal .btn");
    btns.prop('disabled', true).parent('div').css('cursor','wait');
    $.ajax({
        url: '/auth/forgot_password',
        type: 'post',
        data: { 'email' : $("#frgt-email").val() },
        success: function(json) {
            if(json.errors !== undefined) {
                if(json.errors == '') {
                    $("#forgotModal").modal('hide');
                    var msg = json.message;
                    popupMessage(msg.title, msg.text);
                } else {
                    $("#forgotModal .modal-body").append(json.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) { alert('request Failed'); }},
        complete: function() {
            btns.prop('disabled', false).parent('div').css('cursor','default');
        }
    });
}

$(function() {
    $('body').click(function() {
        $(".signin, .signup").removeClass("disbox").hide();
        $(".btn-signin, .btn-signup").removeClass("active");
    });
    $('.signin, .signup, #btn_forgot_pwd').click(function(event) {
        event.stopPropagation();
    });
    $(".btn-signup").mouseover(function(e) {
        $(this).addClass("active");
        $(".signup").addClass("disbox");
    });
    $(".btn-signin").mouseover(function(e) {
        $(this).addClass("active");
        $(".signin").addClass("disbox");
    });
    $("#show-su").on('mouseleave', function(e) {
        if ($(this).find('input[name=email]').is(':focus') || $(this).find('input[name=password]').is(':focus') || $(this).find('input[name=passconf]').is(':focus')) {
            e.preventDefault();
            return false;
        }
        $(".btn-signup").removeClass("active");
        $(".signup").removeClass("disbox");
    });

    $("#show-si").on('mouseleave', function(e) {
        if ($(this).find('input[name=email]').is(':focus') || $(this).find('input[name=password]').is(':focus')) {
            e.preventDefault();
            return false;
        }
        $(".btn-signin").removeClass("active");
        $(".signin").removeClass("disbox");
    });

    $(".navigation ul li ul li").click(function() {
        $(".navigation ul li ul").addClass("hide-sub");
    });

    $("#forgotModal").on('show.bs.modal', function () {
        $("#frgt-email").val('');
        $("#forgotModal .validation-error").remove();
        $("#forgotModal .btn").prop('disabled', false).parent('div').css('cursor','default');
    });

    $("#frm-signup").submit(function(e) {
        e.preventDefault();
        var form = $("#frm-signup");
        clearState(form);
        var button = form.find("[name=btnsubmit]");
        button.prop('disabled', true).parent('li').css('cursor','wait');
        $.ajax({
            url: '/auth/signup',
            type: 'post',
            data: $(this).serialize(),
            success: function(data) {
                var valid = setValidationResult(form, data);
                if(valid) {
                    clearState(form);
                    form.trigger('reset');
                    var msg = '<h2>Your account has been successfully registered!</h2><h4>You will be sent an email with instructions for activating your account.</h4>';
                    popupMessage('Sign up process:', msg);
                }
            },
            error: function (xhr, text_status, error_thrown) {
                if (text_status != "abort" && xhr.status !== 0) {
                    alert('request Failed');
                }
            },
            complete: function() {
                button.prop('disabled', false).parent('li').css('cursor','default');
            }
        });
    });

    $("#frm-signin").submit(function(e) {
        e.preventDefault();
        var form = $("#frm-signin");
        clearState(form);
        var button = form.find("[name=btnsubmit]");
        button.prop('disabled', true).parent('li').css('cursor','wait');
        $.ajax({
            url: '/auth/signin',
            type: 'post',
            data: $(this).serialize(),
            success: function(data) {
                var valid = setValidationResult(form, data);
                if(valid) {
                    setTimeout(function(){ document.location.href = (data.joined ? '/homepage' : '/relationship'); }, 500);
                }
            },
            error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) { alert('request Failed'); }},
            complete: function() {
                button.prop('disabled', false).parent('li').css('cursor','default');
            }
        });
    });
});
