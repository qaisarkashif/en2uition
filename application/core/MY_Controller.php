<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $user_id;
    private $_available_classes = array('relationship', 'home', 'language', 'questionnaire', 'question', 'answer', 'outcome');
    protected $data = array();

    public function __construct() {
        parent::__construct();

        $this->load->model('user_model', "users");
        $this->load->model('auth_model', "authm");
        $this->load->library('Tmpl');

        if (!$this->authm->is_logged_in()) {
            if (IS_AJAX) {
                $this->output->set_status_header(400);
                exit;
            } elseif (isset($this->uri->segments[3]) && $this->uri->segments[3] == 'iframe') {
                echo 'The session has expired. Please login again.';
                exit;
            } else {
                redirect('/home');
            }
        }

        //keep flashdata
        if ($this->session->flashdata('qmode')) {
            $this->session->keep_flashdata('qmode');
        }

        $this->user_id = $this->authm->get_user_id(); //get user's ID
        $this->update_sess_info();
        $this->check_account_state();
        
        if(!$this->authm->is_joined() && !in_array($this->router->class, $this->_available_classes)) {
            if(IS_AJAX) {
                $this->output->set_status_header(404);
                exit;
            } else {
                redirect('/relationship');
            }
        }
    }
    
    public function echo_json($data = array(), $status = 200) {
        $this->output
                ->set_status_header($status)
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
    }

    private function update_sess_info() {
        $profile_id = $this->authm->get_profile_id();
        $user_profile = $this->users->get_user_profile($profile_id, 1); //get profile of the user by profile_ID
        if ($user_profile !== FALSE && count($user_profile) > 0) {//if the profile is found
            $this->session->set_userdata(array(
                'user_info' => $user_profile[0],
                'language' => $user_profile[0]['language']
            ));
            $row = array('is_online' => 1);
            $this->users->updateRow($this->user_id, $row);
        } else {
            $this->session->set_userdata(array(
                'user_info' => array(),
                'language' => 'english'
            ));
        }
    }
    
    public function check_account_state($user_id = null) {
        if (!$user_id) {
            $user_id = $this->user_id;
        }
        $account_active = $this->users->isAccountActive($user_id);
        if (!$account_active) {
            $this->lang->load('error');
            if ($user_id == $this->user_id) {
                show_error(lang('error_your_account_inactive'), 500, lang('error_access_denied'));
            } else {
                $this->data = array(
                    'top_menu' => 'inactive_account',
                    'header_data' => array(
                        'inner_navbar' => true,
                        'active_page' => 'inactive_account',
                        'user' => $this->authm->get_profile()
                    )
                );
                $this->data['main_content'] = $this->load->view('page/inactive_account', array('msg' => lang('error_this_account_inactive')), TRUE);
                $this->tmpl->show_template('include/layout', $this->data);
            }
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function send_email_notification($type, $to_user, $data = array()) {
        
        $Arr = array(
            'received-message', 
            'reply-to-comment', 
            'request-friendship', 
            'accept-friendship', 
            'view-answers-request', 
            'accept-answers-request',
            'profile-comment',
            'photo-comment'
        );

        $pos = array_search($type, $Arr);
        if ($pos === FALSE)
            return FALSE;

        $user = $this->users->get_user($to_user, "email, email_notifications");
        if (!$user)
            return FALSE;

        $emnf = $user->email_notifications;
        if($emnf[$pos] == 1) {
            $this->load->library('SendEmail');
            $this->sendemail->send($type, $user->email, $data);
        }
    }
    
    public function exit_404() {
        show_404();
        exit;
    }
    
    public function exit_json_404() {
        $this->echo_json(array(), 404);
        exit;
    }

}
