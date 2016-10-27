<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
if($has_subquestion) {
    $t *= 2;
}

foreach(array("min", "max") as $opt) {
    if(preg_match("/{$opt}=([0-9]+)/i", $question->optional, $matches)) {
        ${$opt} = $matches[1];
    } else {
        ${$opt} = 0;
    }
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'top_class' => "mt_70", 'question_mode' => $question_mode));

    for ($l = 1; $l <= $t; $l++) {  
        $val = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        if(!empty($val)) {
            if($has_subquestion) {
                $arr = explode('|', $val);
                $val = $l % 2 == 0 ? $arr[1] : $arr[0];
            }
            $val = explode(';', $val);
        } else {
            $val = array('', '');
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="rslider range_slider_bg<?= $l > 1 ? " hidenone" : "" ?>" id="range_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div class="range_sliding_bar division_question_counting_30">
                    <input type="text" id="range_<?= $l ?>" class="range_selecting_sliding_bar" />
                    <input type="hidden" name="min<?= $l ?>" id="min<?= $l ?>" value="<?= $val[0] ?>">
                    <input type="hidden" name="max<?= $l ?>" id="max<?= $l ?>" value="<?= $val[1] ?>">
                </div>
                <span class="rs-txt"><?=lang("{$lbl}_slider_txt");?></span>
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>    

<script type="text/javascript" src="<?= base_url('assets/js/ion.rangeSlider.min.js') ?>"></script>
<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>',
        has_subquestion = '<?= $has_subquestion ?>',
        first_click = true,
        MinVal = <?= $min ?>,
        MaxVal = <?= $max ?>,
        arange = MaxVal * 0.2,
        sliders = {};

    function customFlipBack() {
        $("#me, #my-partner").flip(false);
        $("#heading").removeClass('white_bg');
        $("#heading span:first").html("<?=lang("{$lbl}s1_heading")?>").removeClass('hide');
        $("#subheading span:first").html("<?=lang("{$lbl}s1_subheading");?>").removeClass('hide');
        $("#heading span:last").addClass('hide').html("<?=lang("{$lbl}s3_heading")?>");
        $("#subheading span:last").addClass('hide').html("<?=lang("{$lbl}s3_subheading");?>");
        $("#qsubmit_1, #qsubmit_2, #range_box_2, #range_box_3, #range_box_4, .sub-btn2").addClass('hidenone');
        $("#range_box_1, .sub-btn1").removeClass('hidenone');
    }
    
    function setPartnerAnswersDefault() {
        if(has_subquestion) {
            flipTop(customFlipBack);
            $("#qsubmit_1, #range_box_1, #range_box_2, .sub-btn1").addClass('hidenone');
            $("#heading").removeClass('white_bg');
            $("#range_box_3").setDefault(sliders.s3, $("#min3"), $("#max3"));
            $("#range_box_4").setDefault(sliders.s4, $("#min4"), $("#max4"));
            $(".sub-btn2, #range_box_3").removeClass('hidenone');
        } else {
            flipTop();
            $("#qsubmit_1, #range_box_1").addClass('hidenone');
            $("#range_box_2").setDefault(sliders.s2, $("#min2"), $("#max2"));
            $("#qsubmit_2, #range_box_2").removeClass('hidenone');
        }
        first_click = true;
    }

    $(function() {
        $.fn.setDefault = function(slider, min_field, max_field) {
            slider.update({
                from: min_field.val() != '' ? min_field.val() : MinVal,
                to: max_field.val() != '' ? max_field.val() : MinVal
            });
        };
        
        $.fn.saveSelection = function(slider, whose_answer, link) {
            if(has_subquestion) {
                var answer = new Array(), 
                    status = 1,
                    twice_val = false,
                    selector = "";
                if($(this).attr('id') == 'range_box_1') {
                    if($(".sub-btn1").hasClass('hidenone')) {
                        selector = '#range_1, #range_2';
                    } else {
                        selector = '#range_1';
                        twice_val = true;
                    }                        
                } else {
                    if($(".sub-btn2").hasClass('hidenone')) {
                        selector = '#range_3, #range_4';
                    } else {
                        selector = '#range_3';
                        twice_val = true;
                    }
                }
                $(selector).each(function() {
                    var vals = $(this).val().split(';'),
                        min = vals[0],
                        max = vals[1];
                    answer.push($(this).val());
                    if(twice_val) {
                        answer.push($(this).val());
                    }
                    if(max == MinVal <?php if($lbl !== 'l2q4') { echo '|| (max-min) > arange'; } ?>) {
                        status = 0;
                    }
                });
                answer = answer.join('|');
            } else {
                var vals = slider.val().split(';'),
                    min = vals[0],
                    max = vals[1],
                    answer = slider.val(),
                    status = (max > MinVal <?php if($lbl !== 'l2q4') { echo '&& (max-min) <= arange'; } ?>) ? 1 : 0;
            }
            
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer,
                'status' : status
            };
            
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        for (var i = 1; i < <?= $t+1 ?>; i++) {
            $("#range_" + i).ionRangeSlider({
                min: MinVal,
                max: MaxVal,
                from: MinVal,
                to: MinVal,
                type: 'double',
                <?php if($lbl !== 'l2q4') { ?>
                max_interval: arange,
                <?php } ?>
                drag_interval: true,
                onUpdate:function() {
                    Tipped.create('.irs-bar', "click & drag", { containment: 'viewport', position: 'bottom' });
                },
                onChange:function() {
                    $(".tpd-tooltip").remove(); 
                },
                onFinish: function (data) {
                    if(data.from == data.to && first_click) {
                        var sl = data.input.data("ionRangeSlider");
                        sl.update({
                            from : data.from - (arange/4),
                            to : data.to + (arange/4)
                        });
                    } else if(data.from == data.to){
                        data.slider.find('.irs-to').css('visibility', 'visible');
                    }
                    first_click = false;
                }
            });
            sliders['s'+i] = $("#range_" + i).data("ionRangeSlider");
        }
        
        $("#range_box_1").setDefault(sliders.s1, $("#min1"), $("#max1"));
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#range_box_1").saveSelection($("#range_1"), "me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            if(question_mode == 'single') { 
                $("#range_box_1").saveSelection($("#range_1"), "both", this);
            } else {
                $("#range_box_2").saveSelection($("#range_2"), "partner", this);
            }
        });
        
        if(question_mode == 'double') {
            $("#me, #my-partner").flip({'trigger': 'manual'});
        }
        if(has_subquestion) {
            function presetAnswers(n1, n2) {
                if($.trim($("#min"+n2).val()) != "" && $.trim($("#min"+n2).val()) != "")
                    return false;
                var val = $("#range_"+n1).val().split(';');
                $("#min"+n2).val(val[0]);
                $("#max"+n2).val(val[1]);
                $("#range_box_"+n2).setDefault(sliders['s'+n2], $("#min"+n2), $("#max"+n2));
            }
            
            $("#range_box_2").setDefault(sliders.s2, $("#min2"), $("#max2"));
            
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
                $(".sub-btn1, #range_box_1").addClass('hidenone');
                $("#range_box_2").removeClass('hidenone');
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
                $(".sub-btn2, #range_box_3").addClass('hidenone');
                $("#qsubmit_2, #range_box_4").removeClass('hidenone');
                $("#heading").addClass("white_bg");
                $("#heading span:last").html("<?=lang("{$lbl}s4_heading")?>");
                $("#subheading span:last").html("<?=lang("{$lbl}s4_subheading");?>");
            });
        }
    });
</script>