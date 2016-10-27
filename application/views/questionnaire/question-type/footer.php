<?php 
if(!isset($question_mode)) {
    $question_mode = 'double';
}
if(!isset($has_subquestion)) {
    $has_subquestion = false;
}
?>
    <div class="process_answer">
        <?php if($has_subquestion) : ?>
            <div class="boolean_question clearfix text-center sub-btn1">
                <div class="col-md-12 col-xs-12">
                    <h4><?=lang("{$lbl}s1_was_this_different");?></h4>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <a class="boolean_question_yes" id="submit_p1"><?=lang("q_btn_yes");?></a>
                    <a class="boolean_question_no" id="submit_no1"><?=lang("q_btn_no");?></a>
                </div>
            </div>
        <?php 
        endif;
        if($question_mode == "double") : ?>
            <div class="clearfix text-center<?= $has_subquestion ? " hidenone" : "" ?>" id="qsubmit_1">
                <div class="col-xs-12">
                    <a class="boolean_question_submit_button q1"><?= lang("{$lbl}_submit_and_choose"); ?></a>
                </div>
            </div>
            <?php if($has_subquestion) : ?>
                <div class="boolean_question clearfix text-center hidenone sub-btn2">
                    <div class="col-md-12 col-xs-12">
                        <h4><?=lang("{$lbl}s2_was_this_different");?></h4>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <a class="boolean_question_yes" id="submit_p2"><?=lang("q_btn_yes");?></a>
                        <a class="boolean_question_no" id="submit_no2"><?=lang("q_btn_no");?></a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="clearfix text-center<?= $question_mode == 'double' || $has_subquestion ? " hidenone" : "" ?>" id="qsubmit_2">
            <div class="col-xs-12">
                <a href="<?= site_url('questionnaire/' . $q_type); ?>" class="saveandexit_button q2"><?= lang("q_save_and_exit"); ?></a>
                <?php if(($unans_mode && $step_forward > $question->qnum) || (!$unans_mode && $question->qnum < $all_quest_count)) { ?>
                    <a href="<?= site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, $unans_mode ? $step_forward : $question->qnum + 1)); ?>" class="boolean_next_question play-gears q2">
                        <?= lang("q_next_question"); ?>
                    </a>
                <?php } elseif(($unans_mode && $step_forward == $question->qnum) || (!$unans_mode && $question->qnum == $all_quest_count)) { ?>
                    <a class="boolean_next_question play-gears q2 just-save">
                        <?= lang("btn_save"); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>