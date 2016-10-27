$(function() {
    $("#frm-profile-setting").submit(function(e) {
        e.preventDefault();
        var button = $("#frm-profile-setting").find(".btn-update-setting");
        button
            .prop('disabled', true)
            .parent("div")
            .css("cursor", "wait");
        $.ajax({
            url: '/relationship/update_lite_settings',
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
                        alert.addClass('alert-success');
                        alert_msg = '<p><strong>Success!</strong> Your settings have been updated successfully.</p>';
                        alert_msg += '<p>Please reload the page to accept the changes.</p>';
                        $("#frm-profile-setting").find("#collapse2 :password").val('');
                        var userbox = $("#top-nav .pic", window.parent.document),
                            ava = userbox.find('img');
                        userbox
                            .empty()
                            .append(ava)
                            .append('&nbsp;&nbsp;' + $.trim($("#username").val()));
                    }
                    alert.find('.alert-msg').html(alert_msg);
                    alert.show();
                }
            },
            error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
            complete: function() {
                button
                    .prop('disabled', false)
                    .parent("div")
                    .css("cursor", "default");
            }
        });
    });
});