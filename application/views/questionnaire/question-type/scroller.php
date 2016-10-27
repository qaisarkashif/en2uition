<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
if($has_subquestion) {
    $t *= 2;
}
$def = array(
    "scr_min" => 0,
    "scr_max" => 120
);
$custom_options = false;
$_class = '';
foreach(array("scr_min", "scr_max") as $var) {
    if(preg_match("/{$var}=([0-9]+)/i", $question->optional,$matches)) {
        ${$var} = $matches[1];
    } else {
        ${$var} = $def[$var];
    }
}

if(preg_match('/last_opt=([^\|]+)/', $question->optional, $matches)) {
    $last_opt = $matches[1];
}
if(preg_match('/custom_options/', $question->optional)) {
    $custom_options = lang("{$lbl}_options");
    if($custom_options && $lbl !== 'l1q21') {
        $ml = 0;
        foreach ($custom_options as $o) {
            $ml = strlen($o) > $ml ? strlen($o) : $ml;
        }
        if($ml > 9) { $_class = " opt-mon"; }
    }
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode));
if($lbl == 'l1q21' || $lbl == 'l1q23') {
    $measure_lang = lang('measure');
    $m1 = $measure_lang[$lbl == 'l1q21' ? 'inches' : 'lbs'];
    $m2 = $measure_lang[$lbl == 'l1q21' ? 'cm' : 'kgs'];
?>
    <div class="measure">
        <form autocomplete="off" onsubmit="return false;">
            <label class="radio-inline"><input type="radio" value="" name="measure" checked/> <?= $m1 ?></label>
            <label class="radio-inline"><input type="radio" value="2" name="measure"/> <?= $m2 ?></label>
        </form>
    </div>
<?php
}
    for ($l = 1; $l <= $t; $l++) {
        $val = $question->{$l == 1 || ($has_subquestion && $l == 2) ? "me" : "partner"};
        if($has_subquestion && !empty($val)) {
            $arr = explode('|', $val);
            $val = $l % 2 == 0 ? $arr[1] : $arr[0];
        }
        ?>
        <div class="question_<?= $l ?>_box<?= $l > 1 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
            <div class="option_selector_box scroller">
                <a class="go_up division_question_counting_<?= $l ?>"><i class="fa fa-caret-up"></i></a>
                <div id="ud-scroller<?=$l?>" class="option_selector<?= $_class ?>">
                    <a class="option" data-opt="u2-">-</a>
                    <a class="option" data-opt="u1-">-</a>
                    <a class="option active" data-opt="-">-</a>
                    <?php
                    $k = 0;
                    if($custom_options) {
                        foreach($custom_options as $opt_key => $opt_val) {
                            $k++;
                            $option = '<a class="option' . ($k > 2 ? ' hide' : '') . '" data-opt="';
                            if ($lbl == 'l1q21' || $lbl == 'l1q23') {
                                $option .= $opt_key . '" data-opt2="' . $opt_val . '">' . $opt_key;
                            } else {
                                $option .= $opt_val . '">' . $opt_val;
                            }
                            $option .= '</a>';
                            echo $option;
                        }
                    } else {
                        for($i = $scr_min; $i <= $scr_max; $i++) {
                            $k++;
                            echo '<a class="option' . ($k > 2 ? ' hide' : '') . '" data-opt="' . $i . '">' . $i . '</a>';
                        }
                    }
                    if(isset($last_opt)) {
                        echo '<a class="option' . ($k > 2 ? ' hide' : '') . '" data-opt="' . $last_opt . '">' . $last_opt . '</a>';
                    }
                    ?>
                    <a class="option<?= abs($scr_max - $scr_min) > 1 ? ' hide' : '' ?>" data-opt="d1-">-</a>
                    <a class="option<?= abs($scr_max - $scr_min) > 2 ? ' hide' : '' ?>" data-opt="d2-">-</a>
                </div>
                <a class="go_down division_question_counting_<?= $l ?>"><i class="fa fa-caret-down"></i></a>
            </div>
            <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= $val ?>" class="val-input"/>
        </div>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>

<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>',
        has_subquestion = '<?= $has_subquestion ?>';
</script>
<script type="text/javascript" src="<?= base_url('assets/js/questions/scroller.js') ?>"></script>

<script type="text/javascript">
    var s1Heading = "<?=lang("{$lbl}s1_heading")?>",
        s2Heading = "<?=lang("{$lbl}s2_heading")?>",
        s3Heading = "<?=lang("{$lbl}s3_heading")?>",
        s4Heading = "<?=lang("{$lbl}s4_heading")?>",
        s1SubHeading = "<?=lang("{$lbl}s1_subheading");?>",
        s2SubHeading = "<?=lang("{$lbl}s2_subheading");?>",
        s3SubHeading = "<?=lang("{$lbl}s3_subheading");?>",
        s4SubHeading = "<?=lang("{$lbl}s4_subheading");?>",
        levelID = "<?= $question->level_id ?>";

</script>
<script type="text/javascript" src="<?= base_url('assets/js/questionnaire/question-type/scroller.js') ?>"></script>

<script type="text/javascript">
 </script>
