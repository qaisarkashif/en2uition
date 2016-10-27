<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
$opt_count = 0;
if(preg_match('/opt_count=([0-9]+)/', $question->optional, $matches)) {
    $opt_count = $matches[1];
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'top_class' => 'sq9'));

    for ($l = 1; $l <= $t; $l++) {
        $answer = $l == 2 ? $question->partner : $question->me;
        if(!empty($answer)) {
            $answer = explode(';', $answer);
            $yn = $answer[0];
            $val = isset($answer[1]) ? $answer[1] : "";
        } else {
            $yn = $val = "";
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
                    <div class="rows_box">
                        <div class="option_clicking_selector division_question_counting_5">
                            <?php
                            for ($i = 1; $i <= $opt_count; $i++) {
                                echo '<a href="#" class="option">' . lang("{$lbl}s{$l}_opt{$i}") . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= $val ?>">
            </div>            
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>

<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>';
    
    function setPartnerAnswersDefault() {
        flipTop();
        $("#question_box_2").setDefault($("#q_2"));
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
    
    $(function() {        
        $.fn.setDefault = function(field) {
            var v = field.val();
            if(v != '' && v != -1) {
                this.find(".option_clicking_selector a:eq(" + v + ")").addClass('active');
            }
            var yn = this.find(".q_yn").val();
            this.find('.choose_yes_no a[data-id="'+yn+'"]').removeClass('active').click();
        };
        
        $.fn.saveSelection = function(whose_answer, link) {
            var yn = this.find(".q_yn").val(),
                answer = new Array(),
                status = 0;
        
            if(yn == 'Y' || yn == 'N') {
                answer.push(yn);
                if(yn == 'Y') {
                    var choise = this.find(".option_clicking_selector a.active").index();
                    if(choise !== -1) {
                        answer.push(choise);
                        status = 1;
                    }
                } else {
                    status = 1;
                }
            }
            
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer.join(";"),
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
            var choise = '',
                box = $(this).closest('[id^="question_box_"]');
            if($(this).hasClass('active')) {
                choise = $(this).attr('data-id');
                if(choise == 'Y') {
                    box.find('.op_rows').removeClass('hidenone');
                } else {
                    box.find('.op_rows').addClass('hidenone');
                }
            } else {
                box.find('.op_rows').addClass('hidenone');
            }
            box.find('.q_yn').val(choise);
        });
        
        $("#question_box_1").setDefault($("#q_1"));
    });
</script>