<?php
$lbl = $question->label;
$type = $question->optional ? array_filter(explode('|', $question->optional)) : array('A', 'N');
if(!isset($type[1])) {
    $type[1] = 'N';
}

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'top_class' => 'l3-flipflop sq9', 'l3' => true));
?>
    <div class="l3_bg" id="question_box">
        <div class="top_heading">
            <span><?= lang("{$lbl}s1_heading"); ?></span>
            <span class="hide"><?= lang("{$lbl}s2_heading"); ?></span>
        </div>
        <div class="typea">
            <?php if($type[0] == 'A') : ?>
                <div class="innertop_heading">
                    <span><?= lang("{$lbl}s1_subheading"); ?></span>
                    <span class="hide"><?= lang("{$lbl}s2_subheading"); ?></span>
                </div>
            <?php
            endif;
            for ($l = 1; $l <= 2; $l++) { ?>
                <form autocomplete="off" onsubmit="return false;">
                    <div class="blue_column<?= ($type[1] == 'R' ? '1' : '') . ($l == 2 ? ' hidenone' :
                        "") ?>"
                         id="<?= $l == 1 ? 'me-q' : 'partner-q' ?>" data-qid="<?= $question->id ?>">
                        <ul>
                            <?php
                            for ($i = 1; $i < 8; $i++) {
                                echo '<li></li>';
                            }
                            ?>
                        </ul>
                        <input type="hidden" name="q_<?= $l ?>" id="q_<?= $l ?>" value="<?= $question->{$l == 1 ? "me" : "partner"} ?>">
                    </div>
                </form>
            <?php
            }
            if($type[0] == 'A') {
            ?>
                <div class="clear"></div>
                <div class="innerbot_heading">
                    <span><?= lang("{$lbl}s1_subheading2"); ?></span>
                    <span class="hide"><?= lang("{$lbl}s2_subheading2"); ?></span>
                </div>
            <?php } ?>
        </div>
        <div class="clear"></div>
        <?php if($type[0] == 'B' || $type[0] == 'C') { ?>
            <div id="btm-subheading" class="bottom_text">
                <?php if($type[0] == 'B') { ?>
                    <span><?= lang("{$lbl}s1_heading2") ?></span>
                    <span class="hide"><?= lang("{$lbl}s2_heading2") ?></span>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>

<script type="text/javascript">
    var grey_col = 'grey_column<?= $type[1] == 'R' ? '1' : '' ?>',
        blue_col = 'blue_column<?= $type[1] == 'R' ? '1' : '' ?>',
        q_level_id = <?= $question->level_id ?>,
        q_type = "<?= $type[0] ?>";

</script>
<script type="text/javascript" src="<?= base_url('assets/js/questionnaire/question-type/typeg.js'); ?>"></script>
