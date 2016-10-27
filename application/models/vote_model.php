<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vote_model extends Default_model {

    private $table = false;
    private $fk_field = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Initialization of the model parameters
     * 
     * @param int $target - comment target
     */
    public function init($target) {
        switch ($target) {
            case 'photo':
                $this->table = 'photo_vote';
                $this->fk_field = 'photo_id';
                break;
            case 'photo_comment':
                $this->table = 'photo_comment_vote';
                $this->fk_field = 'comment_id';
                break;
            case 'profile_comment':
                $this->table = 'profile_comment_vote';
                $this->fk_field = 'comment_id';
                break;
            case 'topic_comment':
                $this->table = 'topic_comment_vote';
                $this->fk_field = 'comment_id';
                break;
        }
    }

    /**
     * Add new vote
     * 
     * @param array $data
     * @return boolean
     */
    public function add($data) {
        if ($this->table && $this->fk_field) {
            $data[$this->fk_field] = $data['id'];
            //check if the user already voted for this
            $vote = $this->db
                    ->where($this->fk_field, $data['id'])
                    ->where('user_id', $data['user_id'])
                    ->limit(1)
                    ->get($this->table);
            if ($vote === FALSE) {
                return FALSE;
            }
            unset($data['id']);
            if ($vote->num_rows() == 1) {//user already voted for this
                $vote = $vote->row();
                if($vote->type == $data['type']) {//if the type of votes was unchanged
                    //cancel previous vote
                    $res = $this->deleteRow($vote->id, $this->table);
                } else {
                    $res = $this->updateRow($vote->id, array('type' => $data['type'], 'created' => date("Y-m-d H:i:s")), $this->table);
                }
            } else {//user not voted for this. Add new record
                $res = $this->insertRow($data, $this->table);
            }
            return $res;
        }

        return FALSE;
    }

    /**
     * Get vote by specified ID
     * 
     * @param int $id
     * @return null (if vote not found) or object
     */
    public function getById($id) {
        $vote = $this->db->where('id', $id)->limit(1)->get($this->table);
        if($vote !== FALSE && $vote->num_rows() == 1) {
            return $vote->row();
        }
        return NULL;
    }
    
    /**
     * Get numbers of likes\dislikes
     * 
     * @param int $id - target field ID
     * @return array
     */
    public function getTotals($id) {
        $totals = array('like' => 0, 'dislike' => 0);
        
        $info = $this->db
                ->select('type, count(id) as total', false)
                ->where($this->fk_field, $id)
                ->group_by('type')
                ->get($this->table);
        
        if($info !== FALSE) {
            foreach($info->result_array() as $row) {
                $totals[$row['type']] = $row['total'];
            }
        }
        
        return $totals;
    }
    
    /**
     * Get list of usernames who voted
     * 
     * @param int $id - target field ID
     * @param string $type - 'like', 'dislike' or 'both' (default)
     * @param int $limit - the number of lines for each type of list. If null - the list has no limit 
     * @return array
     */
    public function whoVoted($id, $type = 'both', $limit = 10) {
        $who_voted = array('like' => array(), 'dislike' => array());
        
        $limit = !empty($limit) ? " LIMIT " . $limit : '';
        $query = array();
        if($type == 'like' || $type == 'both') {
            $query[] = "SELECT * FROM (SELECT v.type, u.username FROM ".$this->table." v JOIN user_profile u USING (user_id) WHERE v.type = 'like' AND " . $this->fk_field. " = {$id} ORDER BY v.created DESC {$limit}) AS t1";
        }
        if($type == 'dislike' || $type == 'both') {
            $query[] = "SELECT * FROM (SELECT v.type, u.username FROM ".$this->table." v JOIN user_profile u USING (user_id) WHERE v.type = 'dislike' AND " . $this->fk_field. " = {$id} ORDER BY v.created DESC {$limit}) AS t2";
        }
        $voted = $this->db->query(implode(" UNION ", $query));
        
        if($voted !== FALSE) {
            foreach($voted->result_array() as $row) {
                $who_voted[$row['type']][] = $row['username'];
            }
        }
        
        return $who_voted;
    }

}

/* End of file vote_model.php */
/* Location: ./application/models/vote_model.php */