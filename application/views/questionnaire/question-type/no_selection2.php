<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
$min_val = -1;
$max_val = 5;
$yn = $opt = '';
$opt_count = 4;
if(preg_match('/opt_count=([0-9]+)/', $question->optional, $matches)) {
    $opt_count = $matches[1];
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode, 'top_class' => 'sq9'));

    for ($l = 1; $l <= $t; $l++) {
        $answer = $l == 2 ? $question->partner : $question->me;
        if(preg_match("/yn=([YN]{1})/", $answer, $matches)) {
            $yn = $matches[1];
        }
        if($yn == 'Y') {
            if(preg_match('/opt=([0-9]+)/', $answer, $matches)) {
                $opt = $matches[1] + 1;
            }
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">            
            <div class="plain_rows_4 padding-top-30<?= $l > 1 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <input type="hidden" class="q_yn" value="<?= $yn ?>"/>
                <div class="boolean_question centered shadow choose_yes_no col-sm-12 col-xs-12">
                    <a class="boolean_question_yes" data-id="Y"><?= lang("q_btn_yes"); ?></a>
                    <a class="boolean_question_no" data-id="N"><?= lang("q_btn_no"); ?></a>
                </div>
                <div class="op_rows hidenone">
                    <div class="detail"><?= lang("{$lbl}s{$l}_subheading2"); ?></div>
                    <div class="rows_box rows_box1">
                        <div class="option_clicking_selector division_question_counting_5">
                            <?php
                            for ($i = 1; $i <= $opt_count; $i++) {
                                echo '<a href="#" class="option' . ($i == $opt ? " active" : "") . '">' . lang("{$lbl}s{$l}_opt{$i}") . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php 
                for($k = 1; $k <= 1; $k++) { 
                    if(preg_match("/spin{$k}=([0-9]+)/", $answer, $matches)) {
                        ${"spin{$k}"} = $matches[1];
                    } else {
                        ${"spin{$k}"} = '-';
                    }
                    ?>
                    <div class="hidenone spinner_row<?= $k ?>">
                        <div class="detail"><?= lang("{$lbl}s{$l}_subheading3"); ?></div>
                        <div class="rows_box rows_box1">
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

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>

<script type="text/javascript" src="<?= base_url('assets/js/jquery.ui.spinner.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.mousewheel.min.js') ?>"></script>
<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>';
    
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
        };
        
        $.fn.saveSelection = function(whose_answer, link) {
            var answer = new Array(), 
                yn = this.find(".q_yn").val(),
                status = 0;
            if(yn == 'Y' || yn == 'N') {
                answer.push("yn="+yn);
                if(yn == 'Y') {
                    var l = 3;
                    var spin = this.find('.spinner_row1 .spinner').attr('aria-valuenow');
                    if(spin >= 0) {
                        answer.push('spin1='+spin);
                    }
                    var choise = this.find(".option_clicking_selector a.active").index();
                    if(choise !== -1) {
                        answer.push('opt='+choise);
                    }
                } else {
                    var l = 1;
                }
                status = answer.length == l ? 1 : 0;
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
        $(".ui-spinner-button").click(function(e) { e.preventDefault(); });
        
        if(question_mode == 'double') {
            $("#me, #my-partner").flip({'trigger': 'manual'});
        }
        
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
                box.find('.op_rows, .spinner_row1').removeClass('hidenone');
            } else if(choise == 'N') {
                box.find('.op_rows, .spinner_row1').addClass('hidenone');
            } else {
                box.find('.spinner_row1, .op_rows').addClass('hidenone');
            }
        });
        
        $(".op_rows a").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
        });
        
        $("#question_box_1").setDefault();
    });
</script>