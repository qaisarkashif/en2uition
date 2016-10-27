var rows_request = false;

function loadNextRows() {
    var spinner = $(".neighborhood-list .wait"),
        table = $(".neighborhood-list #table tbody"),
        last_id = table.find("tr").length ? table.find("tr:last").attr('data-id') : -1;
    spinner.removeClass('hide').css('visibility', 'visible');
    rows_request = true;
    $.ajax({
        url      : '/neighborhood/username_ajax',
        type     : 'post',
        data     : { 
            'username' : $(".username").val(),
            'last_id'  : last_id 
        },
        dataType : 'json',
        success: function(data) {
            rows_request = data.stop_request !== undefined ? data.stop_request : false;
            if (data.rows !== undefined) {
                $.each(data.rows, function(i, rec) {
                    var a1 = '<a href="/forum/topic-'+rec.id+'/'+rec.row_color+'/'+rec.row_shape+'">' + rec.title + '</a>',
                        a2 = '<a href="/neighborhood/date/'+rec.last_post_url+'/'+rec.row_color+'/'+rec.row_shape+'">' + rec.last_post_text + '</a>';
                    $("<tr/>")
                        .attr({
                            'data-id' : rec.id,
                            'data-shape' : rec.row_shape,
                            'data-color' : rec.row_color
                        })
                        .append('<td>' + rec.date_posted + '</td>')
                        .append('<td class="topic-text">' + a1 + '</td>')
                        .append('<td align="center" class="nopad">' + rec.users_count + '</td>')
                        .append('<td align="center" class="nopad">' + rec.posts_count + '</td>')
                        .append('<td align="center" class="nopad">' + rec.shares_count + '</td>')
                        .append('<td>' + a2 + '</td>')
                        .appendTo(table);
                });
                $(".nbr-data").slimScroll().mouseover();
            }
        },
        error: function (xhr, text_status, error_thrown) { 
            rows_request = false; 
            if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } 
        },
        complete: function() { 
            spinner.css('visibility', 'hidden').addClass('hide');
        }
    });
}

$(function() {
    $(document).on('mouseover', ".neighborhood-list tbody tr", function() {
        $(".nh-shape img").attr({
            'src': '/assets/img/shapes/shape/' + $(this).attr('data-shape') + '.png',
            'class': 'shape-' + $(this).attr('data-color')
        });
    }).on('mouseout', ".neighborhood-list tbody tr", function() {
        $(".nh-shape img").attr({
            'src': '/assets/img/shapes/shape/hexagon.png',
            'class': 'shape-neighborhood'
        });
    });
    
    $(".username").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            window.location = '/neighborhood/username/'+encodeURIComponent($(this).val());
        }
    }).autocomplete({
        source: "/get_usernames",
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 2,
        select: function( event, ui ) {
            window.location = '/neighborhood/username/'+encodeURIComponent(ui.item ? ui.item.value : this.value);
        }
    });
    
    $(".nbr-data").scroll(function(e) {
        var h = $(this).find("#table tbody").height(),
            h1 = $(this).height(),
            scr_top = $(this).scrollTop();
        if((h1 + scr_top) > h && rows_request === false) {
            loadNextRows();
        }
    });
    
});