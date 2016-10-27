function unblockUser(link, user_id) {
    $.ajax({
        url: '/users/unblock',
        type: 'get',
        data: {'uid':user_id},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == "") {
                    $(link).closest('.blocked-user').remove();
                    $(".msg-unblock[data-uid='"+user_id+"']", window.parent.document).addClass('hide');
                    $(".msg-block[data-uid='"+user_id+"']", window.parent.document).removeClass('hide');
                    $(".tpd-tooltip:visible").remove();
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function accountStatusEvent(obj, slct, act_txt, inact_txt) {
    $(obj).toggleClass("inactive");
    if ($(obj).hasClass("inactive")) {
        $(obj).html(inact_txt);
        $("#frm-profile-setting #"+slct).val(0);
    } else {
        $(obj).html(act_txt);
        $("#frm-profile-setting #"+slct).val(1);
    }
}

$(function() {
    Tipped.create('.cmt-tooltip',{ containment: 'viewport', position: 'bottom' });

    $(document).on('mouseover', '.tpd-tooltip', function() {
        $(this).remove();
    });
    
    $(".profile-status").click(function(e) {
        e.preventDefault();
        accountStatusEvent(this, 'profile-status', "Active", "Inactive");
    });
    
    $(".site-vers").click(function(e) {
        e.preventDefault();
        accountStatusEvent(this, 'site-vers', "en2uition", "en2uition-lite");
    });
    
    $(".users-guide-status").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("inactive");
        var text = 'Visible', val = 0;
        if($(this).hasClass("inactive")) {
            text = 'Hidden';
            val = 1;
        }
        $(this).html(text);
        $("#frm-profile-setting [name='users_guide_hidden']").val(val);
    });

    $(".blocked span").on("click", function(e) {
        $(this).addClass("unblocked");
    });

    $(".btn-update-setting").on("click", function(e) {
        $(".profile-setting span.unblocked").hide();
    });
    
    $(".emnf-checkbox").change(function() {
        var val = $(this).is(":checked") ? 1 : 0,
            pos = $(this).closest("li.checkbox").index(),
            emnf = $("#email_notifications").val().split('');
        emnf[pos] = val;
        $("#email_notifications").val(emnf.join(''));
    });

    $("#frm-profile-setting").submit(function(e) {
        e.preventDefault();
        var button = $("#frm-profile-setting").find(".btn-update-setting");
        button.prop('disabled', true).parent("div").css("cursor", "wait");
        $.ajax({
            url: '/profile/update_settings',
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if(data.errors !== undefined) {
                    var alert = $("#frm-profile-setting").find("div.alert");
                    alert.attr('class', 'alert');
                    var alert_msg = '';
                    if($.trim(data.errors) != '') {
                        alert.addClass('alert-danger');
                        alert_msg = '<ul>'+data.errors+'</ul>';
                    } else {
                        if(data.account_inactive !== undefined && data.account_inactive) {
                            window.alert(data.inactive_msg);
                            $(".clsbtn .close", window.parent.document).click();
                            window.parent.location = '/signout';
                        }
                        alert.addClass('alert-success');
                        alert_msg = '<p><strong>Success!</strong> Your settings have been updated successfully.</p>';
                        alert_msg += '<p>Please reload the page to accept the changes.</p>';
                        $("#frm-profile-setting").find("#collapse2 :password").val('');
                        
                        if($("#userguide", window.parent.document).length !== 0) {
                            var btn = $("#userguide", window.parent.document),
                                val = $("#frm-profile-setting [name='users_guide_hidden']").val();
                            if(btn.attr('data-id') == 1) {
                                window.parent.document.getElementById('userguide').click();
                            }
                            if(val == 1) {
                                btn.addClass('hidden');
                            } else {
                                btn.removeClass('hidden');
                            }
                        }
                        
                        if($("#site-vers").val() == '0') {
                            $(".clsbtn .close", window.parent.document).click();
                            window.parent.location = '/relationship';
                        }
                    }
                    alert.find('.alert-msg').html(alert_msg);
                    alert.show();
                }
            },
            error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
            complete: function() {
                button.prop('disabled', false).parent("div").css("cursor", "default");
            }
        });
    });
});