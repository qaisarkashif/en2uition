$(function() {
    $("#red-partner #t-red").click(function() {
        $("#red-partner ").css("background", "#bb1112").find(".rel-det").hide();
        $("#red-partner ").find(".rel-clr-det").show();
		$("#red-partner .rel-cl").css("display","block");
        $("#yellow-partner").css("background", "#ff").find(".rel-det").show();
        $("#yellow-partner").find(".rel-clr-det").hide();
		$("#yellow-partner .rel-cl").css("display","none");
    });
    $("#red-partner #csl-red").click(function() {
        $("#red-partner").css("background", "#fff").find(".rel-det").show();
        $("#red-partner").find(".rel-clr-det").hide();
		$("#red-partner .rel-cl").css("display","none");
    });
    
    $("#yellow-partner #t-yellow").click(function() {
        $("#yellow-partner").css("background", "#fff258").find(".rel-det").hide();
        $("#yellow-partner").find(".rel-clr-det").show();
		$("#yellow-partner .rel-cl").css("display","block");
        $("#red-partner").css("background", "#ff").find(".rel-det").show();
        $("#red-partner").find(".rel-clr-det").hide();
		$("#red-partner .rel-cl").css("display","none");
    });
    $("#yellow-partner #csl-yellow").click(function() {
        $("#yellow-partner").css("background", "#fff").find(".rel-det").show();
        $("#yellow-partner").find(".rel-clr-det").hide();
		$("#yellow-partner .rel-cl").css("display","none");
    });
	$(window).on('resize', function(){
		$(".rel-txt").each(function(index, element) {
		   var txtHeight = $(this).height();
		   $(this).css("top","50%");
		   $(this).css("margin-top","-"+(txtHeight/2)+"px");
		});
    }).trigger('resize'); //on page load
});