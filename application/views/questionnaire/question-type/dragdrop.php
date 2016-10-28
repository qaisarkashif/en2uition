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

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'top_class' => ($lbl != 'l1q34' ? 'full_length_dd_item' : "")));

    for ($l = 1; $l <= $t; $l++) { 
        $answer = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        if(!empty($answer)) {
            if($has_subquestion) {
                $arr = explode('|', $answer);
                $answer = $l % 2 == 0 ? $arr[1] : $arr[0];
            }
            $items = $answer != 0 ? explode(',', $answer) : array();
        } else {
            $items = array();
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="dd-<?=$opt_count?> plain_rows_4<?= ($lbl == 'l1q34' ? ' padding-top-20' : '') . ($l > 1 ? ' hidenone' : "") ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div id="dvSource<?= $l ?>" class="itemlist dvSource">
                    <?php 
                    for($i = 1; $i <= $opt_count; $i++) {
                        if(!in_array($i, $items)) {
                            echo '<label data-id="' . $i . '">' . lang("{$lbl}s{$l}_dd{$i}") . '</label>';
                        }
                    }
                    ?>
                </div>
                <div id="dvDest<?= $l ?>" class="itemlist dvDest">
                    <?php 
                    foreach($items as $i) {
                        echo '<label data-id="' . $i . '">' . lang("{$lbl}s{$l}_dd{$i}") . '</label>';
                    } 
                    ?>
                </div>
                <div class="dd-none">
                    <label class="<?= $answer == "0" ? 'active' : ''; ?>" data-v="<?= $l ?>"><?= $lbl == 'l1q34' ? lang('q_btn_none') : lang("q_btn_nodeal"); ?></label>
                </div>
                <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= $answer ?>">
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>    

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
            $("#question_box_1").setDefault(3);
            $("#question_box_1").setDefault(4);
            $("#qsubmit_1, #question_box_1, #question_box_2, .sub-btn1").addClass('hidenone');
            $(".sub-btn2, #question_box_3").removeClass('hidenone');
        } else {
            flipTop();
            $("#question_box_1").setDefault(2);
            $("#qsubmit_1, #question_box_1").addClass('hidenone');
            $("#qsubmit_2, #question_box_2").removeClass('hidenone');
        }
    }
    
    $(function() {
        $.fn.setDefault = function(q) {
            $('#question_box_'+q+' .itemlist').sortable({ connectWith: '#question_box_'+q+' .itemlist' });
            if ($("#q_"+q).val() == "0") {
                $( "#question_box_"+q+" .itemlist" ).sortable( "option", "disabled", true );
            }
        };
        
        $.fn.saveSelection = function(q, whose_answer, link) {
            var answer = new Array();
            if(has_subquestion) {
                var answer = {0: new Array(), 1:new Array()},
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
                $(selector).each(function(i) {
                    if($(this).find('.dd-none label').hasClass('active')) {
                        answer[i].push(0);
                    } else {
                        $(this).find("[id^='dvDest'] label").each(function() {
                            answer[i].push($(this).attr('data-id'));
                        });
                    }
                    if(answer[i].length == 0) {
                        status = 0;
                    }
                });
                answer = twice_val ? answer[0].join(',') + '|' + answer[0].join(',') : answer[0].join(',') + '|' + answer[1].join(',');
            } else {
                if(this.find('.dd-none label').hasClass('active')) {
                    answer.push(0);
                } else {
                    $("#dvDest"+q+" label").each(function() {
                        answer.push($(this).attr('data-id'));
                    });
                }
                var status = answer.length ? 1 : 0;
                answer = answer.join(',');
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
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection(1, "me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            if(question_mode == 'single') { 
                $("#question_box_1").saveSelection(1, "both", this);
            } else {
                $("#question_box_2").saveSelection(2, "partner", this);
            }
        });
        
        $(".dd-none label").click(function() {
            $(this).toggleClass('active');
            var q = $(this).attr('data-v');
            if($(this).hasClass('active')) {
                $("#dvSource"+q).append($("#dvDest"+q).html());
                $("#dvDest"+q+" label").remove();
            }
            $( "#question_box_"+q+" .itemlist" ).sortable( "option", "disabled", $(this).hasClass('active') );
            return false;
        });
        
        $("#question_box_1").setDefault(1);
        
        if(has_subquestion) {
            function presetAnswers(n1, n2) {
                if($.trim($("#q_"+n2).val()) != '')
                    return false;
                if($("#question_box_"+n1+" .dd-none .active").length) {
                    $("#question_box_"+n2+" .dd-none label").addClass('active');
                    $("#q_"+n2).val(0);
                } else {
                    $("#question_box_"+n2+" .dd-none label").removeClass('active');
                    $("#q_"+n2).val('');
                }
                $("#dvSource"+n2).html($("#dvSource"+n1).html());
                $("#dvDest"+n2).html($("#dvDest"+n1).html());
                $("#question_box_2").setDefault(n2);
            }
            
            $("#question_box_2").setDefault(2);
            
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