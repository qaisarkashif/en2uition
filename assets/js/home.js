function homePageAdj()
{
    $("#bottom-home").addClass("bcp");
    $("#bottom-home").css("height", (window.innerHeight - 770) + "px");
    $(".bottom-area").css("height", "126px");
}

$(document).ready(function(e) {
    $(window).resize(function() {
        homePageAdj();
    });
    if (window.innerHeight >= 800)
    {
        homePageAdj();
    }
});