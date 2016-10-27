<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation', 'security'));
        $this->load->model('user_model', 'users');
        $this->load->model('auth_model', 'authm');
    }

    /**
     * User registration
     */
    public function signup() {
        $output = array('errors' => array());
        
        if($this->input->post()) {
            $rules = array(
                array('field' => 'email', 'label' => lang('email'), 'rules' => 'trim|required|valid_email|is_unique[user.email]|xss_clean'),
                array('field' => 'username', 'label' => lang('username'), 'rules' => 'trim|required|is_unique[user_profile.username]|xss_clean'),
                array('field' => 'password', 'label' => lang('password'), 'rules' => 'trim|required|min_length[5]|xss_clean'),
                array('field' => 'passconf', 'label' => lang('confirm_password'), 'rules' => 'trim|required|min_length[5]|matches[password]|xss_clean'),
            );
            $this->form_validation->set_rules($rules);
            $this->form_validation->set_message('is_unique', $this->lang->line('auth_unique'));
            if ($this->form_validation->run() === TRUE) {
                $password = crypt(set_value('password'));
                $userinfo = array(
                    'email' => set_value('email'),
                    'password' => $password,
                    'last_ip' => $this->input->ip_address(),
                    'email_key' => md5(rand().microtime())
                );
                //add new user to DB
                $user_id = (int)$this->users->create_user($userinfo);
                //if user created successfully
                if($user_id > 0) {
                    //create user profile
                    $this->users->create_user_profile(array('user_id' => $user_id, 'username' => set_value('username')));
                    //send email with activation link
                    $this->load->library('SendEmail');
                    $this->sendemail->send('activate', $userinfo['email'], array('user_id' => $user_id, 'email_key' => $userinfo['email_key']));
                }
            } else {
                $output['errors'] = $this->form_validation->get_errors_array();
            }
        }
        
        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
    }

    /**
     * Log in
     */
    public function signin() {
        $output = array('errors' => array());
        
        if($this->input->post()) {
            $rules = array(
                array('field' => 'email', 'label' => 'Email', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'password', 'label' => 'password', 'rules' => 'trim|required|min_length[5]|xss_clean')
            );
            
            $this->form_validation->set_rules($rules);
            if($this->form_validation->run() === TRUE) {
                $res = (int)$this->authm->check_user_credentials(set_value('email'), set_value('password'));
                if($res > 0) {
                    $this->login(set_value('email'), 3);
                    $output['joined'] = $this->authm->is_joined(); 
                } elseif($res == -1) {
                    $output['errors'] = array(
                        'email' => $this->lang->line('auth_signin_email_error'),
                        'password' => ''
                    );
                } elseif($res == -2) {
                    $output['errors']['password'] = $this->lang->line('auth_signin_password_error');
                }
            } else {
                $output['errors'] = $this->form_validation->get_errors_array();
            }
        }
        
        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
    }

    /**
     * Log out
     */
    public function signout() {
        $user_id = $this->authm->get_user_id();
        $this->users->updateRow($user_id, array('is_online' => 0));
        $this->session->unset_userdata();
        $this->session->sess_destroy();
        redirect('/home');
    }
    
    /**
     * Activation of a user Account
     * @param int $user_id
     * @param string $email_key
     */
    public function activate($user_id, $email_key) {
        $activated = $this->authm->activate_user_account($user_id, $email_key);
        if($activated === TRUE) {
            $this->login($user_id, 2);
            redirect('/relationship');
        } else {
            if($activated === 1) {
                show_error($this->lang->line('auth_activate_error_1'));
            } else {
                show_error($this->lang->line('auth_activate_error_2'));
            }
        }
    }
    
    public function forgot_password() {
        $output = array('errors' => array());
        
        if ($this->input->post()) {
            $rules = array(
                array('field' => 'email', 'label' => 'Email', 'rules' => 'trim|required|xss_clean|callback_email_registered')
            );

            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() === TRUE) {
                $email = set_value('email');
                $reset_pwd = $this->authm->resetPassword($email);
                if($reset_pwd !== FALSE) {
                    $this->load->library('SendEmail');
                    $this->sendemail->send('reset-password', $email, array('rand_pwd' => $reset_pwd));
                    $this->lang->load('success');
                    $output['message'] = array(
                        'title' => lang('auth_pwd_reset_title'),
                        'text' => lang('success_password_reset')
                    );
                } else {
                    $output['errors'] = '<p class="validation-error">' . lang('auth_reset_pwd_fail') . '</p>';
                }
            } else {
                $output['errors'] = validation_errors('<p class="validation-error">', '</p>');
            }
        }

        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
    }
    
    private function login($by_val, $by_type) {
        $user_profile = $this->users->get_user_profile($by_val, $by_type);
        if ($user_profile !== FALSE && count($user_profile) > 0) {
            $user_profile = $user_profile[0];
            $this->session->set_userdata(array(
                'user_info' => $user_profile,
                'logged' => 1
            ));
            $row = array(
                'active' => 1,
                'last_ip' => $this->input->ip_address()
            );
            $this->users->updateRow($user_profile['user_id'], $row);
        }
    }
    
    public function email_registered($email) {
        $this->lang->load('auth');
        $reg = $this->authm->isEmailRegistered($email);
        if (!$reg) {
            $this->form_validation->set_message('email_registered', lang('auth_email_not_registered'));    
            return false;
        } else {
            return true;
        }
    }

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */