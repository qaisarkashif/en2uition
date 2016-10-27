<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Relationship extends MY_Controller {

    public function __construct() {
        parent::__construct();

        if ($this->authm->is_joined() && !IS_AJAX) {
            redirect("/homepage");
        }
        $this->data['header_data']['joined'] = $this->authm->is_joined();
        $this->load->library('Tmpl');
        $this->lang->load('relationship');
        $this->tmpl->add_scripts("relationships/relationship.js");
    }

    public function index($top_menu = "relationship") {
        $view = $top_menu == "relationship" ? "index" : $top_menu;
        if ($top_menu !== "relationship") {
            $this->tmpl->add_scripts("relationships/{$top_menu}.js");
        }
        $this->data['top_menu'] = $top_menu;
        $this->data['main_content'] = $this->load->view("relationship/{$view}", '', TRUE);
        $this->tmpl->show_template('relationship/layout', $this->data);
    }

    public function set_user_color() {
        if (IS_AJAX) {
            $output = array('errors' => '');
            $color = $this->input->post('color', true);
            if ($color) {
                $row = array(
                    'color' => $color,
                    'relationship' => $this->base->get_relationship($color)
                );
                $prof_id = $this->authm->get_profile_id();
                $res = $this->users->updateRow($prof_id, $row, 'user_profile');
                if (!$res) {
                    $output['errors'] = lang('error_update_profile_info');
                }
            } else {
                $output['errors'] = lang('error_empty_input_param');
            }
            $this->echo_json($output);
        }
    }

    public function join() {
        $this->users->updateRow($this->user_id, array('joined' => 1));
        redirect('/homepage');
    }

    public function lite_settings() {
        $this->load->view('profile/lite-settings');
    }

    public function update_lite_settings() {
        $this->load->library('form_validation');

        $output = array('errors' => array());

        if ($this->input->post()) {
            $old_pwd_rules = "trim|xss_clean";
            $conf_pwd_rules = "trim|min_length[5]|matches[new_password]|xss_clean";
            if ($this->input->post('new_password', true)) {
                $old_pwd_rules .= "|required|callback_check_old_password";
                $conf_pwd_rules .= "|required";
            }
            $username = $this->input->post('username');
            $unique = '|is_unique[user_profile.username]';
            if ($username !== FALSE && trim($username)) {
                $cur_username = $this->authm->get_username();
                if (trim($cur_username) == trim($username)) {
                    $unique = "";
                }
            }
            $rules = array(
                array('field' => 'username', 'label' => "'Username'", 'rules' => 'trim|required' . $unique . '|max_length[30]|xss_clean'),
                array('field' => 'old_password', 'label' => "'Old password'", 'rules' => $old_pwd_rules),
                array('field' => 'new_password', 'label' => "'New password'", 'rules' => 'trim|min_length[5]|xss_clean'),
                array('field' => 'new_password1', 'label' => "'Confirm new password'", 'rules' => $conf_pwd_rules),
                array('field' => 'lang', 'label' => "'Site language'", 'rules' => 'trim|required|xss_clean')
            );
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() === TRUE) {
                if ($unique !== "") {
                    $res = $this->users->updateRow($this->user_id, array('username' => set_value('username')), 'user_profile');
                } else {
                    $res = true;
                }
                if ($res) {
                    $row = array(
                        'language' => set_value('lang')
                    );
                    $new_pwd = set_value('new_password');
                    if (!empty($new_pwd)) {
                        $row['password'] = crypt($new_pwd);
                    }
                    $res = $this->users->updateRow($this->user_id, $row);
                    if ($res) {
                        $this->session->userdata['language'] = set_value('lang');
                        $cookie = array(
                            'name' => 'site_lang',
                            'value' => set_value('lang'),
                            'expire' => 365 * 24 * 60 * 60,
                            'prefix' => 'en2_',
                        );

                        set_cookie($cookie);
                    }
                }
                if (!$res) {
                    $output['errors'] = '<li>' . lang('error_update_settings') . '</li>';
                }
            } else {
                $output['errors'] = validation_errors('<li>', '</li>');
            }
        }

        $this->echo_json($output);
    }

    public function check_old_password($pwd) {
        $user_id = $this->user_id;
        if ($this->authm->check_user_password($user_id, $pwd) === TRUE) {
            return TRUE;
        }
        $this->form_validation->set_message('check_old_password', $this->lang->line('auth_old_password_wrong', 'auth'));
        return FALSE;
    }

}
