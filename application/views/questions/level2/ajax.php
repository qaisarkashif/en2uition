<?php
	session_start();
	$StepVal = 0.67;
	if (!isset($_SESSION['PlayGear'])) $_SESSION['PlayGear'] = 1;
	if (isset($_REQUEST['SaveAns']))
	{
		$Exist = false;
		foreach ($_SESSION['Results'] as $Res)
		{
			if ($_REQUEST['QID'] == $Res['question_id'] && $_REQUEST['SUB_QID'] == $Res['sub_question_id'])
			{
				//$Res['question_heading'] = $_REQUEST['question_heading'];
				//$Res['question'] = $_REQUEST['question'];
				$Res['answer'] = $_REQUEST['answer'];
				$Res['privacy'] = $_REQUEST['privacy'];
				$Exist = true;
			}
		}
		if ($Exist == false)
		{
			$_SESSION['Results'][] = array(
				"question_id" => $_REQUEST['QID'], 
				"sub_question_id" => $_REQUEST['SUB_QID'],
				"question_heading" => $_REQUEST['question_heading'],
				"question" => $_REQUEST['question'],
				"answer" => $_REQUEST['answer'],
				"question1" => isset($_REQUEST['question1']) ? $_REQUEST['question1'] : "",
				"answer1" => isset($_REQUEST['answer1']) ? $_REQUEST['answer1'] : "",
				"boolean" => isset($_REQUEST['boolean']) ? $_REQUEST['boolean'] : "", 
				"privacy" => $_REQUEST['privacy']);

			//	Count Progress
			if (!isset($_SESSION['Progress']))
				$_SESSION['Progress'] = $StepVal;
			else if ($_SESSION['Progress'] >= 99.5)
				$_SESSION['Progress'] = 100;
			else
				$_SESSION['Progress'] = $_SESSION['Progress'] + $StepVal;
			
			// For No answer
			if (isset($_REQUEST['boolean']) && strtolower($_REQUEST['boolean']) == "no")	
			{
				$_SESSION['Results'][] = array(
					"question_id" => $_REQUEST['QID'], 
					"sub_question_id" => $_REQUEST['SUB_QID']+1,
					"question_heading" => $_REQUEST['question_heading'],
					"question" => $_REQUEST['question'],
					"answer" => $_REQUEST['answer'],
					"question1" => isset($_REQUEST['question1']) ? $_REQUEST['question1'] : "",
					"answer1" => isset($_REQUEST['answer1']) ? $_REQUEST['answer1'] : "",
					"boolean" => isset($_REQUEST['boolean']) ? $_REQUEST['boolean'] : "", 
					"privacy" => $_REQUEST['privacy']);
				if ($_SESSION['Progress'] >= 99.50)
					$_SESSION['Progress'] = 100;
				else
					$_SESSION['Progress'] = $_SESSION['Progress'] + $StepVal;
			}
			//$_SESSION['Progress'] = isset($_SESSION['Progress']) ? (($_SESSION['Progress'] >= 100) ? 100 : ($_SESSION['Progress'] + 00.67)) : 0.67;
			$_SESSION['PlayGear'] = 1;
		}
		echo(sprintf("%0.2f",$_SESSION['Progress']));
	}
	if (isset($_REQUEST['PlayGear']))
	{
		$_SESSION['PlayGear'] = $_REQUEST['play'];
		echo (int) $_SESSION['PlayGear'];
		//return 0;
	}
	if (isset($_REQUEST['GearStatus']))
	{
		echo (int) $_SESSION['PlayGear'];
		//return 0;
	}
?>