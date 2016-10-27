$(function() {
    
    $(".gd-clr").change(function() {
        $(this).css("color", $(this).val());
        if(!$(this).hasClass('go')) {
            $(".color-descr").load("/group-description", {'what': 'color', 'name' : $(this).val()});
        }
    });
    
    $(".gd-clr").css("color", $(".gd-clr").val());
    
    $(".gd-shape:not(.go)").change(function() {
        $(".shape-descr").load("/group-description", {'what': 'shape', 'name' : $(this).val()});
    });
    
    $(".go-neighborhood").click(function(e) {
        e.preventDefault();
        var color = $(".gd-clr.go option:selected").val(),
            shape = $(".gd-shape.go option:selected").val();
        window.location = "/neighborhood/" + color + '/' + shape;
    });
    
    $(".go-neighborhood, .go-compare").click(function(e) {
        e.preventDefault();
        var color = $(".gd-clr.go option:selected").val(),
            shape = $(".gd-shape.go option:selected").val(),
            page = $(this).hasClass('go-compare') ? "/compare/" : "/neighborhood/";
        window.location = page + color + '/' + shape;
    });
});