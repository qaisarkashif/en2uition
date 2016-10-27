function filterRows() {
    var search_name = $(".search_area [name='search_text']").val();
    $(".friend-list .friend-name").each(function() {
        if ($(this).is(':Contains("' + search_name + '")')) {
            $(this).closest('.friend').show();
        } else {
            $(this).closest('.friend').hide();
        }
    });
}

function show(elem) {
    elem.style.display = "block";
    if (elem.offsetLeft >= 850) {
        $(elem).addClass("left");
    }
}

function hide(elem) {
    $(elem.id).removeClass("left");
    elem.style.display = "";
}

$(function() {
    $(".search_area input:submit").click(filterRows);
    $(".search_area [name='search_text']").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            filterRows();
        }
    }).val('');
    $(".ftooltip").hover(function(e) {
        $(this).hide();
    });
});