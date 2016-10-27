<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Friend_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'user_friend';
        $this->_pk = 'id';
    }

    /**
     * Get a list of the user's friends
     * 
     * @param int $user_id
     * @param array $ava_arr - an array of necessary avatars
     * @return array
     */
    public function getUserFriends($user_id, $ava_arr = array()) {
        $this->load->model('auth_model', 'authm');
        
        $friends = array();
        $query = "
            SELECT tmp.* 
            FROM (
                SELECT 
                    p.user_id, p.username, p.dailymood, p.dailymood_hidden, p.color, p.profile_image, f.id as fid 
                FROM 
                    " . $this->_table . " as f
                    JOIN user_profile p ON (p.user_id = f.friend_id AND f.user_id = {$user_id}) OR (p.user_id = f.user_id AND f.friend_id = {$user_id})
            ) as tmp
            JOIN user ON (user.active = 1 AND user.id = tmp.user_id)
        ";
        
        $result = $this->db->query($query);
        
        if ($result !== FALSE) {
            foreach ($result->result_array() as $row) {
                $avatars = array();
                foreach ($ava_arr as $avaname) {
                    $avatars[$avaname] = $this->authm->get_user_photo($avaname, $row);
                }
                $row['avatars'] = $avatars;
                $friends[$row['user_id']] = $row;
            }
        }
        return $friends;
    }

    /**
     * Get friend by his id
     * 
     * @param int $user_id
     * @param int $friend_id
     * @return null (if not found) or array
     */
    public function getFriendById($user_id, $friend_id) {
        $friends = $this->getUserFriends($user_id);
        if (isset($friends[$friend_id])) {
            return $friends[$friend_id];
        }
        return NULL;
    }
    
    /**
     * Check whether the user is a friend of mine
     * 
     * @param int $user_id - user ID
     * @param int $friend_id - friend ID
     * @return boolean
     */
    public function isMyFriend($user_id, $friend_id) {
        $res = $this->db
                ->where("(user_id = {$user_id} AND friend_id = {$friend_id})")
                ->or_where("(user_id = {$friend_id} AND friend_id = {$user_id})")
                ->limit(1)
                ->get('user_friend');
        return $res !== FALSE && $res->num_rows() == 1;
    }
    
    /**
     * Get an array of IDs (my friends)
     * 
     * @param int $user_id - user ID
     * @return array
     */
    public function getFriendsIDs($user_id) {
        $IDs = array();
        
        $res = $this->db
                ->select("if(user_id = {$user_id}, friend_id, user_id) as id", false)
                ->where("user_id", $user_id)
                ->or_where("friend_id", $user_id)
                ->get('user_friend');
                
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $IDs[] = $row['id'];
            }
        }
        return $IDs;
    }
    
    /**
     * Delete this user from the list of your friends
     *
     * @param int $my_id - my user's ID
     * @param int $friend_id - friend ID
     * @return boolean
     */
    public function unfriend($my_id, $friend_id) {
        $this->db->trans_start();        
        $del_friend_query = "DELETE FROM " . $this->_table .  " WHERE (user_id = {$my_id} AND friend_id = {$friend_id}) OR (user_id = {$friend_id} AND friend_id = {$my_id})";
        $this->db->query($del_friend_query);        
        $del_perms_query = "DELETE FROM privacy_permission WHERE (from_user = {$my_id} AND to_user = {$friend_id}) OR (to_user = {$my_id} AND from_user = {$friend_id})";
        $this->db->query($del_perms_query);
        $this->db->trans_complete();        
        return $this->db->trans_status();
    }

}

/* End of file friend_model.php */
/* Location: ./application/models/friend_model.php */