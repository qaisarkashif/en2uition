<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questionnaire_model extends Default_model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get levels of questionnaire
     * 
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $uid - user ID
     * @return array
     */
    public function getLevels($type = "", $uid = null) {
        $levels = array();
        $this->db
                ->select('l.*, COUNT(q.id) as quest_count', false)
                ->from('q_level as l')
                ->join('q_question as q', 'q.level_id = l.id', 'left')
                ->group_by('l.id');
        if (!empty($type)) {
            $this->db->where('l.type', $type);
        }
        if(!empty($uid)) {
            $this->db
                    ->select('COUNT(a.id) as answered_count', false)
                    ->join('q_answer as a', 'a.question_id = q.id AND a.user_id = ' . $uid . ' AND (a.me_status = 1 AND a.partner_status = 1)', 'left');
        }
        $result = $this->db->order_by('l.type asc, l.level asc')->get();
        if ($result !== FALSE) {
            foreach($result->result_array() as $row) {
                $key = !empty($type) ? $row['level'] : $row['id'];
                $levels[$key] = $row;
            }
        }
        return $levels;
    }
    
    /**
     * Get level progress
     * 
     * @param int $lid - level ID
     * @param int $uid - user ID
     * @return float
     */
    public function getLevelProgress($lid, $uid) {
        $progress = 0;
        
        $this->db
                ->select('a.me_status, a.partner_status')
                ->from('q_question as q')
                ->join('q_answer as a', 'a.question_id = q.id AND a.user_id = ' . $uid, 'left')
                ->where('q.level_id', $lid);
        
        $res = $this->db->get();
        if($res !== FALSE) {
            $quest_count = $res->num_rows();
            if($quest_count > 0) {
                foreach($res->result_array() as $row) {
                    $progress += $row['me_status'] == 1 ? 0.5 : 0;
                    $progress += $row['partner_status'] == 1 ? 0.5 : 0;
                }
                $progress = ($progress / $quest_count) * 100;
            }
        }
        
        $progress = $progress < 99.85 ? $progress : 100;
        
        return round($progress, 2);
    }
    
    /**
     * Get the overall progress of the questionnaire
     * 
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $uid - user ID
     * @return float
     */
    public function getOverallProgress($type, $uid) {
        $levels = $this->getLevels($type, $uid);
        $questions_count = $answers_count = 0;
        foreach($levels as $level) {
            $questions_count += $level['quest_count'];
            $answers_count += $level['answered_count'];
        }
        if($questions_count > 0) {
            $progress = ($answers_count / $questions_count) * 100;
            return $progress < 99.85 ? $progress : 100;
        }
        return 0;
    }
    
    /**
     * Get the progress of the questionnaire
     * 
     * @param int $user_id - user ID
     * @return array;
     */
    public function getQuestionnaireProgress($user_id) {
        $res = array('past' => array(), 'present' => array());
        $levels = $this->getLevels("", $user_id);
        foreach($levels as $level) {
            $res[$level['type']][$level['level']] = array(
                'title' => lang('q_' . $level['label'] . '_title'),
                'progress' => $level['answered_count'] . '/' . $level['quest_count']
            );
        }
        return $res;
    }
    
    /**
     * Get the number of answered questions (in each level)
     * 
     * @param string $type - type of questionnaire ('past' or 'present')
     * @param int $uid - user ID
     * @return array
     */
    public function getLevelAnsweredQuestion($type, $uid) {
        $levels = array();
        
        $this->db
                ->select('l.id, l.level, count(a.id) as answ_count', false)
                ->from('q_level as l')
                ->join('q_question as q', 'q.level_id = l.id', 'left')
                ->join('q_answer as a', 'a.question_id = q.id AND a.user_id = ' . $uid, 'left')
                ->where('l.type', $type)
                ->where('a.me_status', 1)
                ->where('a.partner_status', 1);
        $res = $this->db->get();
        if ($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $levels[$row['level']] = $row['answ_count'];
            }
        }
        
        return $levels;
    }
    
    /**
     * Get a list of questions specified level and the code of privacy
     * 
     * @param int $uid - user ID
     * @param int $id - level ID
     * @param string $code - privacy code ('low', 'medium' or 'high')
     * @return array
     */
    public function getPrivacyQuestions($uid, $id, $code = 'low') {
        $this->db
                ->select('q.id, q.qnum, q.label, l.type, if(qp.privacy_code is not null, qp.privacy_code, "low") as privacy_code', false)
                ->from('q_question as q')
                ->join('q_level as l', 'l.id = q.level_id')
                ->join('question_privacy as qp', 'qp.question_id = q.id AND qp.user_id = ' . $uid, 'left')
                ->where('q.level_id', $id)
                ->having('privacy_code', $code)
                ->order_by('q.qnum', 'asc');
        
        $res = $this->db->get();
        return $res !== FALSE ? $res->result_array() : array();
    }
    
    /**
     * Get the number of users who responded to all the questions specified levels
     * 
     * @param array $levels - IDs of levels
     * @return int
     */
    public function getFullStatistic($levels) {
        $query = "
            SELECT user_id, COUNT(id) AS lcount FROM (
                SELECT l.id, a.user_id, COUNT(a.id) as answ_count, s.quest_count
                FROM q_level AS l
                JOIN (
                    SELECT l.id, COUNT(q.id) AS quest_count
                    FROM q_level AS l
                    LEFT JOIN q_question AS q ON q.level_id = l.id
                    WHERE l.id IN (" . implode(',', $levels) . ")
                    GROUP BY l.id
                ) AS s ON s.id = l.id
                LEFT JOIN q_question AS q ON q.level_id = l.id
                LEFT JOIN q_answer AS a ON (a.question_id = q.id and a.me_status = 1 and a.partner_status = 1)
                WHERE l.id IN (" . implode(',', $levels) . ")
                GROUP BY l.id, a.user_id
                HAVING answ_count = quest_count AND quest_count <> 0
            ) tmp
            GROUP BY user_id
            HAVING lcount = " . count($levels);
        $res = $this->db->query($query);
        return $res !== FALSE ? $res->num_rows() : 0;
    }
    
    /**
     * Get statistics a questionnaire of my friends
     * 
     * @param int $user_id - user ID
     * @return array()
     */
    public function getFriendsStatistic($user_id) {
        $stats = array();
        $query = "SELECT u.id, q.level_id, tmp2.type, tmp2.level, tmp2.quest_count, count(q.id) as answ_count
            FROM  user as u 
                JOIN (SELECT if(user_id = {$user_id}, friend_id, user_id) as user_id FROM user_friend WHERE user_id = {$user_id} OR friend_id = {$user_id}) as tmp ON (tmp.user_id = u.id)
                LEFT JOIN q_answer as a ON (a.user_id = u.id AND a.me_status = 1 AND a.partner_status = 1)
                LEFT JOIN q_question as q ON (a.question_id = q.id)
                LEFT JOIN (SELECT lvl.id, lvl.type, lvl.level, count(q.id) as quest_count FROM q_level as lvl JOIN q_question as q ON q.level_id = lvl.id GROUP BY lvl.id) as tmp2 ON (tmp2.id = q.level_id)
            GROUP BY u.id, q.level_id";
        $stat = $this->db->query($query);
        if($stat !== FALSE) {
            foreach($stat->result_array() as $row) {
                extract($row);
                if(!isset($stats[$id])) {
                    $stats[$id] = array(
                        'past' => array(),
                        'present' => array()
                    );
                }
                if($level_id) {
                    $stats[$id][$type][$level] = $quest_count > 0 && $quest_count == $answ_count ? 1 : 0;
                }
            }
        }
        return $stats;
    }

}

/* End of file questionnaire_model.php */
/* Location: ./application/models/questionnaire_model.php */