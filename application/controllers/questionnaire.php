<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questionnaire extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->data = array(
            "top_menu" => 'questionnaire',
            'inner_navbar' => true,
            'active_page' => 'questionnaire',
            'user' => $this->authm->get_profile()
        );
        $this->tmpl->set_pagetitle('Questionnaire | en2uition');
        $this->load->model('questionnaire_model', 'quiz');
        $this->lang->load('question');
    }

    public function index() {
        $this->data['main_content'] = $this->load->view('photo/index', '', TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function past($active_box = '') {
        $this->show_levels_page('past', $active_box);
    }

    public function present($active_box = '') {
        $this->show_levels_page('present', $active_box);
    }
    
    private function show_levels_page($type, $active_box) {
        $this->load->model('request_model', 'requests');
        $this->load->model('friend_model', 'friends');
        $this->session->set_flashdata('qmode', 'all');
        
        $requests = array();
        $question_requests = $this->requests->checkRequests($this->user_id);
        if(isset($question_requests['question_privacy'])) {
            foreach($question_requests['question_privacy'] as $r) {
                $opt = json_decode($r['optional'], true);
                if($opt['qtype'] != $type) {
                    continue;
                }
                if(!isset($requests[$r['from_user']])) {
                    $requests[$r['from_user']] = array(
                        'username' => $r['username'],
                        'ava' => $r['ava'],
                        'levels' => array()
                    );
                }
                $requests[$r['from_user']]['levels'][$opt['lnum']] = array('id' => $r['id'], 'codes' => $opt['codes']);
            }
        }
        
        $friends_list = $this->friends->getUserFriends($this->user_id, array('forum'));
        $where = array(
            'from_user' => $this->user_id,
            'to_user IN' => '(' . implode(',', array_keys($friends_list) ? array_keys($friends_list) : array(0)) . ')', 
            'type' => '"question"',
            'privacy LIKE' => '\'%"' . $type . '":%\''
        );
        $granted_permissions = $this->users->get_privacy_permissions(null, $where);
        $data = array(
            'shape_count' => $this->quiz->getRowCount('shape'),
            'type' => $type,
            'request' => isset($_REQUEST['request']) ? 1 : 0,
            'levels' => $this->quiz->getLevels($type, $this->user_id),
            'active_box' => $active_box,
            'questionnaire_requests' => $requests,
            'friends_list' => $friends_list,
            'granted_perms' => isset($granted_permissions['question']) ? $granted_permissions['question'] : array(),
            'is_joined' => $this->authm->is_joined()
        );
        
        $this->data['main_content'] = $this->load->view('questionnaire/request', $data, TRUE);
        $this->tmpl->add_scripts("questionnaire/questionnaire.js");
        $this->tmpl->show_template('include/layout', $this->data);
    }
    
    public function get_level_progress() {
        $output = array('errors' => "");
        
        $level_id = (int) $this->input->get('level_id', true);
        if($level_id) {
            $output['progress'] = $this->quiz->getLevelProgress($level_id, $this->user_id);
        } else {
            $output['errors'] = lang('error_empty_input_param');
        }
        
        $this->echo_json($output);
    }
    
    public function get_privacy_question() {
        $output = array(
            'list' => array(),
            'list_length' => 0,
            'no_found_txt' => lang('q_question_not_found')
        );
        $lvl_id = (int)$this->input->post('lvl_id', true);
        $pr_code = $this->input->post('code', true);
        if($lvl_id && $pr_code && in_array($pr_code, $this->base->privacyCodesArr)) {
            $list = $this->quiz->getPrivacyQuestions($this->user_id, $lvl_id, $pr_code);
            $lang_load = false;
            foreach($list as $row) {
                if(!$lang_load) {
                    $this->lang->load('question' . $row['type']);
                    if($lvl_id == 7) {
                        $this->lang->load('outcome');
                    }
                    $lang_load = true;
                }
                $output['list'][$row['qnum']] = lang($row['label'] . "_title");
            }
            $output['list_length'] = count($output['list']);
        }
        $this->echo_json($output);
    }
    
    public function update_friends_access() {
        $output = array('errors' => "");
        
        $pid = (int) $this->input->post('pid', true);
        $lnum = (int) $this->input->post('lnum', true);
        $type = $this->input->post('type', true);
        $codes = $this->input->post('codes', true);
        if($codes === false) {
            $codes = array();
        }
        
        if($pid && $lnum && in_array($type, $this->base->quizTypesArr) && is_array($codes))
        {
            $record = $this->users->get_privacy_permissions(null, array('id' => $pid));
            if($record) {
                $row = current(current($record['question']));
                if(count($codes) > 0) {
                    $row['privacy'][$type][$lnum] = $codes;
                } else {
                    unset($row['privacy'][$type][$lnum]);
                    if(count($row['privacy'][$type]) == 0) {
                        unset($row['privacy'][$type]);
                    }
                }
                if(count($row['privacy']) > 0) {
                    $res = $this->users->updateRow($pid, array('privacy' => json_encode($row['privacy'])), 'privacy_permission');
                } else {
                    $res = $this->users->deleteRow($pid, 'privacy_permission');
                }
                if(!$res) {
                    $output['errors'] = lang('error_update_settings');
                }
            } else {
                $output['errors'] = lang('error_incorrect_data');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        
        $this->echo_json($output);
    }
    
    public function view_shared_answers($type = "past") {
        $this->load->model('request_model', 'requests');
        $this->load->model('friend_model', 'friends');
        $this->lang->load('question');
        
        $friends_list = $this->friends->getUserFriends($this->user_id, array('forum'));
        $where = array(
            'to_user' => $this->user_id,
            'from_user IN' => '(' . implode(',', array_keys($friends_list) ? array_keys($friends_list) : array(0)) . ')', 
            'type' => '"question"',
            'privacy LIKE' => '\'%"' . $type . '":%\''
        );
        $granted_permissions = $this->users->get_privacy_permissions(null, $where);
        
        $data = array(
            'qtype' => $type,
            'levels' => $this->quiz->getLevels($type, $this->user_id),
            'friends_list' => $friends_list,
            'granted_perms' => isset($granted_permissions['question'][$this->user_id]) ? $granted_permissions['question'][$this->user_id] : array()
        );
        $this->data['main_content'] = $this->load->view('questionnaire/shared_answers', $data, TRUE);
        $this->tmpl->set_pagetitle('Shared Answers | en2uition');
        $this->tmpl->add_scripts("questionnaire/shared_answers.js");
        $this->tmpl->show_template('include/layout', $this->data);
    }

}

/* End of file questionnaire.php */
/* Location: ./application/controllers/questionnaire.php */