<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
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
        $answer = $l == 2 ? $question->partner : $question->me;
        if(preg_match("/yn=([YN]{1})/", $answer, $matches)) {
            $yn = $matches[1];
        } else {
            $yn = '';
        }
        $tmp = array_filter(explode(";", $answer));
        $vals = array();
        foreach($tmp as $item) {
            $val = explode('=', $item);
            if($val[0] != 'yn') {
                $vals[$val[0]] = $val[1];
            }
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="plain_rows_4 padding-top-30<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div class="boolean_question centered shadow choose_yes_no col-sm-12 col-xs-12">
                    <a class="boolean_question_yes <?= $yn == "Y" ? "active" : "" ?>" data-v="Y"><?= lang("q_btn_yes"); ?></a>
                    <a class="boolean_question_no <?= $yn == "N" ? "active" : "" ?>" data-v="N"><?= lang("q_btn_no"); ?></a>
                </div>
                <div class="clear"></div>
                <div class="op_rows<?= $yn == 'Y' ? "" : " hidenone" ?>">
                    <div class="detail" id="detail2"><?= lang("{$lbl}s{$l}_subheading2"); ?></div>
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
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>

<script type="text/javascript" src="<?= base_url('assets/js/jquery.ui.spinner.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.mousewheel.min.js') ?>"></script>
<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>';
    
    function setPartnerAnswersDefault() {
        flipTop();
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
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
            max_val = <?= $max_val ?>,
            txt_yes = "<?= lang("q_btn_yes") ?>",
            txt_no = "<?= lang("q_btn_no") ?>";
    
        $.widget("ui.boolspinner", $.ui.spinner, {
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
                        case txt_yes : if(ui.value == 1) { $(this).boolspinner("value", 0); } else if(ui.value == 0) { $(this).boolspinner("value", -1); } break;
                        case txt_no : if(ui.value == 1) { $(this).boolspinner("value", -1); } else if(ui.value == -1) { $(this).boolspinner("value", 1); } break;
                        case '-' : if(ui.value == 0) { $(this).boolspinner("value", 1); } else if(ui.value == -1) { $(this).boolspinner("value", 0); } break;
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
                    if($(this).val() == '-' && ui.value == -1) {
                        event.preventDefault();
                        $(this).spinner("value", max_val);
                    } else if ($(this).val() == max_val && ui.value == max_val){
                        event.preventDefault();
                        $(this).spinner("value", -1);
                    }
                }
            },
            _format: function (value) { return (value == -1)?"-":value; } 
        });
        
        $.fn.saveSelection = function(whose_answer, link) {
            var answer = new Array(),
                spin_count = this.find(".spinners li").length,
                yn = this.find('.choose_yes_no a.active'),
                status = 0;
            if(yn.length) {
                if(yn.attr('data-v') == 'Y') {
                    this.find('.spinner').each(function() {
                        if($(this).attr('aria-valuenow') >= 0) {
                            answer.push($(this).attr('id') + '=' + $(this).attr('aria-valuenow'));
                        }
                    });
                    status = answer.length == spin_count ? 1 : 0;
                } else if(yn.attr('data-v') == 'N') {
                    status = 1;
                }
                answer.push('yn='+yn.attr('data-v'));
            }
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer.join(";"),
                'status' : status
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        $(".spinner").spinner();
        $(".bspinner").boolspinner();        
        if(question_mode == 'double') {
            $("#me, #my-partner").flip({'trigger': 'manual'});
        }        
        $(".ui-spinner-button").click(function(e) { e.preventDefault(); });
        
        $(".choose_yes_no a").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
            var op_rows = $(this).closest('[id^="question_box_"]').find(".op_rows");
            if($(this).hasClass('active') && $(this).attr('data-v') == 'Y') {
                op_rows.removeClass('hidenone');
            } else {
                op_rows.addClass('hidenone');
            }
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
    });
</script>