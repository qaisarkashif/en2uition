function scrollhere(destination, Top) {
    var stop = $(destination).offset().top - Top,
        delay = 500;
    $('body,html').animate({scrollTop: stop}, delay);
    return false;
}

$(function() {
    $("#feedback").click(function() {
        if($("#prediction-con").length) {
            $("#prediction-con").fadeOut(function() {
                $("#feedback-con").fadeToggle(100);
            });
        } else {
            $("#feedback-con").fadeToggle(100);
        }
    });

    $("#prediction").click(function() {
        if($("#feedback-con").length) {
            $("#feedback-con").fadeOut(function() {
                $("#prediction-con").fadeToggle(100);
                $(".outcome, .proceed-nxt-lvl").show();
            });
        } else {
            $("#prediction-con").fadeToggle(100);
            $(".outcome, .proceed-nxt-lvl").show();
        }
    });

    $('.accordion-toggle').on('click', function(event) {
        var accordion = $(this);
        var accordionContent = accordion.next('.accordion-content');
        // toggle accordion link open class
        accordion.toggleClass("open");
        // toggle accordion content
        accordionContent.slideToggle(250);
        scrollhere("#oc-q" + accordion.attr("data-id"), 100);
    });

    $(".find-out").click(function(e) {
        var level = $(this).attr("data-id"),
            collapse = $(this).attr("data-collapsed");

        if (collapse == 0) {
            $("#fnd-rel-" + level).fadeIn(100);
            $(this).text($(this).attr('data-txt2'));
        } else {
            $("#fnd-rel-" + level).fadeOut(100);
            $(this).text($(this).attr('data-txt1'));
        }
        $(this).attr("data-collapsed", collapse == 0 ? 1 : 0);
    });
	
	/* Feedback Venn Circle Diagrame */
	drawVenn(CT1, CR1, CB1, "fb-y");
	drawVenn(CT2, CR2, CB2, "fb-yp");
});

function drawVenn(CT, CR, CB, ID)
{
	var canvas = document.getElementById(ID);
	var venn = canvas.getContext('2d');
	CT = (CT / 100) * 95;
	CR = (CR / 100) * 95;
	CB = (CB / 100) * 95;
	console.log(CT+" "+CR+" "+CB);
	venn.beginPath();
	venn.rect(0, 0, 400, 400);
	
	/* Bottom Yellow Circle C2 */
	venn.beginPath();
	venn.arc(172, 245.5, CB, 0, 2 * Math.PI, false);
	venn.fillStyle = "rgba(241, 241, 0, 0.5)";
	venn.fill();

	/* Right Aqua Blue Circle C3 */
	venn.beginPath();
	venn.arc(244.5, 185, CR, 0, 2 * Math.PI, false);
	venn.fillStyle = "rgba(0, 241, 241, 0.5)";
	venn.fill();

	/* Top Pink Circle C1 */
	venn.beginPath();
	venn.arc(155, 153.5, CT, 0, 2 * Math.PI, false);
	venn.fillStyle = "rgba(241, 0, 241, 0.5)";
	venn.fill();

	
	
	console.log(CT + CR + CB);
	//var C3Int = calThreeCircleIntersection(155, 153.5, CT, 244.5, 185, CR, 172, 245.5, CB)
	if (Math.abs(CT + CR + CB) >= 171)
	{
		var P = getIntersectionPoints(CT, CB, 155, 153.5, 172, 245.5);
		venn.beginPath();
		venn.moveTo(150,355);
		venn.lineTo(P[0],P[1]);
		venn.moveTo(P[0] + 3,P[1]+6);
		venn.lineTo(P[0],P[1]);
		venn.moveTo(P[0] - 5,P[1] + 5);
		venn.lineTo(P[0],P[1]);
		venn.stroke();
		venn.fillStyle = "black";
		venn.font="16px Myriad Pro";
		venn.fillText(qa_consummate,90,370); 
	}
	else
	{
		if ((CT + CR) > 95)
		{
			var P = getIntersectionPoints(CT, CR, 155, 153.5, 244.5, 185);
			venn.beginPath();
			venn.moveTo(300,50);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] - 2,P[1] - 6);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] + 7,P[1] - 1);
			venn.lineTo(P[0],P[1]);
			venn.stroke();
			venn.fillStyle = "black";
			venn.font="16px Myriad Pro";
			venn.fillText(qa_romantic,260,45); 
		}
		if ((CT + CB) > 95)
		{
			var P = getIntersectionPoints(CT, CB, 155, 153.5, 172, 245.5);
			venn.beginPath();
			venn.moveTo(50,300);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] - 1,P[1] + 6);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] - 6,P[1] - 1);
			venn.lineTo(P[0],P[1]);
			venn.stroke();
			venn.fillStyle = "black";
			venn.font="16px Myriad Pro";
			venn.fillText(qa_fatuous,0,315); 
		}
		if ((CR + CB) > 95)
		{
			var P = getIntersectionPoints(CR, CB, 244.5, 185, 172, 245.5);
			venn.beginPath();
			venn.moveTo(330,280);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] + 1,P[1]+8);
			venn.lineTo(P[0],P[1]);
			venn.moveTo(P[0] + 6,P[1] - 1);
			venn.lineTo(P[0],P[1]);
			venn.stroke();
			venn.fillStyle = "black";
			venn.font="16px Myriad Pro";
			venn.fillText(qa_companionate,265,295); 
		}
	}
}

function getIntersectionPoints(R1, R2, X1, Y1, X2, Y2)
{
	var NX1 = X1 + (R1 / 94) * (X2 - X1);
	var NX2 = X2 + (R2 / 94) * (X1 - X2);
	var NY1 = Y1 + (R1 / 94) * (Y2 - Y1);
	var NY2 = Y2 + (R2 / 94) * (Y1 - Y2);
	var AX = (NX1 + NX2) / 2;
	var AY = (NY1 + NY2) / 2;
	//console.log(NX1+" "+NX1+" "+NY1+" "+NY2);
	//console.log(" Line: "+AX+" "+AY);	
	return [AX, AY];
}
function calThreeCircleIntersection()
{
	var C1 = getIntersectionPoints(R1, R2, X1, Y1, X2, Y2);
	var C2 = getIntersectionPoints(R1, R2, X1, Y1, X2, Y2);
	var C3 = getIntersectionPoints(R1, R2, X1, Y1, X2, Y2);
}