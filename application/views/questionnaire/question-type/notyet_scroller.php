<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
$def = array(
    "opt_count" => 2, 
    "scr_min" => 0, 
    "scr_max" => 120
);

foreach(array("opt_count", "scr_min", "scr_max") as $var) {
    if(preg_match("/{$var}=([0-9]+)/i", $question->optional,$matches)) {
        ${$var} = $matches[1];
    } else {
        ${$var} = $def[$var];
    }
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl));

    for ($l = 1; $l <= $t; $l++) {
        $vals = explode(";", $question->{$l == 1 ? "me" : "partner"});
        if(!isset($vals[0])) {
            $vals[0] = "N";
        }
        ?>
        <div class="question_<?= $l ?>_box<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>" data-init="<?= $vals[0] ?>">
            <div class="boolean_question choose_yes_no">
                <a class="boolean_question_yes notyet <?= $vals[0] == "Y" ? "active" : "" ?>"><?= lang("q_btn_notyet"); ?></a>
            </div>
            <div class="option_selector_box scroller <?= $lbl . ($vals[0] == "Y" ? " hidenone" : "") ?>">
                <a class="go_up division_question_counting_<?= $l ?>"><i class="fa fa-caret-up"></i></a>
                <div id="ud-scroller<?=$l?>" class="option_selector">
                    <a class="option" data-opt="u2-">-</a>
                    <a class="option" data-opt="u1-">-</a>
                    <a class="option active" data-opt="-">-</a>
                    <?php 
                    $k = 0;
                    for($i = $scr_min; $i <= $scr_max; $i++) {
                        $k++;
                        echo '<a class="option' . ($k > 2 ? ' hide' : '') . '" data-opt="' . $i . '">' . $i . '</a>';
                    }
                    ?>
                    <a class="option<?= abs($scr_max - $scr_min) > 1 ? ' hide' : '' ?>" data-opt="d1-">-</a>
                    <a class="option<?= abs($scr_max - $scr_min) > 2 ? ' hide' : '' ?>" data-opt="d2-">-</a>
                </div>
                <a class="go_down division_question_counting_<?= $l ?>"><i class="fa fa-caret-down"></i></a>
            </div>
            <div id="op_rows<?= $l ?>" class="<?= $lbl . ($vals[0] == "Y" ? "" : " hidenone") ?>">
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
                <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= isset($vals[1]) ? $vals[1] : "" ?>" class="val-input"/>
            </div>
        </div>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>

<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>';
</script>
<script type="text/javascript" src="<?= base_url('assets/js/questions/scroller.js') ?>"></script>
<script type="text/javascript">
    function setPartnerAnswersDefault() {
        flipTop();
        $("#question_box_2").setDefault("ud-scroller2", $("#q_2").val());
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
    
    $(function() {
        $.fn.setDefault = function(scroller_id, val) {
            if(!this.find('.notyet').hasClass('active')) {
                initScroller(scroller_id, val);
            } else {
                var arr = val.split(",");
                var $this = this;
                $this.find(".option_clicking_selector a.active").removeClass("active");
                $.each(arr, function(i, v) {
                    if(v != '') {
                        $this.find(".option_clicking_selector a:eq(" + v + ")").addClass('active');
                    }
                });
            }
        };
        
        $.fn.saveSelection = function(whose_answer, link) {
            var status = 0;
            if(this.find(".notyet").hasClass('active')) {
                var answer = 'Y;',
                    choise = this.find(".option_clicking_selector a.active").index();
                if(choise !== -1) {
                    status = 1;
                    answer += choise;
                }
            } else {
                var scroller = $(".option_selector:visible"),
                    active = scroller.find("a.active"),
                    answer = "N;"+active.attr('data-opt');
                if(active.attr('data-opt') != '-') {
                    status = 1;
                }
            }
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer,
                'status' : status
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        $('.option_clicking_selector a').click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
        });
        
        $(".notyet").click(function() {
            var box = $(this).closest('[id^="question_box_"]'),
                scr_box = box.find(".scroller"),
                opt_box = box.find('[id^="op_rows"]'),
                val = "";
            if(!$(this).hasClass('active')) {
                scr_box.addClass('hidenone');
                opt_box.removeClass('hidenone');
                $(this).addClass('active');                
                val = box.attr("data-init") == "Y" ? box.find(".val-input").val() : "";
            } else {
                opt_box.addClass('hidenone');
                scr_box.removeClass('hidenone');
                $(this).removeClass('active');
                val = box.attr("data-init") == "N" ? box.find(".val-input").val() : "";
            }
            box.setDefault(scr_box.find(".option_selector").attr('id'), val);
        });
        
        $("#question_box_1").setDefault("ud-scroller1", $("#q_1").val());
    });
</script>