<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
if($has_subquestion) {
    $t *= 2;
}
$opt_count = 0;
if(preg_match('/opt_count=([0-9]+)/', $question->optional, $matches)) {
    $opt_count = $matches[1];
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode));

    for ($l = 1; $l <= $t; $l++) {
        $val = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        if($has_subquestion && !empty($val)) {
            $arr = explode('|', $val);
            $val = $l % 2 == 0 ? $arr[1] : $arr[0];
        }
        ?>
        <div class="plain_rows_4<?= $l > 1 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
            <div class="rows_box">
                <div class="option_clicking_selector division_question_counting_5">
                    <?php
                    for ($i = 1; $i <= $opt_count; $i++) {
                        echo '<a href="#" class="option">' . lang("{$lbl}s{$l}_opt{$i}") . '</a>';
                    }
                    ?>
                </div>
            </div>
            <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= $val ?>">
        </div>
    <?php } ?>
    <?php if($lbl == 'l1q4') : ?>
        <div class="clearfix" style="padding-bottom: 10px;">
            <a class="btn btn-default" id="show_definitions"><?= lang('q_btn_definitions') ?></a>
        </div>
    <?php endif; ?>
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
            $("#question_box_3").setDefault($("#q_3"));
            $("#question_box_4").setDefault($("#q_4"));
            $("#qsubmit_1, #question_box_1, #question_box_2, .sub-btn1").addClass('hidenone');
            $(".sub-btn2, #question_box_3").removeClass('hidenone');
        } else {
            flipTop();
            $("#question_box_2").setDefault($("#q_2"));
            $("#qsubmit_1, #question_box_1").addClass('hidenone');
            $("#qsubmit_2, #question_box_2").removeClass('hidenone');
        }
    }
    
    $(function() {
        $.fn.setDefault = function(field) {
            var arr = field.val().split(",");
            var $this = this;
            $.each(arr, function(i, v) {
                if(v != '' && v != -1) {
                    $this.find(".option_clicking_selector a:eq(" + v + ")").addClass('active');
                }
            });
        };
        
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
                $(selector).find(".option_clicking_selector").each(function() {
                    var active = $(this).find("a.active").index();
                    if(active === -1) {
                        status = 0;
                    }
                    answer.push(active);
                    if(twice_val) {
                        answer.push(active);
                    }
                });
                answer = answer.join('|');
            } else {
                var choise = this.find(".option_clicking_selector a.active").index(),
                    answer = choise === -1 ? '' : choise,
                    status = choise === -1 ? 0 : 1;
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
        
        $('.option_clicking_selector a').click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
        });
        
        $("#question_box_1").setDefault($("#q_1"));
        
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
                if($.trim($("#q_"+n2).val()) != "")
                    return false;
                var predef = $("#question_box_"+n1+" .option_clicking_selector .active").index(),
                    box = $("#question_box_"+n2+" .option_clicking_selector");
                box.find(".active").removeClass('active');
                box.find("a:eq("+predef+")").addClass("active");
            }
            
            $("#question_box_2").setDefault($("#q_2"));
            
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
        
        $("#show_definitions").click(function(e) {
            e.preventDefault();
            $("#definitions").modal("show");
        });
    });
</script>