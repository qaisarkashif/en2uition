<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'user';
        $this->_pk = 'id';
    }

    public function is_logged_in()
	{
        if($this->session->userdata('logged'))
            return TRUE;
        return FALSE;
	}

    public function get_user_id() {
        return $this->session->userdata['user_info']['user_id'];
    }

    public function get_username() {
        return $this->session->userdata['user_info']['username'];
    }

    public function get_profile() {
        return $this->session->userdata['user_info'];
    }

    public function get_profile_id() {
        return $this->session->userdata['user_info']['id'];
    }
    
    public function is_joined() {
        $joined = (int)$this->session->userdata['user_info']['joined'];
        return $joined === 1;
    }

    public function get_dailymood() {
        return array(
            'dailymood' => $this->session->userdata['user_info']['dailymood'],
            'dailymood_hidden' => $this->session->userdata['user_info']['dailymood_hidden']
        );
    }

    public function get_my_group() {
        return array(
            'color' => $this->session->userdata['user_info']['color'],
            'shape_id' => (isset($this->session->userdata['user_info']['shape_id']) ? $this->session->userdata['user_info']['shape_id'] : 0),
            'shapename' => (isset($this->session->userdata['user_info']['shape_id']) ? $this->session->userdata['user_info']['shapename'] : '')
        );
    }

    public function get_user_photo($type, $user = null, $set_image = true) {
        $type = strtolower($type);
        if(!in_array($type, array('forum', 'homepage', 'orig', 'profile', 'friend'))) {
            return FALSE;
        }
        if(!$user) {
            $user = $this->get_profile();
        }
        
        if (!$user['profile_image']) {
            $img = constant("DEF_USER_AVA_" . strtoupper($type));
        } else {
            if($type == 'orig') {
                $img = sprintf(USER_AVA_ORIG, $user['user_id'], $user['profile_image']);
            } else {
                $img = sprintf(constant("USER_AVA_" . strtoupper($type)), $user['user_id'], substr(strrchr($user['profile_image'], '.'), 1));
            }
        }
        
        return $set_image ? set_image($img) : $img;
    }

    /**
     * Activation of user account
     * @param int $user_id
     * @param string $email_key
     * @return int or boolean (1 - if the activation link is invalid TRUE/FALSE - as a result of the activation)
     */
    public function activate_user_account($user_id, $email_key) {
        $user = $this->db
                ->where($this->_pk, $user_id)
                ->where('email_key', $email_key)
                ->where('activated', 0)
                ->limit(1)
                ->get($this->_table);

        if($user !== FALSE && $user->num_rows() == 1) {
            $activated = $this->updateRow($user_id, array('activated' => 1, 'email_key' => NULL));
            return $activated;
        } else {
            return 1;
        }
    }

    /**
     * Check user credentials
     * @param string $email
     * @param string $password
     * @return int (-1 - if user not found/deleted/blocked/not activated, -2 - incorrect password, else - user_id will be returned as the result)
     */
    public function check_user_credentials($email, $password) {
        $user = $this->db
                ->where('LOWER(email)', $email)
                ->where('activated', 1)
                ->where('blocked', 0)
                ->where('deleted', 0)
                ->limit(1)
                ->get($this->_table);

        if($user !== FALSE && $user->num_rows() == 1) {
            $hash = $user->row()->password;
            if(crypt($password, $hash) == $hash) {
                return $user->row()->id;
            } else {
                return -2;
            }
        }

        return -1;
    }

    /**
     * Check user password
     * @param int $user_id
     * @param string $password
     * @return boolean
     */
    public function check_user_password($user_id, $password) {
        $user = $this->db
                ->select('password')
                ->where($this->_pk, $user_id)
                ->limit(1)
                ->get($this->_table);

        if($user !== FALSE && $user->num_rows() == 1) {
            $hash = $user->row()->password;
            if(crypt($password, $hash) == $hash) {
                return TRUE;
            }
        }

        return FALSE;
    }
    
    /**
     * Check if specified email is registered on the site
     * 
     * @param string $email
     * @return boolean
     */
    public function isEmailRegistered($email = "") {
        $this->db
                ->where('email', $email)
                ->limit(1);
        $res = $this->db->get('user');
        return $res !== FALSE && $res->num_rows() == 1;
    }
    
    /**
     * Set a temporary password
     * 
     * @param string $email
     * @return boolean
     */
    public function resetPassword($email) {
        $new_pwd = random_string();
        $this->db
                ->set('password', crypt($new_pwd))
                ->where('email', $email);
        $res = $this->db->update('user');
        if($res !== FALSE) {
            return $new_pwd;
        }
        return FALSE;
    }

}

/* End of file auth_model.php */
/* Location: ./application/models/auth_model.php */