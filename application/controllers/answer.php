<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Answer extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('answer_model', 'answers');
    }
    
    public function save() {
        $output = array('errors' => "");
        
        $id = (int) $this->input->post('id', true);
        $whose_answer = $this->input->post('whose_answer', true);
        $answer = $this->input->post('answer', true);
        $status = (int) $this->input->post('status', true);
        
        if($id && $whose_answer) {
            $row = array(
                'user_id' => $this->user_id,
                'question_id' => $id
            );
            $answer = htmlspecialchars($answer, ENT_COMPAT);
            if($whose_answer == 'main_outcome') {
                $mark_rel = (int) $this->input->post('mark_rel', true);
                $ending_rel = (int) $this->input->post('ending_rel', true);
                
                $row['me_status'] = $row['partner_status'] = $status;
                $row['me'] = $answer ? $answer : "";
                $row['partner'] = implode('|', array($mark_rel, $ending_rel));
            } elseif($whose_answer == 'both') {
                $split_answer = $this->input->post('split_answer', true);
                if($split_answer && $answer != '') {
                    $answer = explode(';', $answer);
                    $row['me'] = $answer[0];
                    $row['partner'] = $answer[1];
                } else {
                    $row['me'] = $row['partner'] = $answer == '' ? NULL : $answer;
                }
                $row["me_status"] = $row["partner_status"] = $status;
            } else {
                $row[$whose_answer] = $answer == '' ? NULL : $answer;
                $row[$whose_answer . "_status"] = $status;
            }
            $answ = $this->answers->getAnswerOnQuestion($id, $this->user_id);
            if(!is_null($answ)) {
                $res = $this->answers->updateRow($answ->id, $row);
            } else {
                $res = $this->answers->insertRow($row);
            }
            if(!$res) {
                $output['errors'] = lang('error_answer_save');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        
        $this->echo_json($output);
    }
    
    public function view_answered() {
        $output = array('errors' => "");        
        $level_id = (int) $this->input->post('level_id', true);
        $type = $this->input->post('type', true);
        if($level_id && in_array($type, $this->base->quizTypesArr)) {
            $this->lang->load('question');
            $opts = array();
            if($type == 'past' && $level_id == 7) {
                $this->lang->load('outcome');
                $answ = $this->answers->getMainOutcomeAnswer($this->user_id);
                if($answ && $answ->me_status == 1) {
                    $answ = str_replace(array(',', ' '), '', $answ->me);
                    $p1 = strpos($answ, '1');
                    $p2 = strrpos($answ, '1');
                    $opts['yellow_count'] = $p2 - $p1 + 1;
                }
            } else {
                $this->lang->load('question' . $type);
            }
            $anq = $this->answers->getAnsweredQuestions($level_id, $this->user_id);
            $output['list'] = $this->answers->getTextAnswer($anq, $opts);
            $output['ttl'] = lang('q_answer');
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }        
        $this->echo_json($output);
    }
    
    public function get_shared_answers() {
        $output = array('errors' => "");
        $level_id = (int) $this->input->post('level_id', true);
        $user_id = (int) $this->input->post('user_id', true);
        $privacy_code = $this->input->post('code', true);
        $type = $this->input->post('type', true);
        if($level_id && $type && $privacy_code && $user_id) {
            $this->lang->load('question');
            $opts = array();
            if($type == 'past' && $level_id == 7) {
                $this->lang->load('outcome');
                $answ = $this->answers->getMainOutcomeAnswer($user_id);
                if($answ && $answ->me_status == 1) {
                    $answ = str_replace(array(',', ' '), '', $answ->me);
                    $p1 = strpos($answ, '1');
                    $p2 = strrpos($answ, '1');
                    $opts['yellow_count'] = $p2 - $p1 + 1;
                }
            } else {
                $this->lang->load('question' . $type);
            }
            $anq = $this->answers->getAnsweredQuestions($level_id, $user_id, $privacy_code);
            $output['list'] = $this->answers->getTextAnswer($anq, $opts);
            $output['list_length'] = count($output['list']);
            if($output['list_length'] == 0) {
                $output['not_found_txt'] = lang('error_answers_not_found');
            }
            $output['ttl'] = lang('q_answer');
        } else {
            $output['errors'] = lang("error_empty_input_params");
        }
        $this->echo_json($output);
    }
}

/* End of file answer.php */
/* Location: ./application/controllers/answer.php */