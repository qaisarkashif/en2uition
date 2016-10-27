<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Question_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'q_question';
        $this->_pk = 'id';
    }
    
    /**
     * Get question
     * 
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $lnum - level number
     * @param int $qnum - question number
     * @param int $uid - user ID
     * @param boolean $include_answers
     * @return null or object
     */
    public function getQuestion($type, $lnum, $qnum, $uid, $include_answers = false) {
        $this->db
                ->select('q.*, qt.typename as quest_type, if(qp.privacy_code is not null, qp.privacy_code, "low") as privacy_code', false)
                ->from($this->_table . ' as q')
                ->join('q_level as l', 'l.id = q.level_id')
                ->join('q_question_type as qt', 'qt.id = q.quest_type_id')
                ->join('question_privacy as qp', 'qp.question_id = q.id AND qp.user_id = ' . $uid, 'left')
                ->where('l.type', $type)
                ->where('l.level', $lnum)
                ->where('q.qnum', $qnum);
        if($include_answers) {
            $this->db
                    ->select('a.me, a.partner')
                    ->join('q_answer as a', 'a.question_id = q.id AND a.user_id = ' . $uid, 'left');
        }
        $question = $this->db->limit(1)->get();
        if($question !== FALSE && $question->num_rows() == 1) {
            return $question->row();
        }
        return NULL;
    }
    
    /**
     * Get an array of unanswered questions
     * 
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $lnum - level number
     * @param int $user_id - user ID
     * @return array
     */
    public function getUnansweredQuestions($type, $lnum, $user_id) {
        $this->db
                ->select('q.qnum')
                ->from($this->_table . ' as q')
                ->join('q_level as l', 'l.id = q.level_id')
                ->join('q_answer as a', 'a.question_id = q.id AND a.user_id = ' . $user_id, 'left')
                ->where('l.type', $type)
                ->where('l.level', $lnum)
                ->where('(a.id IS NULL OR a.me_status = 0 OR a.partner_status = 0)', null, false)
                ->order_by('qnum');
        
        $res = $this->db->get();
        $result = array();
        if($res !== FALSE) {
            foreach($res->result_array() as $row) 
                $result[] = $row['qnum'];
        }
        
        return $result;
    }
    
    /**
     * Get an array of ethnicities
     * 
     * @return array
     */
    public function getEthnicity() {
        $Arr = array();
        $query = "SELECT * FROM ethnicity WHERE status = 1 ORDER BY CASE WHEN sort_order IS NULL THEN 1000 ELSE sort_order END , sort_order, main_id";
        $res = $this->db->query($query);
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $Arr[$row['id']]['info'] = $row;
                if(!isset($Arr[$row['id']]['childs'])) {
                    $Arr[$row['id']]['childs'] = array();
                }
                if($row['main_id'] != 0) {
                    if(!isset($Arr[$row['main_id']])) {
                        $Arr[$row['main_id']]['info'] = array();
                        $Arr[$row['main_id']]['childs'] = array($row['id']);
                    } else {
                        $Arr[$row['main_id']]['childs'][] = $row['id'];
                    }
                }
            }
        }
        return $Arr;
    }
    
    /**
     * Update privacy code for questions
     * 
     * @param array $data 
     * @return boolean
     */
    public function updatePrivacyCode($data) {
        $res = $this->db
                ->where('user_id', $data['user_id'])
                ->where('question_id', $data['question_id'])
                ->delete('question_privacy');
        if($res !== FALSE) {
            return $this->insertRow($data, 'question_privacy');
        }
        return FALSE;
    }
    
    /**
     * Get questions for the Outcome page
     * 
     * @param int $uid - user ID
     * @return array
     */
    public function getOutcomeQuestions($uid) {
        $questions = $this->db
                ->select('q.id, q.qnum, q.optional, if(qp.privacy_code is not null, qp.privacy_code, "low") as privacy_code', false)
                ->from($this->_table . ' as q')
                ->join('question_privacy as qp', 'qp.question_id = q.id AND qp.user_id = ' . $uid, 'left')
                ->where('level_id', 7)
                ->where('quest_type_id', 22)
                ->order_by('q.qnum', 'asc')
                ->get();
        return $questions !== FALSE ? $questions->result_array() : array();
    }

}

/* End of file question_model.php */
/* Location: ./application/models/question_model.php */