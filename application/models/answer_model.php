<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Answer_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'q_answer';
        $this->_pk = 'id';
    }
    
    /**
     * Get answer the question
     * 
     * @param int $id - question ID
     * @param int $uid - user ID
     * @return null or object
     */
    public function getAnswerOnQuestion($id, $uid) {
        $answer = $this->db
                ->where('question_id', $id)
                ->where('user_id', $uid)
                ->limit(1)
                ->get($this->_table);
        if($answer !== FALSE && $answer->num_rows() == 1) {
            return $answer->row();
        }
        return NULL;
    }
    
    /**
     * Get all the questions answered in this level
     * 
     * @param int $level_id
     * @param int $user_id
     * @param string $privacy_code (optional) - privacy code of the answers ('low', 'medium' or 'high')
     * @return array
     */
    public function getAnsweredQuestions($level_id, $user_id, $privacy_code = null) {
        $this->db
                ->select('q.qnum, q.label, q.quest_type_id, q.optional, a.me, a.partner')
                ->from($this->_table . ' as a')
                ->join('q_question as q', 'q.id = a.question_id')
                ->where('q.level_id', $level_id)
                ->where('a.user_id', $user_id)
                ->where('a.me_status', 1)
                ->where('a.partner_status', 1)
                ->order_by('q.qnum');
        
        if(!empty($privacy_code)) {
            $this->db
                    ->select('if(qp.privacy_code is not null, qp.privacy_code, "low") as privacy_code', false)
                    ->join('question_privacy as qp', 'qp.question_id = q.id AND qp.user_id = ' . $user_id, 'left')
                    ->having('privacy_code', $privacy_code);
        }
        
        $answers = $this->db->get();
        return $answers !== FALSE ? $answers->result_array() : array();
    }
    
    /**
     * Get a textual representation of the responses
     * 
     * @param array $questions - an array of questions
     * @param array $opts (optional) - optional parameters
     * @return array
     */
    public function getTextAnswer($questions, $opts = array()) {
        $this->load->helper('questions');
        $list = array();
        foreach($questions as $q) {
            $lbl = $q['label'];
            $qtype = get_question_mode($q['optional']);
            $has_subquestion = has_subquestion($q['optional']);
            switch($q['quest_type_id']) {
                case 1: 
                    if ($has_subquestion) {
                        foreach (array("me", "partner") as $i) {
                            $arr = explode('|', $q[$i]);
                            ${$i} = $arr[0];
                            ${$i . "_sub"} = $arr[1];
                        }
                    } else {
                        $me = $q['me'];
                        $partner = $q['partner'];
                    }
                    if($lbl == 'l1q21' || $lbl == 'l1q23') {
                        $list['measure_lang'] = lang('measure');
                        $options = lang("{$lbl}_options");
                        $me = '<span class="meas">' . $me . '</span><span class="meas2 hide">' . $options[$me] . '</span>';
                        $partner = '<span class="meas">' . $partner . '</span><span class="meas2 hide">' . $options[$partner] . '</span>';
                        if($has_subquestion) {
                            $me_sub = '<span class="meas">' . $me_sub . '</span><span class="meas2 hide">' . $options[$me_sub] . '</span>';
                            $partner_sub = '<span class="meas">' . $partner_sub . '</span><span class="meas2 hide">' . $options[$partner_sub] . '</span>';
                        }
                    }
                    break;
                case 2: 
                case 5:
                case 21:
                    if($has_subquestion) {
                        $arr = explode('|', $q['me']);
                        $me = get_selection_text("{$lbl}s1", $arr[0]);
                        $me_sub = get_selection_text("{$lbl}s2", $arr[1]);
                        $arr = explode('|', $q['partner']);
                        $partner = get_selection_text("{$lbl}s3", $arr[0]);
                        $partner_sub = get_selection_text("{$lbl}s4", $arr[1]);
                    } else {
                        $me = get_selection_text("{$lbl}s1", $q['me']);
                        $partner = get_selection_text("{$lbl}s2", $q['partner']);
                    }
                    break;
                case 3:
                    if($has_subquestion) {
                        foreach (array("me", "partner") as $i) {
                            $arr = explode('|', $q[$i]);
                            ${$i} = get_range_text($arr[0]);
                            ${$i . "_sub"} = get_range_text($arr[1]);
                        }
                    } else {
                        $me = get_range_text($q['me']);
                        $partner = get_range_text($q['partner']);
                    }
                    break;
                case 4: 
                    if($has_subquestion) {
                        foreach (array("me", "partner") as $i) {
                            $arr = explode('|', $q[$i]);
                            ${$i} = get_yesno_text($arr[0]);
                            ${$i . "_sub"} = get_yesno_text($arr[1]);
                        }
                    } else {
                        $me = get_yesno_text($q['me']);
                        $partner = get_yesno_text($q['partner']);
                    }
                    break;
                case 6: 
                    if($has_subquestion) {
                        foreach (array("me", "partner") as $i) {
                            $arr = explode('|', $q[$i]);
                            ${$i} = get_spinner_text($lbl, $arr[0], $q['optional']);
                            ${$i . "_sub"} = get_spinner_text($lbl, $arr[1], $q['optional']);
                        }
                    } else {
                        $me = get_spinner_text($lbl, $q['me'], $q['optional']);
                        $partner = get_spinner_text($lbl, $q['partner'], $q['optional']);
                    }
                    break;
                case 7: 
                    if($has_subquestion) {
                        $arr = explode('|', $q['me']);
                        $me = get_dragdrop_text("{$lbl}s1", $arr[0]);
                        $me_sub = get_dragdrop_text("{$lbl}s2", $arr[1]);
                        $arr = explode('|', $q['partner']);
                        $partner = get_dragdrop_text("{$lbl}s3", $arr[0]);
                        $partner_sub = get_dragdrop_text("{$lbl}s4", $arr[1]);
                    } else {
                        $me = get_dragdrop_text("{$lbl}s1", $q['me']);
                        $partner = get_dragdrop_text("{$lbl}s2", $q['partner']);
                    }
                    break;
                case 8: 
                    $me = get_ynselection_text("{$lbl}s1", $q['me']);
                    $partner = get_ynselection_text("{$lbl}s2", $q['partner']);
                    break;
                case 9: 
                    $qtype = 'double';
                    $me = $q['me'] . "%";
                    $partner = $q['partner'] . "%";
                    break;
                case 10: 
                    $me = get_household_text("{$lbl}s1", $q['me']);
                    $partner = get_household_text("{$lbl}s2", $q['partner']);
                    break;
                case 11: 
                    $this->load->model('question_model', 'qm');
                    $ethnicity = $this->qm->getEthnicity();
                    $me = get_ethnicity_text("{$lbl}s1", $ethnicity, $q['me']);
                    $partner = get_ethnicity_text("{$lbl}s2", $ethnicity, $q['partner']);
                    break;
                case 12: 
                    $me = get_noselection_text("{$lbl}s1", $q['me']);
                    $partner = get_noselection_text("{$lbl}s2", $q['partner']);
                    break;
                //case 13: break;
                case 14: 
                    $me = $q['me'];
                    $partner = $q['partner'];
                    $type = array_filter(explode('|', $q['optional']));
                    if(!isset($type[0])) {
                        $type[0] = 'C';
                    }
                    if($type[0] == 'A' || $type[0] == 'B') {
                        $qtype = 'double';
                        $has_subquestion = true;
                        $me = abs(-6 + $me);
                        $me_sub = 6 - $me;
                        $partner = abs(-6 + $partner);
                        $partner_sub = 6 - $partner;
                    } else {
                        $me = 6 - $me;
                        $partner = 6 - $partner;
                    }
                    break;
                case 15: 
                    $me = get_notyet_scroller_text("{$lbl}s1", $q['me']);
                    $partner = get_notyet_scroller_text("{$lbl}s2", $q['partner']);
                    break;
                case 16: 
                    $me = get_multispinner_text($lbl, $q['me'], $q['optional']);
                    $partner = get_multispinner_text($lbl, $q['partner'], $q['optional']);
                    break;
                case 17: 
                    $me = get_yesno_spinner_text($lbl, $q['me'], $q['optional']);
                    $partner = get_yesno_spinner_text($lbl, $q['partner'], $q['optional']);
                    break;
                case 18:
                    $me = get_ynselection2_text("{$lbl}s1", $q['me']);
                    $partner = get_ynselection2_text("{$lbl}s2", $q['partner']);
                    break;
                case 19:
                    if($has_subquestion) {
                        $arr = explode('|', $q['me']);
                        $me = get_ynrange_text("{$lbl}s1", $arr[0]);
                        $me_sub = get_ynrange_text("{$lbl}s2", $arr[1]);
                        $arr = explode('|', $q['partner']);
                        $partner = get_ynrange_text("{$lbl}s3", $arr[0]);
                        $partner_sub = get_ynrange_text("{$lbl}s4", $arr[1]);
                    } else {
                        $me = get_ynrange_text("{$lbl}s1", $q['me']);
                        $partner = get_ynrange_text("{$lbl}s2", $q['partner']);
                    }
                    break;
                case 20:
                    $me = get_noselection2_text("{$lbl}s1", $q['me']);
                    $partner = get_noselection2_text("{$lbl}s2", $q['partner']);
                    break;
                case 22:
                    if($q['optional'] == 'main') {
                        $answer = array(
                            'title1' => lang("{$lbl}_view1"),
                            'title2' => lang("{$lbl}_view2")
                        );
                        $approx = $opts['yellow_count'] * 1.5;
                        $answer['title1'] .= sprintf(lang("approximately_months"), $approx);
                        $rel = explode("|", $q['partner']);
                        if(count($rel) == 2) {
                            $answer['title2'] .= (int)$rel[1] == 1 ? lang('ocq1_btn1') : lang('ocq1_btn2');
                        }
                    } else {
                        $txt = lang("{$lbl}_view");
                        $answer = array(
                            'main_title' => $txt ? $txt : ""
                        );
                        $lng = lang('percents_of_time');
                        foreach(array('mred', 'mgreen', 'pred', 'pgreen') as $l) {
                            $k = $l[0] == 'm' ? 'me' : 'partner';
                            $g = substr($l, 1) == 'green' ? '2' : '1';
                            $c = substr_count($q[$k], $g) / $opts['yellow_count'];
                            $txt = lang("{$lbl}_{$l}_view");
                            $answer[$l] = array(
                                'title' => $txt ? $txt : "",
                                'value' => round($c * 100.0) . $lng
                            );
                        }
                    }
                    break;
                default: $me = $partner = ""; break;
            }
            if($q['quest_type_id'] != 22) {
                $answer = array(
                    'type' => $qtype, 
                    'has_subquestion' => $has_subquestion,
                    'me' => $me, 
                    'partner' => $partner
                );
                if($has_subquestion) {
                    $answer['me_sub'] = $me_sub;
                    $answer['partner_sub'] = $partner_sub;
                }
                if ($qtype == 'single') {
                    $txt = lang("{$lbl}s_view");
                    $answer['title'] = $txt ? $txt : "";
                    if($has_subquestion) {
                        $txt = lang("{$lbl}sub_view");
                        $answer['subtitle'] = $txt ? $txt : "";
                    }
                } else {
                    $txt1 = lang("{$lbl}s1_view");
                    $txt2 = lang("{$lbl}s2_view");
                    $answer['title_me'] = $txt1 ? $txt1 : "";
                    $answer['title_partner'] = $txt2 ? $txt2 : "";
                    if($has_subquestion) {
                        $txt1 = lang("{$lbl}sub1_view");
                        $txt2 = lang("{$lbl}sub2_view");
                        $answer['subtitle_me'] = $txt1 ? $txt1 : "";
                        $answer['subtitle_partner'] = $txt2 ? $txt2 : "";
                    }
                }
            }
            $list[$q['qnum']] = $answer;
        }
        return $list;
    }
        
    /**
     * Get main outcome answer
     * 
     * @param int $uid - user ID
     * @return null or object
     */
    public function getMainOutcomeAnswer($uid) {
        $this->db
                ->select('a.*', false)
                ->from('q_answer as a')
                ->join('q_question as q', 'a.question_id = q.id')
                ->where('a.user_id', $uid)
                ->where('q.optional', 'main')
                ->where('q.level_id', 7)
                ->where('q.quest_type_id', 22)
                ->limit(1);
        $answer = $this->db->get();
        if($answer !== FALSE && $answer->num_rows() == 1) {
            return $answer->row();
        }
        return NULL;
    }
    
    /**
     * Get outcome answers
     * 
     * @param int $uid - user ID
     * @return array 
     */
    public function getOutcomeAnswers($uid) {
        $answers = array();
        $this->db
                ->select('a.question_id, a.me, a.partner, a.me_status, a.partner_status')
                ->from($this->_table . ' as a')
                ->join('q_question as q', 'q.id = a.question_id AND q.level_id = 7 AND q.quest_type_id = 22')
                ->where('a.user_id', $uid);
        $res = $this->db->get();
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $answers[$row['question_id']] = $row;
            }
        }
        return $answers;
    }

}

/* End of file answer_model.php */
/* Location: ./application/models/answer_model.php */