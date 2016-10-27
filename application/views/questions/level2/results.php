<?php
	session_start();
	if (!isset($_SESSION['Results']))
		$_SESSION['Results'] = array();
	if (isset($_REQUEST['ShowResults']))
	{
		//echo("<pre>");
		//print_r($_SESSION['Results']);
		//echo("</pre>");
		$SQ = 0;
		echo("<table width=\"100%\" class=\"features-table\">");
		foreach ($_SESSION['Results'] as $Res)
		{
			$SQ++;
			$Privacy = "";
			if ($Res['privacy'] == "Low")
				$Privacy = "<span style=\"background:yellow; padding: 1px 5px; font-size:12px; color:#fff; text-shadow:none;\">".$Res['privacy']."</span>";
			elseif ($Res['privacy'] == "Medium")
				$Privacy = "<span style=\"background:orange; padding: 1px 5px; font-size:12px; color:#fff; text-shadow:none;\">".$Res['privacy']."</span>";
			elseif ($Res['privacy'] == "High")
				$Privacy = "<span style=\"background:red; padding: 1px 5px; font-size:12px; color:#fff; text-shadow:none;\">".$Res['privacy']."</span>";
			echo("<tr><td style=\"font-size:14px;\">
				<strong>".$SQ."-Question ".$Res['question_id']." Part ".$Res['sub_question_id'].":</strong>&nbsp;".$Res['question_heading']."</td></tr>");
			echo("<tr><td><p><strong>Question: </strong>&nbsp;".$Res['question']."&nbsp;&nbsp;".$Privacy."<br><strong>Answer: </strong>&nbsp;".$Res['answer']."</p>");
			if (isset($Res['question1']) && $Res['question1'] != "")
				echo("<p><strong>Question 2: </strong>&nbsp;".$Res['question1']."<br><strong>Answer 2: </strong>&nbsp;".$Res['answer1']."</p>");
			if (isset($Res['boolean']) && $Res['boolean'] != "")
				echo("<strong>Yes / No:&nbsp;</strong>".$Res['boolean']);
		}
		echo("</td></tr></table>");
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="keywords" content="Describe keywords for Seo">
<meta name="description" content="Detail for Seo">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Results</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/FontAwesome/css/font-awesome.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->
<script src="js/jquery.min.js"></script>
<script language="javascript">
	function SaveAnswer(QID,SUB_QID,Q,AID,Progress)
	{
		var data = '';
		$.ajax({
			url: "results.php?ShowResults",	
			type: "POST",		
			data: "",
			cache: false,
			success: function (data)
			{
				document.getElementById("results").innerHTML = data;
			}		
		});
	}
	window.onload = SaveAnswer;
</script>
</head>
<body>
<div id="results"></div>
</body>
</html>