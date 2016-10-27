<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Question extends MY_Controller {

    private $_mode;

    public function __construct() {
        parent::__construct();
        $this->load->model('question_model', 'questions');
        $this->lang->load('question');
        $this->data = array(
            'header_data' => array(
                'pagetitle' => "Questions | en2uition",
                'is_joined' => $this->authm->is_joined()
            )
        );
        if($this->session->flashdata('qmode')) {
            $this->_mode = $this->session->flashdata('qmode');
        } else {
            $this->_mode = 'all';
        }
    }

    /**
     * Display question
     *
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $lnum - level number
     * @param int $qnum - question number
     */
    public function show($type = 'past', $lnum = 1, $qnum = 1) {
        if(!in_array($type, $this->base->quizTypesArr)) {
            show_404();
            exit;
        }
        $this->lang->load('question' . $type);
        if($this->_mode == 'unans') {
            $unans_questions = $this->questions->getUnansweredQuestions($type, $lnum, $this->user_id);
            $unans_count = count($unans_questions);
            if($unans_count == 0) {
                $this->session->set_flashdata('qmode', 'all');
                redirect("/questionnaire/{$type}/level{$lnum}/analyze");
            }
            $key = array_search($qnum, $unans_questions);
            if(!in_array($qnum, $unans_questions)) {
                redirect("/questions/{$type}/l{$lnum}/q" . current($unans_questions));
            }
        }
        $question = $this->questions->getQuestion($type, $lnum, $qnum, $this->user_id, true);
        if($question) {
            $this->load->helper('questions');
            $this->load->model('questionnaire_model', 'quiz');
            $levels = $this->quiz->getLevels($type, $this->user_id);
            $this->check_access($levels, $lnum);
            $all_quest_count = isset($levels[$lnum]['quest_count']) ? $levels[$lnum]['quest_count'] : 0;
            $data = array(
                'cur_lvl' => $lnum,
                'q_type' => $type,
                'question' => $question,
                'all_quest_count' => $all_quest_count,
                'level_progress' => $this->quiz->getLevelProgress($levels[$lnum]['id'], $this->user_id),
                'unans_mode' => $this->_mode == 'all' ? false : true,
                'step_backward' => $question->qnum > 1 ? $question->qnum - 1 : 1,
                'step_forward' => $question->qnum < $all_quest_count ? $question->qnum + 1 : $all_quest_count,
                'full_step_backward' => 1,
                'full_step_forward' => $all_quest_count,
                'question_mode' => get_question_mode($question->optional),
                'has_subquestion' => has_subquestion($question->optional)
            );
            if($this->_mode == 'unans') {
                $data['step_backward'] = $key - 1 >= 0 ? $unans_questions[$key - 1] : $unans_questions[0];
                $data['step_forward'] = $key + 1 < $unans_count - 1 ? $unans_questions[$key + 1] : $unans_questions[$unans_count - 1];
                $data['full_step_backward'] = current($unans_questions);
                $data['full_step_forward'] = end($unans_questions);
                $data['unans_count'] = $unans_count;
            }
            if(strtolower($question->quest_type) == 'ethnicity') {
                $data['ethnicity'] = $this->questions->getEthnicity();
            }
            $this->data['main_content'] = $this->load->view('questionnaire/question-type/' . strtolower($question->quest_type), $data, TRUE);
            $this->data['levels'] = $levels;
            $this->data['header_data']['pagetitle'] = "Q" . $question->qnum;
            $this->load->view('questionnaire/layout', array_merge($this->data, $data));
        } else {
            show_404();
            exit;
        }
    }

    public function update_privacy_code() {
        $output = array('errors' => "");

        $id = (int) $this->input->post('id', true);
        $privacy_code = $this->input->post('privacy_code', true);
        if (in_array($privacy_code, $this->base->privacyCodesArr)) {
            $row = array(
                'user_id' => $this->user_id,
                'question_id' => $id,
                'privacy_code' => $privacy_code
            );
            $res = $this->questions->updatePrivacyCode($row);
            if(!$res) {
                $output['errors'] = lang('error_update_privacy_code');
            }
        } else {
            $output['errors'] = lang('error_incorrect_privacy_code');
        }

        $this->echo_json($output);
    }

    public function change_mode($mode = 'all') {
        if($mode == 'all' || $mode == 'unans') {
            $this->session->set_flashdata('qmode', $mode);
        }
        redirect($this->agent->referrer());
    }

    public function analyze($type = 'past', $level = 0) {
        $level = (int) $level;
        if(!in_array($type, $this->base->quizTypesArr) || $level <= 0 || $level > 7) {
            show_404();
            exit;
        }

        //  TEMPORARY CODE ONLY!!!
        // update shape to hex
        $profile_id = $this->authm->get_profile_id();
        $this->users->updateRow($profile_id, array('shape_id' => 1), 'user_profile');
        $this->session->userdata['user_info']['shape_id'] = 1;
        $this->session->userdata['user_info']['shapename'] = 'hexagon';


        $k = $level <= ($type == 'past' ? 7 : 6) ? $level : ($type == 'past' ? 7 : 6);
        $this->lang->load('question_analyze');
        $this->lang->load('outcome');
        $this->load->model('questionnaire_model', 'quiz');
        $levels = $this->quiz->getLevels($type, $this->user_id);

        $this->check_access($levels, $level);

        //run the algorithm for converting raw answers in a single row and save to DB
        $this->load->model('algorithm_model', 'algorithm');
        $this->algorithm->runZeroOne(null, $this->user_id, $type, $level);

        $lvls = $lstr = array();
        for($i = 1; $i <= $k; $i++) {
            $lvls[] = $levels[$i]['id'];
            if($i < $k) { $lstr[] = $i; }
        }
        $data = array(
            'levels' => $levels,
            'cur_lvl' => $level,
            'level' => $levels[$level],
            'q_type' => $type,
            'next_lvl' => ($level + 1) <= ($type == 'past' ? 7 : 6) ? ($level + 1) : false,
            'next_stat' => $this->quiz->getFullStatistic($lvls),
            'lstr' => $lstr,
            'my_group' => $this->authm->get_my_group()
        );
        $this->data['header_data']['pagetitle'] = lang('q_proceed_to_analysis');
        $this->data['header_data']['is_joined'] = $this->authm->is_joined();
        $this->data['header_data']['is_analyze_page'] = true;
        $this->load->view('questionnaire/analyze', array_merge($this->data, $data));
    }

    private function check_access($levels, $lvl) {
        if($lvl == 1) {
            return TRUE;
        }
        $access = true;
        for($i = 1; $i < $lvl; $i++) {
            if ($access && $levels[$i]['quest_count'] > 0 && $levels[$i]['answered_count'] < $levels[$i]['quest_count']) {
                $access = false;
                break;
            }
        }
        if($access) {
            return TRUE;
        }
        show_error(lang('q_answer_previous_before'), 500, lang('error_access_denied'));
        exit;
    }

}

/* End of file question.php */
/* Location: ./application/controllers/question.php */
