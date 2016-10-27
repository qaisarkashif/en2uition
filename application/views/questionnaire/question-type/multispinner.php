<?php
$lbl = $question->label;

$def = array( 
    "opt_count" => 4,
    "min_val" => -1,
    "max_val" => 1
);

for($p = 1; $p <= 2; $p++) {
    foreach(array("opt_count", "min_val", "max_val") as $var) {
        if(preg_match("/{$var}{$p}=([0-9]+)/i", $question->optional,$matches)) {
            ${$var.$p} = $matches[1];
        } else {
            ${$var.$p} = $def[$var];
        }
    }
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl));

    for ($l = 1; $l <= 2; $l++) {
        $tmp = array_filter(explode(";", $question->{$l == 1 ? "me" : "partner"}));
        $vals = array();
        foreach($tmp as $item) {
            $val = explode('=', $item);
            $vals[$val[0]] = $val[1];
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="plain_rows_4<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div class="rows_box">
                    <div class="spinner_box division_question_counting_5">
                        <ul class="spinners">
                            <?php
                            for ($i = 1; $i <= $opt_count1; $i++) {
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
                <div class="detail"><?=lang("{$lbl}s{$l}_subheading2");?></div>
                <div class="rows_box">
                    <div class="spinner_box division_question_counting_5">
                        <ul class="spinners boolean_spinner">
                            <?php
                            for ($i2 = $i; $i2 < $opt_count2 + $i; $i2++) {
                                $input_id = "q{$l}v{$i2}";
                                ?>
                                <li>
                                    <div class="option" id="<?= "option{$l}_{$i2}" ?>"><?= lang("{$lbl}s{$l}_spin{$i2}"); ?></div>
                                    <input id="<?= $input_id ?>" class="bspinner" value="<?= isset($vals[$input_id]) ? $vals[$input_id] : "-" ?>" readonly />
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>

<script type="text/javascript" src="<?= base_url('assets/js/jquery.ui.spinner.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.mousewheel.min.js') ?>"></script>
<script type="text/javascript">
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
        
        var min_val1 = <?= $min_val1 ?>,
            max_val1 = <?= $max_val1 ?>,
            min_val2 = <?= $min_val2 ?>,
            max_val2 = <?= $max_val2 ?>,
            txt_yes = "<?= lang("q_btn_yes") ?>",
            txt_no = "<?= lang("q_btn_no") ?>";
    
        $.widget("ui.boolspinner", $.ui.spinner, {
			options: {
				min: min_val2,
				max: max_val2,
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
                min: min_val1,
				max: max_val1,
                value: -1,
                start: -1,
                step: 1,
                numberFormat: "n",
                mouseWheel: true,
                spin: function( event, ui ) {
                    if($(this).val() == '-' && ui.value == -1) {
                        event.preventDefault();
                        $(this).spinner("value", max_val1);
                    } else if ($(this).val() == max_val1 && ui.value == max_val1){
                        event.preventDefault();
                        $(this).spinner("value", -1);
                    }
                }
            },
            _format: function (value) { return (value == -1)?"-":value; } 
        });
        
        $.fn.saveSelection = function(whose_answer, link) {
            var answer = new Array();
            this.find('.spinner, .bspinner').each(function() {
                if($(this).attr('aria-valuenow') >= 0) {
                    answer.push($(this).attr('id') + '=' + $(this).attr('aria-valuenow'));
                }
            });
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer.join(";"),
                'status' : answer.length == this.find('.spinner, .bspinner').length ? 1 : 0
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        $("#me, #my-partner").flip({'trigger': 'manual'});
        $(".spinner").spinner();
        $(".bspinner").boolspinner();
        $(".ui-spinner-button").click(function(e) { e.preventDefault(); });
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection("me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            $("#question_box_2").saveSelection("partner", this);
        });
    });
</script>