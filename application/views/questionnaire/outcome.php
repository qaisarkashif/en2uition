<?php 

function getRangeBar($id, $period) {
    $wbox = true;
    for ($i = 1; $i < 8; $i++) {
        echo '<div class="' . ($wbox ? 'w' : 'g') . 'box">';
        echo '<div id="ss' . $i . '" class="ss"></div><div id="qa' . $i . '" class="qa">';
        foreach ($period as $key => $val) {
            echo '<div id="' . $key . '" data-id="' . $i . '" box-id="' . $id . '" class="oca white" alt="' . lang("oc_" . $val) . '"></div>';
        }
        echo '</div><div class="yr">' . lang("oc_{$i}year") . '</div></div>';
        $wbox = !$wbox;
    }
}

$this->load->view('questionnaire/head', $header_data);
?>
<script type="text/javascript" src="<?= base_url('/assets/js/questions/view_answers.js') ?>"></script>
<div id="main">
    <div class="header">
        <?php $this->load->view('questionnaire/header', $header_data); ?>
    </div>
    <div class="container main-container">
        <div class="outcome" data-collapse-type="manual">
            <?php
            $questions_count = count($questions);
            foreach ($questions as $ind => $q) : 
                $i = $q['qnum'];
                $class = $me = $partner = "";
                if(isset($answers[$q['id']])) {
                    $me = $answers[$q['id']]['me'];
                    $partner = $answers[$q['id']]['partner'];
                    if($answers[$q['id']]['me_status'] == 1) {
                        $class .= " ma-compl";
                    }
                    if($answers[$q['id']]['partner_status'] == 1) {
                        $class .= " pa-compl";
                    }
                }
            ?>
                <div id="oc-q<?= $i ?>" class="accordion-container<?= $class ?>" data-qid="<?= $q['id'] ?>">
                    <?php 
                    if($q['optional'] == 'main') : 
                        $tmp = explode('|', $partner);
                        $mark_rel = (int) $tmp[0];
                        $ending_rel = count($tmp) == 2 ? (int) $tmp[1] : 0;
                        ?>
                        <input type="hidden" class="qstatus" value="<?= $class == " ma-compl pa-compl" ? 1 : 0 ?>"/>
                        <input type="hidden" class="qanswer" value="<?= $me ?>"/>
                        <div href="#" class="main accordion-toggle open">
                            <?= lang("ocq{$i}_title"); ?>
                            <div class="oct_privacy_opts">
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'low', this);"<?= $q['privacy_code'] == "low" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_low') ?>
                                </a>
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'medium', this);"<?= $q['privacy_code'] == "medium" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_medium') ?>
                                </a>
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'high', this);"<?= $q['privacy_code'] == "high" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_high') ?>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="accordion-content" style="display:block;">
                            <div class="ocq_content be">
                                <div class="be-head">
                                    <?= lang("ocq{$i}_subtitle1"); ?> 
                                    <a class="<?= $mark_rel == 0 ? 'active' : ''; ?>" data-v="1"><?= lang("ocq{$i}_btnstart"); ?></a> 
                                    <a class="<?= $mark_rel > 0 ? 'active pink' : ''; ?>" data-v="2"><?= lang("ocq{$i}_btnend"); ?></a> 
                                    <?= lang("ocq{$i}_subtitle2"); ?>
                                </div>
                                <div class="duration-bar">
                                    <div id="comparison<?= $i ?>" class="qboxes" style="padding-top:15px;"><?php getRangeBar($i, $period); ?></div>
                                </div>
                                <p><?= lang("ocq{$i}_txt1"); ?></p>
                                <p class="ending-rel">
                                    <a <?= $ending_rel == 1 ? 'class="active"' : '' ?> data-v="1"><?= lang("ocq{$i}_btn1"); ?></a>
                                    <a <?= $ending_rel == 2 ? 'class="active"' : '' ?> data-v="2"><?= lang("ocq{$i}_btn2"); ?></a>
                                </p>
                            </div>
                            <div class="process_answer ocq_ans">
                                <div id="sub2" class="clearfix text-center" style="display:block;">
                                    <a class="saveandexit_button" onClick="saveAnswer(<?= $i ?>, true);"><?= lang("q_save_and_exit"); ?></a>
                                    <a class="boolean_next_question" onClick="saveAnswer(<?= $i ?>, false, 2);"><?= lang("q_next_question"); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="me-answer" value="<?= $me ?>"/>
                        <input type="hidden" name="partner-answer" value="<?= $partner ?>"/>
                        <div href="#" class="accordion-toggle">
                            <?= lang("ocq{$i}_title"); ?>
                            <div class="oct_privacy_opts hide">
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'low', this);"<?= $q['privacy_code'] == "low" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_low') ?>
                                </a>
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'medium', this);"<?= $q['privacy_code'] == "medium" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_medium') ?>
                                </a>
                                <a onclick="updatePrivacyCode(<?= $q['id'] ?>, 'high', this);"<?= $q['privacy_code'] == "high" ? ' class="active"' : "" ?>>
                                    <?= lang('privacy_high') ?>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="accordion-content">
                            <div class="ocq_content type-flip">
                                <div class="partner-selection">
                                    <div class="me">
                                        <div id="me">
                                            <div class="front"><div class="me-title"><?= lang("q_me"); ?></div></div> 
                                            <div class="back"><div class="me-title"><?= lang("q_me"); ?></div></div> 
                                        </div>
                                    </div>
                                    <div class="oc-txt">
                                        <span class="me-oc-txt"><?= lang("ocq{$i}_text1") ?></span>
                                        <span class="partner-oc-txt hide"><?= lang("ocq{$i}_text2") ?></span>
                                    </div>
                                    <div class="my-partner">
                                        <div id="my-partner">
                                            <div class="front"><div class="mp-title"><?= lang("q_my_partner"); ?></div></div> 
                                            <div class="back"><div class="mp-title"><?= lang("q_my_partner"); ?></div></div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="duration-bar">
                                    <div id="comparison<?= $i ?>" class="qboxes"><?php getRangeBar($i, $period); ?></div>
                                </div>
                            </div>
                            <div class="process_answer ocq_ans">
                                <div id="sub1" class="clearfix text-center">
                                    <div class="col-xs-12"><a class="boolean_question_submit_button" onClick="saveAnswer(<?=$i?>, false, 'flip');"><?= lang("oc_btn1"); ?></a></div>
                                </div>
                                <div id="sub2" class="clearfix text-center hidenone">
                                    <a class="saveandexit_button" onClick="saveAnswer(<?=$i?>, true);" style="<?= $i >= $questions_count ? "float: none;" : "" ?>">
                                        <?= lang("q_save_and_exit"); ?>
                                    </a>
                                    <a class="boolean_next_question" onClick="saveAnswer(<?=$i?>, false, <?= $i < $questions_count ? ($i+1) : -1 ?>);">
                                        <?= $i < $questions_count ? lang("q_next_question") : lang('btn_save') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
        <div id="goto-analyze" class="hide" style="text-align: center; margin-top: -15px;">
            <a class="btngreen abtn" href="<?= site_url('/outcome/analyze') ?>"><?= lang('q_proceed_to_analysis') ?></a>
        </div>
    </div>
    
    <div class="results">
        <a id="see_unanswered" class="not-affect" onclick="return false;">&nbsp;</a>
        <a id="see_answer_received" href="/questions/answered" onclick="viewAnswers(7, 'past'); return false;"><?=lang("q_view_answers");?></a>
    </div>
    <div class="clearfix"></div>
    <?php $this->load->view('include/footer'); ?>
</div>
<?php $this->load->view('questionnaire/foot', array('is_outcome' => true)); ?>

<script type="text/javascript" src="<?= base_url('assets/js/questions/outcome.js') ?>"></script>