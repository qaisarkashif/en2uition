<?php
$lbl = $question->label;
$min_val = -1;
$max_val = 5;
$yn = $opt = '';
$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl));

    for ($l = 1; $l <= 2; $l++) {
        $answer = $l == 2 ? $question->partner : $question->me;
        if(preg_match("/yn=([YN]{1})/", $answer, $matches)) {
            $yn = $matches[1];
        }
        if(preg_match("/opt=(c|spin)/", $answer, $matches)) {
            $opt = $matches[1];
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">            
            <div class="range_slider_bg<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <input type="hidden" class="q_yn" value="<?= $yn ?>"/>
                <div class="boolean_question centered shadow choose_yes_no col-sm-12 col-xs-12">
                    <a class="boolean_question_yes" data-id="Y"><?= lang("q_btn_yes"); ?></a>
                    <a class="boolean_question_no" data-id="N"><?= lang("q_btn_no"); ?></a>
                </div>
                <div class="op_rows hidenone">
                    <div class="detail"><?= lang("{$lbl}s{$l}_subheading2"); ?></div>
                    <div class="rows_box">
                        <div class="option_clicking_selector division_question_counting_5">
                            <a class="option<?= $opt == 'spin' ? ' active' : '' ?>" data-v="spin"><?= lang("{$lbl}s{$l}_opt1"); ?></a>
                            <a class="option<?= $opt == 'c' ? ' active' : '' ?>" data-v="c"><?= lang("{$lbl}s{$l}_opt2"); ?></a>
                        </div>
                    </div>
                </div>
                <?php 
                for($k = 1; $k <= 2; $k++) { 
                    if(preg_match("/spin{$k}=([0-9]+)/", $answer, $matches)) {
                        ${"spin{$k}"} = $matches[1];
                    } else {
                        ${"spin{$k}"} = '-';
                    }
                    ?>
                    <div class="hidenone spinner_row<?= $k ?>">
                        <div class="detail"><?= lang("{$lbl}s{$l}_subheading3"); ?></div>
                        <div class="rows_box">
                            <div class="spinner_box division_question_counting_5">
                                <ul class="spinners">
                                    <li>
                                        <div class="option" id="option<?= "{$l}_{$k}"?>"><?= lang("{$lbl}s{$l}_spin{$l}"); ?></div>
                                        <input id="<?= "q{$l}v{$k}" ?>" class="spinner" name="<?= "q{$l}v{$k}" ?>" value="<?= ${"spin{$k}"} ?>" readonly />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>                
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
        $("#question_box_2").setDefault();
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
            max_val = <?= $max_val ?>;
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
        
        $.fn.setDefault = function() {
            var yn = this.find(".q_yn").val();
            this.find('.choose_yes_no a[data-id="'+yn+'"]').removeClass('active').click();
            if(yn == 'N' && this.find('.op_rows .active').length) {
                this.find('.op_rows .active').removeClass('active').click();
            }
        };
        
        $.fn.saveSelection = function(whose_answer, link) {
            var answer = new Array(), 
                yn = this.find(".q_yn").val(),
                status = 0;
            if(yn != '') {
                answer.push("yn="+yn);
            }
            if(yn == 'Y') {
                var spin = this.find('.spinner_row1 .spinner').attr('aria-valuenow');
                if(spin >= 0) {
                    answer.push('spin1='+spin);
                    status = 1;
                }
            } else if(yn == 'N' && this.find('.op_rows .active').length) {
                var choise = this.find('.op_rows .active').attr('data-v');
                var spin = this.find('.spinner_row2 .spinner').attr('aria-valuenow');
                if(choise == 'c') {
                    answer.push('opt=c');
                    status = 1;
                } else if(choise == 'spin' && spin >= 0) {
                    answer.push('opt=spin#spin2='+spin);
                    status = 1;
                }
            }
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer.join("#"),
                'status' : status
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        $(".spinner").spinner();
        $("#me, #my-partner").flip({'trigger': 'manual'});
        $(".ui-spinner-button").click(function(e) { e.preventDefault(); });
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection("me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            $("#question_box_2").saveSelection("partner", this);
        });
        
        $(".choose_yes_no a").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
            var choise = '';
            if($(this).hasClass('active')) {
                choise = $(this).attr('data-id');
            }
            var box = $(this).closest('[id^="question_box_"]');
            box.find('.q_yn').val(choise);
            if(choise == 'Y') {
                box.find('.op_rows, .spinner_row2').addClass('hidenone');
                box.find('.spinner_row1').removeClass('hidenone');
            } else if(choise == 'N') {
                box.find('.spinner_row1').addClass('hidenone');
                var opts = box.find('.op_rows');
                opts.removeClass('hidenone');
                if(opts.find('.active').length && opts.find('.active').attr('data-v') == "spin") {
                    box.find('.spinner_row2').removeClass('hidenone');
                } else {
                    box.find('.spinner_row2').addClass('hidenone');
                }
            } else {
                box.find('.spinner_row1, .spinner_row2, .op_rows').addClass('hidenone');
            }
        });
        
        $(".op_rows a").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
            var box = $(this).closest('[id^="question_box_"]');
            if($(this).hasClass('active') && $(this).attr('data-v') == 'spin') {
                box.find('.spinner_row2').removeClass('hidenone');
            } else {
                box.find('.spinner_row2').addClass('hidenone');
            }
        });
        
        $("#question_box_1").setDefault();
    });
</script>