$(function() {
    $(".goto-quiz").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            'url': '/set_usercolor',
            'type': 'post',
            'data': { 'color' : $this.attr('data-color') },
            'dataType': 'json',
            success: function(json) {
                if(json.errors !== undefined) {
                    if(json.errors == '') {
                        window.location = $this.attr('href');
                    } else {
                        alert(json.errors);
                    }
                }
            },
            error: function(xhr, text_status, error_thrown) {
                if (text_status != "abort" && xhr.status !== 0) {
                    requestFailed();
                }
            }
        });
    });
});