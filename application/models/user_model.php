<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'user';
        $this->_pk = 'id';
    }

    /**
     * Create user record
     * @param array $userinfo
     * @return boolean or int (user_id if the record was successfully created)
     */
    public function create_user($userinfo) {
        $user = $this->insertRow($userinfo);
        if ($user === FALSE)
            return FALSE;
        else
            return $this->db->insert_id();
    }

    /**
     * Create user profile
     * @param array $data - user info
     * @return boolean
     */
    public function create_user_profile($data) {
        $user = $this->db
                ->where('user_id', $data['user_id'])
                ->limit(1)
                ->get('user_profile');
        if ($user !== FALSE && $user->num_rows() == 1) {
            return TRUE;
        } else {
            $res = $this->insertRow($data, 'user_profile');
            return $res;
        }
    }

    /**
     * Get user profile
     * @param int or string $val - key value
     * @param int $by - in what field? (1 - user profile id, 2 - user id, 3 - user email, 4 - username)
     * @return array or FALSE (if user profile not found)
     */
    public function get_user_profile($val, $by = 1) {
        switch ($by) {
            case 1: $key = 'up.id';
                break;
            case 2: $key = 'u.id';
                break;
            case 3: $key = 'LOWER(u.email)';
                break;
            case 4: $key = 'up.username';
                break;
        }
        $select_fields = array(
            'up.*', 
            'u.activated',
            'u.active', 
            'u.is_online', 
            'u.blocked', 
            'u.deleted', 
            'u.language', 
            'u.email_notifications',
            'shape.id as shapeid',
            'shape.name as shapename', 
            'u.users_guide_hidden',
            'u.joined'
        );
        $profile = $this->db
                ->select(implode(',', $select_fields), false)
                ->from('user_profile as up')
                ->join('user as u', 'u.id = up.user_id')
                ->join('shape', 'shape.id = up.shape_id', 'left')
                ->where($key, $val)
                ->limit(1)
                ->get();
        if ($profile !== FALSE && $profile->num_rows() == 1) {
            return $profile->result_array();
        }

        return FALSE;
    }
    
    /**
     * Get user's record
     * 
     * @param int $user_id - user ID
     * @param string $fields - fields for SELECT
     * @return mixed (object or null if not found)
     */
    public function get_user($user_id, $fields = '*') {
        $user = $this->db
                ->select($fields, false)
                ->where($this->_pk, $user_id)
                ->limit(1)
                ->get($this->_table);
        return $user !== FALSE && $user->num_rows() == 1 ? $user->row() : null;
    }
    
    /**
     * Get user's email
     * @param int $user_id - user ID
     * @return mixed (string or null if not found)
     */
    public function get_user_email($user_id) {
        $email = $this->db
                ->select('email')
                ->where($this->_pk, $user_id)
                ->limit(1)
                ->get($this->_table);
        return $email !== FALSE && $email->num_rows() == 1 ? $email->row()->email : null;
    }

    /**
     * Toggle dailymood
     * @param int $profile_id
     * @return boolean
     */
    public function toggle_dailymood($profile_id) {
        $res = $this->db
                ->set('dailymood_hidden', '(CASE WHEN dailymood_hidden = 1 THEN 0 ELSE 1 END)', false)
                ->where('id', $profile_id)
                ->update('user_profile');
        return $res !== FALSE;
    }
    
    /**
     * Get user's daily mood (for compare)
     * 
     * @param int $user_id
     * @param array $filters
     * @return array
     */
    public function getDailyMoodData($user_id, $filters = array()) {
        $data = array();
        $dm_max = 185.0;
        $seg_cnt = 16;
        $step = $to = round($dm_max/$seg_cnt, 1);
        $from = 0;
        
        while($from < $dm_max) {
            $data[] = array(
                'range' => array('from' => $from, 'to' => $to),
                'users_count' => 0
            );
            $from = $to;
            $to += $step;
        }
        
        $this->db
                ->select('up.dailymood')
                ->from('user_profile as up')
                ->where('dailymood_hidden', 0);
        
        if(isset($filters['friend']) && $filters['friend'] == 'all') {
            $join = "(select distinct if(user_id = {$user_id}, friend_id, user_id) as uid from user_friend where user_id = {$user_id} or friend_id = {$user_id}) as tmp";
            $this->db->join($join, "tmp.uid = up.user_id");
        }
        
        foreach(array('country', 'state', 'city', 'color', 'shape') as $f) {
            if(isset($filters[$f]) && !empty($filters[$f])) {
                if($f == 'shape' && $filters[$f] == -1) {
                    $this->db->where('(up.shape_id is null or up.shape_id = "")', null, false);
                } else {
                    $this->db->where($f == 'shape' ? "up.shape_id" : "up.{$f}", $filters[$f]);
                }
            }
        }
        
        $res = $this->db->get();
        
        
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $user_dm = (float) $row['dailymood'];
                for($i = 0; $i < count($data); $i++) {
                    $r = $data[$i]['range'];
                    if($user_dm >= $r['from'] && $user_dm < $r['to']) {
                        $data[$i]['users_count']++;
                        break;
                    }
                }
            }
            $max_users = 0;
            foreach($data as $d) {
                $max_users = max($max_users, $d['users_count']);
            }
            $data['max_users'] = $max_users;
            $data['all_users_count'] = $res->num_rows();
        }
        
        return $data;
    }
    
    /**
     * Get granted permissions
     * 
     * @param int $to_user - permissions granted to user
     * @param mixed $where - search terms
     * @return array
     */
    public function get_privacy_permissions($to_user = null, $where = null) {
        $perms = array();
        if(!empty($to_user)) {
            $this->db->where('to_user', $to_user);
        }
        if(!empty($where)) {
            $this->db->where($where, null, false);
        }
        $res = $this->db->get('privacy_permission');        
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $i = $row['type'];
                $j = $row['to_user'];
                $k = $row['id'];
                if(!isset($perms[$i][$j])) {
                    $perms[$i][$j] = array();
                }
                if($row['type'] == 'question') {
                    $row['privacy'] = json_decode($row['privacy'], true);
                }
                unset($row['to_user'], $row['type'], $row['id']);
                $perms[$i][$j][$k] = $row;
            }
        }
        return $perms;
    }
    
    /**
     * Check if the user is present in the blacklist of another user
     * 
     * @param int $uid1 - user ID who owns blacklist
     * @param int $uid2 - user ID which must be checked in the blacklist
     * @return boolean
     */
    public function in_black_list($uid1, $uid2) {
        $res = $this->db
                ->where('user1', $uid1)
                ->where('user2', $uid2)
                ->get('black_list');
        return $res !== FALSE && $res->num_rows() > 0;
    }
    
    /**
     * Update blacklist
     * 
     * @param string $action - 'add' or 'remove'
     * @param int $list_owner - user ID
     * @param int $uid - user ID
     * @return boolean 
     */
    public function updateBlackList($action, $list_owner, $uid) {
        $row = array(
            'user1' => $list_owner, 
            'user2' => $uid
        );
        $res = $this->db->where($row)->get('black_list');
        if($res !== FALSE) {
            if($action == 'add') {
                return $res->num_rows() > 0 ? TRUE : $this->insertRow($row, 'black_list');
            } else {
                return $res->num_rows() > 0 ? $this->db->where($row)->delete('black_list') : TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Get my blacklist
     * 
     * @param int $uid - user ID
     * @param boolean $add_username (optional)
     * @return array
     */
    public function getMyBlackList($uid, $add_username = false) {
        $list = array();
        $this->db
                ->select('user2')
                ->where('user1', $uid);
        if($add_username) {
            $this->db
                    ->select('u.username')
                    ->join('user_profile as u', 'u.user_id = black_list.user2')
                    ->order_by('u.username');
        }
        $res = $this->db->get('black_list');
        if($res !== FALSE && $res->num_rows() > 0) {
            foreach($res->result_array() as $row) {
                $usr = $row['user2'];
                if ($add_username) {
                    $list[$usr] = $row['username'];
                } else {
                    $list[] = $usr;
                }
            }
        }
        return $list;
    }
    
    /**
     * Get shape by name
     * 
     * @param string $name - shape name
     * @return object or null 
     */
    public function getShapeByName($name) {
        if($name) {
            $name = strtolower(str_replace('-', '_', $name));
            $this->db->where('name', $name)->limit(1);
            $shape = $this->db->get('shape');
            if($shape !== FALSE && $shape->num_rows() == 1) {
                return $shape->row();
            }
        }
        return null;
    }
    
    /**
     * Search usernames
     * 
     * @param string $term - search term
     * @return array
     */
    public function searchUsernames($term) {
        $names = array();
        
        $res = $this->db
                ->select('user_id, username')
                ->like('username', $term, 'after')
                ->get('user_profile');
        
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $names[$row['user_id']] = $row['username'];
            }
        }
        
        return $names;
    }
    
    /**
     * Search users by specified rules
     * 
     * @param string $where
     * @param int $page
     * @return array
     */
    public function searchUsers($where, $page = -1) {
        $this->db
                ->select('up.user_id, up.username, up.profile_image')
                ->from('user_profile as up')
                ->join('user as u', 'u.id = up.user_id')
                ->where($where, null, false)
                ->order_by('up.user_id', 'asc');
        if($page > -1) {
            $this->db->limit(PROSLD_LIMIT, $page * PROSLD_LIMIT);
        }
        $users = $this->db->get();
        return $users !== FALSE ? $users->result_array() : array();
    }
    
    /**
     *  Check if account is active
     * 
     * @param int $user_id - user ID
     * @return boolean
     */
    public function isAccountActive($user_id) {
        $res = $this->db
                ->select('active')
                ->where($this->_pk, $user_id)
                ->limit(1)
                ->get($this->_table);
        if($res !== FALSE && $res->num_rows() == 1) {
            return $res->row()->active == 1;
        }
        return FALSE;
    }
    
    /**
     * Remove current user's avatar
     * 
     * @param int $user_id - user ID
     * @return boolean
     */
    public function removeAvatar($user_id) {
        $this->db
                ->set('profile_image', NULL)
                ->where('user_id', $user_id);
        $res = $this->db->update('user_profile');
        if($res !== FALSE) {
            $this->load->helper('files');
            $path = realpath('.' . sprintf(USER_AVA_FOLDER, $user_id));
            delete_all_files($path);
            return TRUE;
        }
        return FALSE;
    }

}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */