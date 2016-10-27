$(function() {
    $("#green-partner #t-green").click(function() {
        $("#green-partner").css("background", "#6aa048").find(".rel-det").hide();
        $("#green-partner").find(".rel-clr-det").show();
		$("#green-partner .rel-cl").css("display","block");
        $("#blue-partner").css("background", "#ff").find(".rel-det").show();
        $("#blue-partner").find(".rel-clr-det").hide();
		$("#blue-partner .rel-cl").css("display","none");
    });
    $("#green-partner #csl-green").click(function() {
        $("#green-partner").css("background", "#fff").find(".rel-det").show();
        $("#green-partner").find(".rel-clr-det").hide();
		$("#green-partner .rel-cl").css("display","none");
    });
    
    $("#blue-partner #t-blue").click(function() {
        $("#blue-partner").css("background", "#01aefd").find(".rel-det").hide();
        $("#blue-partner").find(".rel-clr-det").show();
		$("#blue-partner .rel-cl").css("display","block");
        $("#green-partner").css("background", "#ff").find(".rel-det").show();
        $("#green-partner").find(".rel-clr-det").hide();
		$("#green-partner .rel-cl").css("display","none");
    });
    $("#blue-partner #csl-blue").click(function() {
        $("#blue-partner").css("background", "#fff").find(".rel-det").show();
        $("#blue-partner").find(".rel-clr-det").hide();
		$("#blue-partner .rel-cl").css("display","none");
    });
	$(".rel-det img").mouseover(function(e) {
        var src = $(this).attr("src");
		$(this).attr("src", src.replace("man","woman"));
    });
	$(".rel-det img").mouseout(function(e) {
        var src = $(this).attr("src");
		$(this).attr("src", src.replace("woman","man"));
    });
	$(window).on('resize', function(){
		$(".rel-txt").each(function(index, element) {
		   var txtHeight = $(this).height();
		   $(this).css("top","50%");
		   $(this).css("margin-top","-"+(txtHeight/2)+"px");
		});
    }).trigger('resize'); //on page load
});