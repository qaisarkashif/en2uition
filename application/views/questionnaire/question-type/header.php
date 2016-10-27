<?php 
$l3 = isset($l3);
if(!isset($has_subquestion)) {
    $has_subquestion = false;
}
if(!isset($question_mode)) {
    $question_mode = 'double';
}
?>
<div class="row">
    <div class="qbox type-flip col-md-4 col-md-push-4 col-sm-6 col-sm-push-3 col-xs-12<?= isset($top_class) ? " " . $top_class : "" ?>">
        
        <?php if(!$l3) : ?>
            <div class="heading" id="heading">
                <span><?= lang($lbl . "s1_heading"); ?></span>
                <span class="hide"><?= lang($lbl . ($has_subquestion ? "s3" : "s2") . "_heading"); ?></span>
            </div>
        <?php endif; ?>
        
        <div class="partner-selection">
            <div class="me<?= ($l3 ? " me-flip" : "") . ($question_mode == 'single' ? " no-flip" : "") ?>">
                <div id="me">
                    <div class="front"><div class="me-title"><?= lang("q_me"); ?></div></div>
                    <?php if($question_mode == 'double') { ?>
                        <div class="back"><div class="me-title"><?= lang("q_me"); ?></div></div> 
                    <?php } ?>
                </div>
            </div>
            <span id="subheading">
                <span><?= $l3 ? "" : lang($lbl . "s1_subheading"); ?></span>
                <span class="hide"><?= $l3 ? "" : lang($lbl . ($has_subquestion ? "s3" : "s2") . "_subheading"); ?></span>
            </span>
            <div class="my-partner<?= $question_mode == 'single' ? " no-flip" : "" ?>">
                <div id="my-partner">
                    <?php if($question_mode == 'double') { ?>
                        <div class="front"><div class="mp-title"><?= lang("q_my_partner"); ?></div></div>
                    <?php } ?>
                    <div class="back"><div class="mp-title"><?= lang("q_my_partner"); ?></div></div> 
                </div>
            </div>
        </div>