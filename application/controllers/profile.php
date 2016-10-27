<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        if(!IS_AJAX) {
            $this->tmpl->add_css(array(
                "magnific-popup.css",
                "profile.css"
            ));
            $this->tmpl->add_scripts(array(
                "jquery.knob.js",
                "jquery.magnific-popup.min.js",
                "jquery.mousewheel.min.js",
                "comments/share-comments.js",
                "comments/comment.js",
                "votes/vote.js",
                "profile/profile.js"
            ));
        }
        $this->data = array(
            'top_menu'      => 'profile',
            'header_data'   => array(
                'inner_navbar'  => true,
                'active_page'   => 'profile'
            )
        );
    }

    public function index() {
        if($this->check_account_state($this->user_id) === FALSE) {
            return;
        }

        $this->lang->load('question');
        $this->load->model('friend_model', 'friends');
        $this->load->model('questionnaire_model', 'quiz');
        $this->load->model('request_model', 'requests');
        $this->load->model('photo_model', 'photos');

        $dailymood = $this->authm->get_dailymood();
        $where = array(
            'to_user' => $this->user_id,
            'type' => 'question_privacy'
        );
        $requests = array('past' => array(), 'present' => array());
        $question_requests = $this->requests->getRequests($where);
        foreach($question_requests as $r) {
            $opt = json_decode($r['optional'], true);
            if(!isset($requests[$opt['qtype']][$opt['lnum']])) {
                $requests[$opt['qtype']][$opt['lnum']] = 0;
            }
            $requests[$opt['qtype']][$opt['lnum']] += 1;
        }
        $data['questionnaire_progress'] = $this->quiz->getQuestionnaireProgress($this->user_id);
        $data['questionnaire_requests'] = $requests;
        $data['user'] = $this->authm->get_profile();
        $data['avatar_preview'] = $this->authm->get_user_photo('profile', $data['user']);
        $data['avatar_original'] = $this->authm->get_user_photo('orig', $data['user']);
        $data['user']['edu_title'] = $data['user']['education'] ? $this->base->educationArr[$data['user']['education']] : "";
        $data['user']['gender_title'] = $data['user']['gender'] ? $this->base->genderArr[$data['user']['gender']] : "";
        $data['user']['sex_ori_title'] = $data['user']['orientation'] ? $this->base->orientationArr[$data['user']['orientation']] : "";
        $data['friends']['list'] = $this->friends->getUserFriends($this->user_id, array('profile'));
        $data['shared_photos'] = $this->photos->getUserPublicPhotos($this->user_id);
        $data = array_merge($data, $dailymood);
        $this->data['main_content'] = $this->load->view('profile/profile', $data, TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function visitor($id) {
        if($id == $this->user_id) {
            redirect('/profile');
        }
        if($this->check_account_state($id) === FALSE) {
            return;
        }
        $this->data['top_menu'] = 'visitor';
        $this->data['header_data']['active_page'] = 'visitor';
        $this->data['header_data']['visitor_id'] = $id;

        $user = $this->users->get_user_profile($id, 2);
        $data = array();

        if($user) {
            $this->lang->load('question');
            $this->load->model('friend_model', 'friends');
            $this->load->model('questionnaire_model', 'quiz');
            $this->load->model('photo_model', 'photos');

            $data['questionnaire_progress'] = $this->quiz->getQuestionnaireProgress($id);
            $data['user'] = current($user);
            $data['user']['edu_title'] = $data['user']['education'] ? $this->base->educationArr[$data['user']['education']] : "";
            $data['user']['gender_title'] = $data['user']['gender'] ? $this->base->genderArr[$data['user']['gender']] : "";
            $data['user']['sex_ori_title'] = $data['user']['orientation'] ? $this->base->orientationArr[$data['user']['orientation']] : "";
            $data['friends']['list'] = $this->friends->getUserFriends($id, array('profile'));
            $data['shared_photos'] = $this->photos->getUserPublicPhotos($id);
            $this->data['header_data']['my_friend'] = $data['my_friend'] = in_array($this->user_id, array_keys($data['friends']['list']));
        } else {
            show_404();
            exit;
        }

        $this->load->model('request_model', 'requests');
        $req_data = array(
            'from_user' => $this->user_id,
            'to_user' => $id,
            'type' => 'friendship'
        );
        $frshp_request = $this->requests->getFriendshipRequest($req_data);
        if($frshp_request) {
            $this->data['header_data']['frshp_request'] = current($frshp_request);
        }
        $this->data['header_data']['is_joined'] = $this->authm->is_joined();
        $data['avatar_preview'] = $this->authm->get_user_photo('profile', $data['user']);
        $data['avatar_original'] = $this->authm->get_user_photo('orig', $data['user']);
        $this->data['main_content'] = $this->load->view('profile/visitor', $data, TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function edit() {
        $this->load->view('profile/edit-profile');
    }

    public function update() {
        $output = array('errors' => "");

        if($this->input->post()) {
            $username = $this->input->post('username');
            $unique = '|is_unique[user_profile.username]';
            if($username !== FALSE && trim($username)) {
                $cur_username = $this->authm->get_username();
                if(trim($cur_username) == trim($username)) {
                    $unique = "";
                }
            }
            $rules = array(
                array('field' => 'username', 'label' => "'Username'", 'rules' => 'trim|required' . $unique . '|max_length[30]|xss_clean'),
                array('field' => 'living_country', 'label' => "'Living in'", 'rules' => 'trim|xss_clean'),
                array('field' => 'living_state', 'label' => "'Living in'", 'rules' => 'trim|xss_clean'),
                array('field' => 'living_city', 'label' => "'Living in'", 'rules' => 'trim|xss_clean'),
                array('field' => 'age', 'label' => "'Birthday'", 'rules' => 'trim|xss_clean'),
                array('field' => 'education', 'label' => "'Education'", 'rules' => 'trim|xss_clean'),
                array('field' => 'gender', 'label' => "'Gender'", 'rules' => 'trim|xss_clean'),
                array('field' => 'sexual_ori', 'label' => "'Sexual orientation'", 'rules' => 'trim|xss_clean'),
                array('field' => 'rel_status', 'label' => "'Relationship Status'", 'rules' => 'trim|xss_clean'),
                array('field' => 'profile_id', 'label' => "'Profile ID'", 'rules' => 'trim|required|integer|xss_clean')
            );

            $this->form_validation->set_rules($rules);
            $this->form_validation->set_message('is_unique', $this->lang->line('auth_unique'));
            if($this->form_validation->run() === TRUE) {
                $row = array(
                    'username' => set_value('username'),
                    'country' => trim(set_value('living_country')) ? set_value('living_country') : NULL,
                    'state' => trim(set_value('living_state')) ? set_value('living_state') : NULL,
                    'city' => trim(set_value('living_city')) ? set_value('living_city') : NULL,
                    'birthday' => trim(set_value('age')) ? date("Y-m-d", strtotime(set_value('age'))) : NULL,
                    'education' => set_value('education'),
                    'gender' => set_value('gender'),
                    'orientation' => set_value('sexual_ori'),
                    'relationship' => set_value('rel_status'),
                    'color' => $this->base->get_color(set_value('rel_status'))
                );

                $res = $this->users->updateRow(set_value('profile_id'), $row, 'user_profile');
                if(!$res) {
                    $output['errors'] = '<p>' . lang('error_update_profile_info') . '</p>';
                } else {
                    //update profile info in session
                    foreach($row as $key => $val) {
                        $this->session->userdata['user_info'][$key] = $val;
                    }
                    $output['color'] = $row['color'];
                }
            } else {
                $output['errors'] = validation_errors('<p>', '</p>');
            }
        } else {
            $output['errors'] = '<p>' . lang('error_data_not_transferred') . '</p>';
        }

        $this->echo_json($output);
    }

    public function settings() {
        $data = array(
            'black_list' => $this->users->getMyBlackList($this->user_id, true)
        );
        $this->load->view('profile/setting', $data);
    }

    public function update_settings() {
        $output = array('errors' => array());

        if($this->input->post()) {
            $old_pwd_rules = "trim|xss_clean";
            $conf_pwd_rules = "trim|min_length[5]|matches[new_password]|xss_clean";
            if($this->input->post('new_password', true)) {
                $old_pwd_rules .= "|required|callback_check_old_password";
                $conf_pwd_rules .= "|required";
            }
            $rules = array(
                array('field' => 'old_password', 'label' => "'Old password'", 'rules' => $old_pwd_rules),
                array('field' => 'new_password', 'label' => "'New password'", 'rules' => 'trim|min_length[5]|xss_clean'),
                array('field' => 'new_password1', 'label' => "'Confirm new password'", 'rules' => $conf_pwd_rules),
                array('field' => 'lang', 'label' => "'Site language'", 'rules' => 'trim|required|xss_clean'),
                array('field' => 'profile-status', 'label' => "'Account Status'", 'rules' => 'trim|required|xss_clean'),
                array('field' => 'site-vers', 'label' => "'Site version'", 'rules' => 'trim|required|xss_clean'),
                array('field' => 'users_guide_hidden', 'label' => "User Guide", 'rules' => 'trim|required|xss_clean'),
                array('field' => 'email_notifications', 'label' => "Email Notifications", 'rules' => 'trim|xss_clean')
            );
            $this->form_validation->set_rules($rules);
            if($this->form_validation->run() === TRUE) {
                $row = array(
                    'language' => set_value('lang'),
                    'active' => set_value('profile-status'),
                    'joined' => set_value('site-vers'),
                    'users_guide_hidden' => set_value('users_guide_hidden'),
                    'email_notifications' => set_value('email_notifications')
                );
                $new_pwd = set_value('new_password');
                if(!empty($new_pwd)) {
                    $row['password'] = crypt($new_pwd);
                }
                $user_id = $this->user_id;
                $res = $this->users->updateRow($user_id, $row);
                if($res) {
                    $this->session->userdata['language'] = set_value('lang');
                    $cookie = array(
                        'name' => 'site_lang',
                        'value' => set_value('lang'),
                        'expire' => 365 * 24 * 60 * 60,
                        'prefix' => 'en2_',
                    );

                    set_cookie($cookie);

                    if($row['active'] == '0') {
                        $output['account_inactive'] = 'yes';
                        $output['inactive_msg'] = lang('inactive_msg');
                    }
                } else {
                    $output['errors'] = '<li>' . lang('error_update_settings') . '</li>';
                }
            } else {
                $output['errors'] = validation_errors('<li>', '</li>');
            }
        }

        $this->echo_json($output);
    }

    public function upload_profile_picture() {
        $output = array("errors" => array());

        if(isset($_FILES["profile-picture"])) {
            $this->load->helper('files');
            $upload_path = './uploads/profiles/pro-' . $this->user_id . '/avatars';
            $folder_exist = false;
            if (!is_dir($upload_path)) {
                @mkdir($upload_path, 0777, true);
                chmod($upload_path, 0777);
                $folder_exist = is_dir($upload_path);
            } else {
                $folder_exist = true;
            }
            if ($folder_exist) {
                make_old_files($upload_path);
                $config = array(
                    'upload_path'   => $upload_path,
                    'allowed_types' => 'gif|jpg|jpeg|png',
                    'overwrite'     => TRUE,
                    'file_name'     => 'original'
                );

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload("profile-picture")) {
                    $output['errors'][] = $this->upload->display_errors();
                    make_old_files($upload_path, true);
                } else {
                    $filedata = $this->upload->data();

                    $thumbs = array(
                        array('thumb_name' => 'homepage_thumb', 'width' => 191, 'height' => 147),
                        array('thumb_name' => 'profile_thumb', 'width' => 309, 'height' => 196),
                        array('thumb_name' => 'forum_thumb', 'width' => 60, 'height' => 60),
                        array('thumb_name' => 'friend_thumb', 'width' => 83, 'height' => 83)
                    );
                    create_thumbnails($filedata, $thumbs);

                    $profile_id = $this->authm->get_profile_id();
                    $this->users->updateRow($profile_id, array('profile_image' => $filedata['file_name']), 'user_profile');

                    delete_all_files($upload_path, '*.old');

                    $this->session->userdata['user_info']['profile_image'] = $filedata['file_name'];
                    $p = base_url('uploads/profiles') . '/pro-' . $this->user_id . '/avatars/';
                    $output['filepath_original'] = $p . $filedata['file_name'];
                    $output['filepath_preview'] = $p . 'profile_thumb' . $filedata['file_ext'];
                }
            } else {
                $output['errors'][] = "<p>" . lang('error_create_folder_fd') . "</p>";
            }
        }

        $this->echo_json($output);
    }

    public function update_blacklist($action = 'add') {
        $output = array('errors' => '');
        $uid = $this->input->get('uid', true);
        if($uid) {
            $res = $this->users->updateBlackList($action, $this->user_id, $uid);
            if(!$res) {
                $output['errors'] = $action == 'add' ? lang('error_add_to_blacklist') : lang('error_remove_from_blacklist');
            }
        } else {
            $output['errors'] = lang('error_empty_input_param');
        }

        $this->echo_json($output);
    }

    public function unfriend() {
        $output = array('errors' => '');
        $friend_id = $this->input->get('friend_id', true);
        if($friend_id) {
            $this->load->model('friend_model', 'friends');
            $res = $this->friends->unfriend($this->user_id, $friend_id);
            if(!$res) {
                $output['errors'] = lang('error_unfriend');
            }
        } else {
            $output['errors'] = lang('error_empty_input_param');
        }
        $this->echo_json($output);
    }

    public function remove_avatar() {
        $output = array('errors' => '');
        $res = $this->users->removeAvatar($this->user_id);
        if($res) {
            $output['no_ava'] = set_image(DEF_USER_AVA_HOMEPAGE);
            $output['no_ava_orig'] = set_image(DEF_USER_AVA_ORIG);
        } else {
            $output['errors'] = lang('error_photo_delete');
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

/* End of file profile.php */
/* Location: ./application/controllers/profile.php */