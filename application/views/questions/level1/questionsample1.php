<div id="q_container" class="scene_element--noexiting">
	<h3 class="question_heading">Age</h3>
	<div class="me_mypartner_option">
		<ul>
			<li><a href="#">Me</a></li>
			<li><a href="#" class="active">Me</a></li>
			<li><a href="#">My Partner</a></li>
		</ul>
	</div>
	<div class="row">
		<div class="qbox col-md-4 col-md-push-4 col-sm-6 col-sm-push-3 col-xs-12">
			<!--my_age_in_start_relation-->
			<div class="question_1_box" id="my_age_in_start_relation">
				<div class="heading">On the day me and my partner met</div>
				<div class="detail">my age was:</div>
				<div class="option_selector_box scroller">
					<a href="#" class="go_up division_question_counting_1"><i class="fa fa-caret-up"></i></a>
					<div class="option_selector">
						<a href="#" class="option">&nbsp;</a>
						<a href="#" class="option">&nbsp;</a>
						<a href="#" class="option active">&nbsp;</a>
						<a href="#" class="option">&nbsp;</a>
						<a href="#" class="option">&nbsp;</a>
					</div>
					<a href="#" class="go_down division_question_counting_1"><i class="fa fa-caret-down"></i></a>
				</div>
			</div>
			<!--End my_age_in_start_relation-->

			<!--my_different_age_in_end_relation-->
			<div class="question_1_box" id="my_different_age_in_end_relation">
				<div class="heading white_bg">At the end of our dating relationship</div>
				<div class="detail">my age was:</div>
				<div class="option_selector_box scroller">
					<a href="#" class="go_up division_question_counting_2"><i class="fa fa-caret-up"></i></a>
					<div class="option_selector">
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option active"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
					</div>
					<a href="#" class="go_down division_question_counting_2"><i class="fa fa-caret-down"></i></a>
				</div>
			</div>
			<!--End my_different_age_in_end_relation-->

			<!--my_partner_age_in_start_relation-->
			<div class="question_1_box" id="my_partner_age_in_start_relation">
				<div class="heading">On the day me and my partner met</div>
				<div class="detail">my partner's age was:</div>
				<div class="option_selector_box scroller">
					<a href="#" class="go_up division_question_counting_3"><i class="fa fa-caret-up"></i></a>
					<div class="option_selector">
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option active"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
					</div>
					<a href="#" class="go_down division_question_counting_3"><i class="fa fa-caret-down"></i></a>
				</div>
			</div>
			<!--End my_partner_age_in_start_relation-->

			<!--my_partner_different_age_in_end_relation-->
			<div class="question_1_box" id="my_partner_different_age_in_end_relation">
				<div class="heading white_bg">At the end of our dating relationship</div>
				<div class="detail">my partner's age was:</div>
				<div class="option_selector_box scroller">
					<a href="#" class="go_up division_question_counting_4"><i class="fa fa-caret-up"></i></a>
					<div class="option_selector">
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option active"></a>
						<a href="#" class="option"></a>
						<a href="#" class="option"></a>
					</div>
					<a href="#" class="go_down division_question_counting_4"><i class="fa fa-caret-down"></i></a>
				</div>
			</div>
			<!--End my_partner_different_age_in_end_relation-->
		</div>

		<!--<div class="clearfix"></div>-->
		<div class="process_answer">
			<div class="boolean_question clearfix text-center" id="my_age">
				<div class="col-md-12 col-xs-12">
					<h4>Was this different at the end of your dating relationship</h4>
				</div>
				<div class="col-sm-12 col-xs-12">
					<a href="#" class="boolean_question_yes" id="submit_p1">Yes</a>
					<a href="#" class="boolean_question_no" id="submit_no1">No</a>
				</div>
			</div>
			<div class="boolean_question_submit clearfix text-center" id="first_question_submit_1">
				<div class="col-xs-12">
					<a href="#" class="boolean_question_submit_button" id="submit_p2">Submit and choose your partner age</a>
				</div>
			</div>
			<div class="boolean_question clearfix text-center" id="my_partner_age">
				<div class="col-md-12 col-xs-12">
					<h4>Was this different at the end of your dating relationship?</h4>
				</div>
				<div class="col-sm-12 col-xs-12">
					<a href="#" class="boolean_question_yes" id="submit_p3">Yes</a>
					<a href="question2.php" class="boolean_question_no" id="submit_no">No</a>
				</div>
			</div>
			<div class="boolean_question_submit clearfix text-center" id="first_question_submit_2">
				<div class="col-xs-12">
					<a href="#" class="saveandexit_button">Save and exit</a>
					<a href="question2.php" class="boolean_next_question nextques submit_p4">Next question</a>
				</div>
			</div>
		</div>
		<input type="hidden" name="scrollval" id="scrollval" />
	</div>
	<!--end row-->

	<!--Privacy changing-->
	<div class="privacy_area">
		<p>Privacy:&nbsp;</p>
		<a href="#">Low</a>
		<a href="#">Medium</a>
		<a href="#" class="active">High</a>
	</div>
	<div class="clearfix"></div>
	<!--pagination question-->
	<div class="pagination_area">
		<a href="#" class="full_step_backward"><i class="fa fa-step-backward"></i></a>
		<a href="#" class="step_backward"><i class="fa fa-caret-left"></i></a>
		<p>Question: <input class="variable_page_number" type="text" value="1"> of &nbsp;&nbsp;<?=$this->session->userdata['level_info']['questions'];?>  </p>
		<a href="question2.php" class="step_forward"><i class="fa fa-caret-right"></i></a>
		<a href="question53.php" class="full_step_forward"><i class="fa fa-step-forward"></i></a>
	</div>

	<!--circular_progress_bar-->
	<div class="circular_progress_bar">
		<label class="q-level">Level 1</label>
		<div class="circular_bar_inner_bg"></div>
		<input class="knob" data-thickness=".2" data-step="0.67" data-width="100" value="<?=isset($_SESSION['Progress']) ? $_SESSION['Progress'] : 0;?>" data-fgColor="#2d87d9" data-bgColor ="#e6e6e6" data-inputColor="#6794cc" data-fontWeight="normal">
	</div>
</div>
<script language="javascript">
var minval = 0;
var maxval = 120;
var QType = "Scroller";
var Clicked = 0;
</script>
<script language="javascript">
$(document).ready(function(e) {
    $("#submit_p1").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_age_in_start_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_age_in_start_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,1,1,UrlString);
    });
    $("#submit_no1").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_age_in_start_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_age_in_start_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&boolean=No";
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,1,1,UrlString);
    });
    $("#submit_p2").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_different_age_in_end_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_different_age_in_end_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,2,1,UrlString);
    });
    $("#submit_p3").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_partner_age_in_start_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_partner_age_in_start_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,3,1,UrlString);
    });
    $("#submit_no").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_partner_age_in_start_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_partner_age_in_start_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&boolean=No";
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,3,1,UrlString);
    });
    $(".submit_p4").click(function(e) {
        var UrlString = "";
        UrlString = UrlString + "question_heading="+$("#my_partner_different_age_in_end_relation .heading").html();
        UrlString = UrlString + "&question="+$("#my_partner_different_age_in_end_relation .detail").html();
        UrlString = UrlString + "&answer="+$("#scrollval").val();
        UrlString = UrlString + "&privacy="+$(".privacy_area a.active").html();
        SaveAnswer(1,4,1,UrlString);
    });
});
</script>
