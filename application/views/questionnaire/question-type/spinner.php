<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
if($has_subquestion) {
    $t *= 2;
}
$def = array(
    "type" => 'boolean', 
    "opt_count" => 4,
    "min_val" => -1,
    "max_val" => 1
);

foreach(array("opt_count", "min_val", "max_val") as $var) {
    if(preg_match("/{$var}=([0-9]+)/i", $question->optional,$matches)) {
        ${$var} = $matches[1];
    } else {
        ${$var} = $def[$var];
    }
}

if(preg_match("/type=([a-z]+)/", $question->optional, $matches)) {
    $type = $matches[1];
} else {
    $type = $def['type'];
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode));

    for ($l = 1; $l <= $t; $l++) {
        $answer = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        $tmp = array_filter(explode(";", str_replace("|", ";", $answer)));
        $vals = array();
        foreach($tmp as $item) {
            $val = explode('=', $item);
            $vals[$val[0]] = $val[1];
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="plain_rows_4<?= $l > 1 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div class="rows_box">
                    <div class="spinner_box division_question_counting_5">
                        <ul class="spinners<?= $type == "boolean" ? " boolean_spinner" : "" ?>">
                            <?php
                            for ($i = 1; $i <= $opt_count; $i++) {
                                $input_id = "q{$l}v{$i}";
                                ?>
                                <li>
                                    <div class="option" id="<?= "option{$l}_{$i}" ?>"><?= lang("{$lbl}s{$l}_spin{$i}"); ?></div>
                                    <input id="<?= $input_id ?>" class="spinner" value="<?= isset($vals[$input_id]) ? $vals[$input_id] : "-" ?>" readonly />
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>

<script type="text/javascript" src="<?= base_url('assets/js/jquery.ui.spinner.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.mousewheel.min.js') ?>"></script>
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
        $(document).on('mousewheel', '.ui-spinner-button', function(event, delta, deltaX, deltaY) {
            var spin_box = $(this).parent().find('input'),
                step = delta > 0 ? "Up" : "Down";
            if(spin_box.hasClass('bspinner')) { spin_box.boolspinner( "step" + step ); } 
            else { spin_box.spinner( "stepUp" ); }
            event.stopPropagation();
            event.preventDefault();
        });
        
        var min_val = <?= $min_val ?>,
            max_val = <?= $max_val ?>;
    
        <?php if($type == "boolean") : ?>
            var txt_yes = "<?= lang("q_btn_yes") ?>",
                txt_no = "<?= lang("q_btn_no") ?>";
        $.widget("ui.spinner", $.ui.spinner, {
			options: {
				min: min_val,
				max: max_val,
				step: 1,
				start: -1,
				value: -1,
                mouseWheel: true,
                spin: function( event, ui ) {
                    event.preventDefault();
                    switch($(this).val()) {
                        case txt_yes : if(ui.value == 1) { $(this).spinner("value", 0); } else if(ui.value == 0) { $(this).spinner("value", -1); } break;
                        case txt_no : if(ui.value == 1) { $(this).spinner("value", -1); } else if(ui.value == -1) { $(this).spinner("value", 1); } break;
                        case '-' : if(ui.value == 0) { $(this).spinner("value", 1); } else if(ui.value == -1) { $(this).spinner("value", 0); } break;
                    }
                }
			},
			_parse: function (value) {
				if (typeof value === "string") 
				{
					if (value == "-")
						return -1;
					else if (value == txt_no)
						return 0;
					else if (value == txt_yes)
						return 1;
				}
				return value;
			},
			_format: function (value) {
				if (value < 0)
					return "-";
				else
					return (value == 1) ? txt_yes : txt_no;
			}
		});
        <?php else : ?>
            $.widget("ui.spinner", $.ui.spinner, {
                options: {
                    min: min_val,
                    max: max_val,
                    value: -1,
                    start: -1,
                    step: 1,
                    numberFormat: "n",
                    mouseWheel: true,
                    spin: function( event, ui ) {
                        var mval = $(this).attr('aria-valuemax');
                        if($(this).val() == '-' && ui.value == -1) {
                            event.preventDefault();
                            $(this).spinner("value", mval);
                        } else if ($(this).val() == mval && ui.value == mval){
                            event.preventDefault();
                            $(this).spinner("value", -1);
                        }
                    }
                },
                _format: function (value) { return (value == -1)?"-":value; }
            });
        <?php endif; ?>
        $.fn.saveSelection = function(whose_answer, link) {
            if(has_subquestion) {
                var answer = {0:new Array(), 1:new Array()},
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
                    $(this).find(".spinner").each(function() {
                        if($(this).attr('aria-valuenow') >= 0) {
                            var choise = $(this).attr('id') + '=' + $(this).attr('aria-valuenow');
                            answer[i].push(choise);
                        }
                    });
                });
                var spin_count = $(selector).find(".spinners li").length;
                var l = twice_val ? answer[0].length : (answer[0].length + answer[1].length);
                answer = twice_val ? answer[0].join(';') + '|' + answer[0].join(';') : answer[0].join(';') + '|' + answer[1].join(';');
                var status = l == spin_count ? 1 : 0;
            } else {
                var answer = new Array(),
                    spin_count = this.find(".spinners li").length;
                this.find('.spinner').each(function() {
                    if($(this).attr('aria-valuenow') >= 0) {
                        answer.push($(this).attr('id') + '=' + $(this).attr('aria-valuenow'));
                    }
                });
                var status = answer.length == spin_count ? 1 : 0;
                answer = answer.join(";");
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
        
        <?php if($lbl == 'l1q50') : ?>
            $('#q1v1, #q2v1').spinner({ min: -1, max: 7});
            $('#q1v2, #q2v2').spinner({ min: -1, max: 5});
        <?php else : ?>
            $(".spinner").spinner();
        <?php endif; ?>
            
        $(".ui-spinner-button").click(function(e) { e.preventDefault(); });
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
                var box1 = $("#question_box_"+n1+" .spinners"),
                    box2 = $("#question_box_"+n2+" .spinners"),
                    exit = false;
            
                box2.find(".spinner").each(function() {
                    if($(this).attr('aria-valuenow') >= 0) {
                        exit = true;
                        return false;
                    }
                });
                if(exit) return false;
                box1.find(".spinner").each(function() {
                    var i = $(this).closest('li').index(),
                        v = $(this).attr("aria-valuenow");
                    box2
                        .find("li:eq("+i+") .spinner")
                        .spinner( "value", v !== undefined ? v : -1 );
                });
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