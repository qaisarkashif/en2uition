<?php $this->load->view('questionnaire/head', $header_data); ?>
<script type="text/javascript" src="<?= base_url('/assets/js/questions/view_answers.js') ?>"></script>
<div id="main">
    <div class="header">
        <?php $this->load->view('questionnaire/header', $header_data); ?>
    </div>
    <div class="container main-container">
        <div id="q_container">
            <div id="q_content" class="<?=$question->quest_type?>"><?= $main_content ?></div>
        </div>
        <div class="clear"></div>
        <div class="q_bottom">
            <!--Privacy-->
            <div class="privacy_area" id="privacy">
                <div class="privacy_heading"><?= lang('q_privacy_title') ?></div>
                <div class="privacy_opts">
                    <?php
                    foreach (array('low', 'medium', 'high') as $privacy) {
                        echo '<a class="pr-' . $privacy . ($privacy == $question->privacy_code ? ' active' : '') .
                        '" onclick="updateQuestionPrivacyCode(' . $question->id . ', \'' . $privacy . '\')">' . lang('privacy_' . $privacy) . '</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <form autocomplete="off" onsubmit="return false;">
                <!--pagination-->
                <div class="pagination_area" id="pagination" data-all="<?= $all_quest_count ?>" data-cur="<?= $question->qnum ?>">
                    <div class="paging">
                        <p>
                            <?= $unans_mode ? lang('q_pag_unanswered') : lang('q_pag_question') ?>:
                            &nbsp;<input type="text" value="<?= $unans_mode ? $unans_count : $question->qnum ?>" maxlength="2" class="variable_page_number" <?= $unans_mode ? 'readonly' : '' ?>/>&nbsp;
                            <?= $unans_mode ? '' : lang('q_pag_off') . "&nbsp;&nbsp;" . $all_quest_count ?>
                        </p>
                    </div>
                    <div class="pagenav">
                        <a href="<?= site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, $full_step_backward)) ?>" class="full_step_backward"><i class="fa fa-step-backward"></i></a>
                        <a href="<?= site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, $step_backward)) ?>" class="step_backward"><i class="fa fa-caret-left"></i></a>
                        <a href="<?= site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, $step_forward)) ?>" class="step_forward"><i class="fa fa-caret-right"></i></a>
                        <a href="<?= site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, $full_step_forward)) ?>" class="full_step_forward"><i class="fa fa-step-forward"></i></a>
                    </div>
                </div>
                <!--circular_progress_bar-->
                <div class="level_progress">
                    <div class="circular_progress_bar">
                        <label class="q-level"><?= lang("q_level") . " " . $cur_lvl ?></label>
                        <div class="circular_bar_inner_bg"></div>
                        <input class="knob" data-thickness=".2" data-step="0.01" data-width="100" data-min="0" data-max="100" value="<?= isset($level_progress) ? $level_progress : 0 ?>" data-fgColor="#2d87d9" data-bgColor ="#e6e6e6" data-inputColor="#6794cc" data-fontWeight="normal"/>
                    </div>
                    <div id="analyze" class="btn_analyze<?= isset($level_progress) && $level_progress >= 99.9 ? "" : " hide" ?>">
                        <a href="<?= site_url(sprintf("questionnaire/%s/level%s/analyze", $q_type, $cur_lvl)) ?>"><?= lang('q_proceed_to_analysis') ?></a>
                    </div>
                </div>
            </form>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="results">
        <a href="/questions/mode/<?= $unans_mode ? 'all' : 'unans' ?>" class="<?= $unans_mode ? 'ans_selected' : '' ?>" id="see_unanswered"><?= !$unans_mode ? lang("q_filter_out_answers") : lang("q_view_all_answers");?></a>
        <a id="see_answer_received" href="/questions/answered" onclick="viewAnswers(<?= $question->level_id ?>, '<?= $q_type ?>'); return false;"><?=lang("q_view_answers");?></a>
    </div>
    <div class="clearfix"></div>
    <?php $this->load->view('include/footer'); ?>
</div>
<?php $this->load->view('questionnaire/foot', array('lnum' => $cur_lvl, 'qnum' => $question->qnum)); ?>
