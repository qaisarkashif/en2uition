function editRecord(link, id) {
    var tr = $(link).closest('tr');
    if (!$(link).hasClass("fc-green")) {
        $(link).text('save').addClass('fc-green');
        $.each(new Array("low", "medium", "high"), function(i, pr) {
            var chk = tr.find('.chk' + (i + 1)),
                    checked = chk.hasClass('checked') ? 'checked' : '';
            chk.html('<input type="checkbox" class="chk-box" value="' + pr + '" name="privacy[]" ' + checked + ' /><label>&nbsp;</label>');
        });
    } else {
        savePrivacy(tr, link, id);
    }
}

function savePrivacy(tr, link, friend_id) {
    var privacy = new Array();
    tr.find(":checkbox[name='privacy[]']:checked").each(function() {
        privacy.push($(this).val());
    });
    tr.css('cursor', 'wait');
    $.ajax({
        url: "/photo/privacy/edit",
        type: 'post',
        data: {
            'friend_id': friend_id,
            'privacy': privacy.join("|")
        },
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '') {
                    $(link).text('edit').removeClass('fc-green');
                    $.each(new Array("low", "medium", "high"), function(i, pr) {
                        var chk = tr.find('.chk' + (i + 1));
                        if (chk.find('input').is(":checked")) {
                            chk.html('<img src="/assets/img/checked1.png" alt="' + pr + ' privacy">').addClass("checked");
                        } else {
                            chk.html('').removeClass("checked");
                        }
                    });
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
        complete: function() {
            tr.css('cursor', 'default');
        }
    });
}

function filterRows() {
    var search_name = $(".search_area [name='search_text']").val();
    $("#table tbody tr td .user-name").each(function() {
        if ($(this).is(':Contains("' + search_name + '")')) {
            $(this).closest('tr').show();
        } else {
            $(this).closest('tr').hide();
        }
    });
}

$(function() {
    $(document).on('click', ".chk1 label, .chk2 label, .chk3 label", function() {
        var chk = $(this).prev(":checkbox[name='privacy[]']");
        if (chk.is(":checked")) {
            chk.removeProp('checked');
        } else {
            chk.prop('checked', true);
        }
    });

    $(".search_area input:submit").click(filterRows);
    
    $(".search_area [name='search_text']").keypress(function(e) {
        var code = e.which || e.keyCode;
        if(code == 13) {
            filterRows();
        }
    }).val('');
});