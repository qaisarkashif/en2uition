function sendContactUsMessage(btn) {
    btn.prop('disabled', true);
    $.ajax({
        url: '/send-feedback',
        type: 'post',
        dataType: 'json',
        data: {
            'feedback': $("#main-contact-form #message").val()
        },
        success: function(data) {
            if (data.errors !== undefined) {
                if(data.errors == '') {
                    $("#main-contact-form #message").val('');
                    var tab = $("[data-tab='tab-1']");
                    if(tab.hasClass('active')) {
                        tab.find('a').click();
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function(xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        },
        complete: function() {
            btn.prop('disabled', false);
        }
    });
}

$(function() {
    $("#main-contact-form button:submit").click(function(e) {
        e.stopPropagation();
        sendContactUsMessage($(this));
    });
});