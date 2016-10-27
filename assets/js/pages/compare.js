function getVMax(max) {
    max = parseInt(max);
    if (max === 0) {
        return 1;
    }
    var m = 16;
    while (m < max) {
        m *= 10;
    }
    return m;
}

function reloadHistoData() {
    var filters = {},
            select_names = new Array('friend', 'country', 'state', 'city', 'color', 'shape');
    $.each(select_names, function(i, name) {
        filters[name] = $("select[name='" + name + "'] option:selected").val();
    });
    $.ajax({
        'url': "/homepage/get_compare_data",
        'type': 'post',
        'data': {'filters': filters},
        'dataType': 'json',
        success: function(json) {
            $(".dm-pro-bar").attr({
                'data-max': json.max_users !== undefined ? json.max_users : 0,
                'data-all-count': json.all_users_count !== undefined ? json.all_users_count : 0
            });
            $.each(json, function(key, val) {
                if (key !== 'data-all-count' && key !== 'data-max') {
                    $(".dm-pro-bar .col").eq(key).attr('data-id', val.users_count !== undefined ? val.users_count : 0);
                }
            });
            histogram();
        },
        error: function(xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
}

function histogram() {
    var max = $(".dm-pro-bar").attr('data-max'),
            vm = getVMax(max);
    var ustep = 384 / vm,
            fz = vm > 1 ? vm / 16 : 1;
    $("#population").text($(".dm-pro-bar").attr('data-all-count'));
    $(".dm-pro-bar .col").each(function() {
        var val = $(this).attr("data-id");
        $(this).animate({height: (val * ustep) + "px"});
        $(this).attr("title", val + " User(s)");
    });

    $(".lax").each(function() {
        var mle = $(this).hasClass('w') ? 4 : 2.1;
        $(this).text($(this).attr('data-num') * fz)
                .css('margin-left', (-20 - (mle * fz.toString().length)) + 'px');
    });
}

$(function() {
    reloadHistoData();
});