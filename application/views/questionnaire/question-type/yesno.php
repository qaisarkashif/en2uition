<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
if($has_subquestion) {
    $t *= 2;
}
$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode));

    for ($l = 1; $l <= $t; $l++) {
        $answer = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        if($has_subquestion) {
            if(!empty($answer)) {
                $arr = explode('|', $answer);
                $answer = $l % 2 == 0 ? $arr[1] : $arr[0];
            }
        }
        ?>         
        <div class="plain_rows_4 padding-top-30<?= $l > 1 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
            <div class="boolean_question centered shadow choose_yes_no col-sm-12 col-xs-12">
                <a class="boolean_question_yes <?= $answer == 'Y' ? "active" : "" ?>" data-v="Y"><?= lang("q_btn_yes") ?></a>
                <a class="boolean_question_no <?= $answer == 'N' ? "active" : "" ?>" data-v="N"><?= lang("q_btn_no") ?></a>
            </div>
        </div>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>

<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>',
        has_subquestion = '<?= $has_subquestion ?>';
    
    function customFlipBack() {
        $("#me, #my-partner").flip(false);
        $("#heading").removeClass('white_bg');
        $("#heading span:first").html("<?=lang("{$lbl}s1_heading")?>").removeClass('hide');
        $("#subheading span:first").html("<?=lang("{$lbl}s1_subheading");?>").removeClass('hide');
        $("#heading span:last").addClass('hide').html("<?=lang("{$lbl}s3_heading")?>");
        $("#subheading span:last").addClass('hide').html("<?=lang("{$lbl}s3_subheading");?>");
        $("#qsubmit_1, #qsubmit_2, #question_box_2, #question_box_3, #question_box_4, .sub-btn2").addClass('hidenone');
        $("#question_box_1, .sub-btn1").removeClass('hidenone');
    }
    
    function setPartnerAnswersDefault() {
        if(has_subquestion) {
            flipTop(customFlipBack);
            $("#heading").removeClass('white_bg');
            $("#qsubmit_1, #question_box_1, #question_box_2, .sub-btn1").addClass('hidenone');
            $(".sub-btn2, #question_box_3").removeClass('hidenone');
        } else {
            flipTop();
            $("#qsubmit_1, #question_box_1").addClass('hidenone');
            $("#qsubmit_2, #question_box_2").removeClass('hidenone');
        }
    }
    
    $(function() {        
        $.fn.saveSelection = function(whose_answer, link) {
            if(has_subquestion) {
                var answer = new Array(), 
                    status = 1,
                    twice_val = false,
                    selector = "";
                if($(this).attr('id') == 'question_box_1') {
                    if($(".sub-btn1").hasClass('hidenone')) {
                        selector = '#question_box_1, #question_box_2';
                    } else {
                        selector = '#question_box_1';
                        twice_val = true;
                    }                        
                } else {
                    if($(".sub-btn2").hasClass('hidenone')) {
                        selector = '#question_box_3, #question_box_4';
                    } else {
                        selector = '#question_box_3';
                        twice_val = true;
                    }
                }
                $(selector).find(".choose_yes_no").each(function() {
                    var active = $(this).find("a.active");
                    if(!active.length) {
                        status = 0;
                        var choise = "-";
                    } else {
                        choise = active.attr('data-v'); 
                    }
                    answer.push(choise);
                    if(twice_val) {
                        answer.push(choise);
                    }
                });
                answer = answer.join('|');
            } else {
                var choise = this.find('.choose_yes_no a.active'),
                    answer = choise.length ? choise.attr('data-v') : '',
                    status = choise.length ? 1 : 0;
            }
            
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer,
                'status' : status
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        if(question_mode == 'double') {
            $("#me, #my-partner").flip({'trigger': 'manual'});
        }
        
        $(".choose_yes_no a").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
        });
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection("me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            if(question_mode == 'single') { 
                $("#question_box_1").saveSelection("both", this);
            } else {
                $("#question_box_2").saveSelection("partner", this);
            }
        });
        
        if(has_subquestion) {
            function presetAnswers(n1, n2) {
                var box1 = $("#question_box_"+n1+ " .boolean_question"),
                    box2 = $("#question_box_"+n2+ " .boolean_question");
                if(box2.find(".active").length)
                    return false;
                var i = box1.find('.active').attr('data-v');
                box2.find('[data-v='+i+']').addClass("active");
            }
            
            $("#submit_no1").click(function(e) {
                e.preventDefault();
                if(question_mode == 'single') {
                    $(".q2:last").click();
                } else {
                    $(".q1").click();
                }
            });
            
            $("#submit_no2").click(function(e) {
                e.preventDefault();
                $(".q2:last").click();
            });
            
            $("#submit_p1").click(function() {
                presetAnswers(1, 2);
                $(".sub-btn1, #question_box_1").addClass('hidenone');
                $("#question_box_2").removeClass('hidenone');
                if(question_mode == 'single') {
                    $("#qsubmit_2").removeClass('hidenone');
                } else {
                    $("#qsubmit_1").removeClass('hidenone');
                }
                $("#heading").addClass("white_bg");
                $("#heading span:first").html("<?=lang("{$lbl}s2_heading")?>");
                $("#subheading span:first").html("<?=lang("{$lbl}s2_subheading");?>");
            });
            
            $("#submit_p2").click(function() {
                presetAnswers(3, 4);
                $(".sub-btn2, #question_box_3").addClass('hidenone');
                $("#qsubmit_2, #question_box_4").removeClass('hidenone');
                $("#heading").addClass("white_bg");
                $("#heading span:last").html("<?=lang("{$lbl}s4_heading")?>");
                $("#subheading span:last").html("<?=lang("{$lbl}s4_subheading");?>");
            });
        }
        
    });
</script>