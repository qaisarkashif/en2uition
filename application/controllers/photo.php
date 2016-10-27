<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Photo extends MY_Controller {

    private $album_path;
    private $album_tmp_path;

    public function __construct() {
        parent::__construct();

        if(!IS_AJAX) {
            $this->tmpl->add_css('photo.css');
        }


        $this->data = array(
            'top_menu' => '',
            'header_data' => array(
                'inner_navbar' => true,
                'active_page' => 'photos',
                'user' => $this->authm->get_profile()
            )
        );

        $this->load->model('photo_model', 'photos');

        $this->album_path = FCPATH . 'uploads/profiles/pro-' . $this->user_id . '/albums';
        $this->album_tmp_path = $this->album_path . '/tmp';
    }

    public function index() {
        $this->data['main_content'] = $this->load->view('photo/index', '', TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function album($album_id = '') {
        $this->data['top_menu'] = 'album';

        $album_id = $album_id == "new" ? -1 : intval($album_id);

        if ($album_id === -1 || $album_id > 0) {
            $this->load->helper('files');
            recursiveRemoveDirectory($this->album_tmp_path);

            $this->tmpl->add_css("jquery.fileupload.css");
            $this->tmpl->add_scripts(array(
                "jquery.ui.widget.js",
                "load-image.all.min.js",
                "canvas-to-blob.min.js",
                "jquery.iframe-transport.js",
                "jquery.fileupload.js",
                "jquery.fileupload-process.js",
                "jquery.fileupload-image.js",
                "jquery.fileupload-validate.js",
                "photo-album/upload-photo.js"
            ));
            $album_title = '';
            if($album_id > 0) {
                $album = $this->photos->getAlbumByID($album_id);
                if($album !== FALSE) {
                    $album_title = $album->title;
                }
            }
            $data = array(
                'album_id' => $album_id,
                'album_title' => $album_title
            );
            $this->data['main_content'] = $this->load->view('photo/new_album', $data, TRUE);
        } else {
            $this->tmpl->add_scripts(array(
                "jquery.mousewheel.min.js",
                "photo-album/all-albums.js"
            ));
            $album_data = array(
                'albums' => $this->photos->getAlbumsWithPhotos($this->user_id)
            );
            $this->data['main_content'] = $this->load->view('photo/album', $album_data, TRUE);
        }
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function save_album() {
        $output = array('errors' => "");

        if ($this->input->post()) {

            $this->load->library('form_validation');

            $rules = array(
                array('field' => 'album-title', 'label' => "'Title'", 'rules' => 'trim|required|xss_clean'),
                array('field' => 'album-id', 'label' => "'Album ID'", 'rules' => 'trim|required|xss_clean')
            );
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() === TRUE) {
                $row = array(
                    'owner' => $this->user_id,
                    'title' => set_value('album-title')
                );
                if(set_value('album-id') == -1) {
                    $res = $this->photos->insertRow($row, 'photo_album');
                    $album_id = $this->db->insert_id();
                } else {
                    $album_id = set_value('album-id');
                    $res = $this->photos->updateRow($album_id, $row, 'photo_album');
                }
                if($res) {
                    $album_source = $this->album_tmp_path;
                    if (is_dir($album_source)) {
                        $album_dest = $this->album_path . "/alb-{$album_id}";
                        $res = $this->photos->saveAlbumPhoto($album_id, $album_source);
                        if($res) {
                            if(!is_dir($album_dest)) {
                                rename($album_source, $album_dest);
                            } else {
                                $this->load->helper('files');
                                merge_dir($album_source, $album_dest);
                            }
                        } else {
                            $output['errors'] = 'general';
                        }
                    }
                } else {
                    $output['errors'] = 'general';
                }
            } else {
                $output['errors'] = form_error('album-title', '<p class="verrors">', '</p>');
            }
        } else {
            $output['errors'] = '<p class="verrors">' . lang('error_data_not_transferred') . '</p>';
        }

        $this->echo_json($output);
    }

    public function upload_to_album() {
        if (IS_AJAX) {
            $upload_to = 'uploads/profiles/pro-' . $this->user_id . '/albums/tmp';
            $upload_dir = FCPATH . $upload_to . DS;
            $options = array(
                'upload_dir' => $upload_dir,
                'upload_url' => base_url($upload_to) . '/',
                'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
                'image_versions' => array(
                    'medium' => array('max_width' => 673, 'max_height' => 423),
                    'thumbnail' => array('max_width' => 230, 'max_height' => 230)
                ),
            );
            $this->load->library("UploadHandler", $options);
        } else {
            show_error('Access denied!');
        }
    }

    public function update_album_title() {
        $output = array('errors' => "");

        $id = (int) $this->input->post('id', true);
        $title = $this->input->post('title', true);

        if ($id && $title) {
            $row = array('title' => $title);
            $res = $this->photos->updateRow($id, $row, 'photo_album');
            if(!$res) {
                $output['errors'] = lang('error_photo_title_update');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

    public function delete_album($id) {
        if((int)$id > 0) {
            $deleted = $this->photos->deleteAlbum((int) $id);
            if($deleted !== FALSE) {
                $this->load->helper('files');
                recursiveRemoveDirectory($this->album_path . "/alb-{$id}");
                $flash_msg = array('status' => 'success', 'text' => 'The album successfully removed.');
            } else {
                $flash_msg = array('status' => 'error', 'text' => 'Error occurred while deleting album.');
            }
            $this->session->set_flashdata('msg', $flash_msg);
        }

        redirect('/photo/page');
    }

    public function preset_page($album_id, $photo_id, $owner_id = null) {
        $this->session->set_flashdata('album_id', $album_id);
        $this->session->set_flashdata('photo_id', $photo_id);
        if(!empty($owner_id) && intval($owner_id) != $this->user_id) {
            redirect("/visitor/photo/uid-{$owner_id}");
        } else {
            redirect('/photo/page');
        }
    }

    public function page($vid = null) {
        if($this->check_account_state($vid ? $vid : $this->user_id) === FALSE) {
            return;
        }

        foreach(array('album_id', 'photo_id') as $var) {
            if($this->session->flashdata($var)) {
                ${$var} = (int) $this->session->flashdata($var);
                $this->session->keep_flashdata($var);
            } else {
                ${$var} = '';
            }
        }

        $this->tmpl->add_css("magnific-popup.css");
        $this->tmpl->add_scripts(array(
            "jquery.magnific-popup.min.js",
            "jquery.mousewheel.min.js",
            "photo-album/jquery.waterwheelCarousel.js",
            "comments/share-comments.js",
            "comments/comment.js",
            "votes/vote.js"
        ));

        if(!empty($vid)) {
            $this->load->model('friend_model', 'friends');
            $is_my_friend = $this->friends->isMyFriend($this->user_id, $vid);
            if(!$is_my_friend) {
                show_404();
                exit;
            }
            $this->tmpl->add_scripts("photo-album/visitor_page.js");
        } else {
            $this->tmpl->add_scripts("photo-album/page.js");
        }
        $this->data['top_menu'] = 'photo_page';
        $this->data['main_content'] = $this->get_page($album_id, $photo_id, $vid);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function update_photo($target = '') {
        $output = array('errors' => "");
        $res = null;

        if(in_array($target, array('privacy', 'title'))) {

            $id = (int) $this->input->post('id', true);

            if($target == 'privacy') {
                $privacy_code = $this->input->post('privacy_code', true);
                if(empty($privacy_code) || in_array($privacy_code, $this->base->privacyCodesArr)) {
                    $res = $this->photos->updatePrivacyCode($id, !empty($privacy_code) ? $privacy_code : NULL);
                } else {
                    $output['errors'] = lang('error_incorrect_privacy_code');
                }
            } elseif($target == 'title') {
                $title = $this->input->post('title', true);
                $row = array('title' => $title);
                $res = $this->photos->updateRow($id, $row);
            }

            if($res === FALSE) {
                $output['errors'] = lang('error_photo_update');
            }
        }

        $this->echo_json($output);
    }

    public function share_photo() {
        $output = array('errors' => '');
        $photo_id = (int) $this->input->post('photo_id', true);
        $action = $this->input->post('action', true);
        if($photo_id > 0 && in_array($action, array('share', 'unshare'))) {
            switch($action) {
                case 'share'    :
                    $photo = $this->photos->getPhotoById($photo_id);
                    if($photo) {
                        if(empty($photo->privacy_code)) {
                            $res = $this->photos->sharePhoto($this->user_id, $photo_id);
                            if($res) {
                                $output['btn_text'] = lang('btn_unshare_photo');
                            } else {
                                $output['errors'] = lang('error_share_foto');
                            }
                        } else {
                            $output['errors'] = lang('error_photo_share_denied');
                        }
                    } else {
                        $output['errors'] = lang('error_photo_not_found');
                    }
                    break;
                case 'unshare'  :
                    $res = $this->photos->unsharePhoto($this->user_id, $photo_id);
                    if($res) {
                        $output['btn_text'] = lang('btn_share_photo');
                    } else {
                        $output['errors'] = lang('error_unshare_foto');
                    }
                    break;
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        echo $this->echo_json($output);
    }

    public function delete_photo() {
        $output = array('errors' => "");

        $id = (int) $this->input->post('id', true);
        $photo = $this->photos->getPhotoByID($id);
        if(!is_null($photo)) {
            $res = $this->photos->deletePhoto($photo->id);
            if($res !== FALSE) {
                @unlink(FCPATH . $photo->orig);
                @unlink(FCPATH . $photo->medium);
                @unlink(FCPATH . $photo->thumb);
            } else {
                $output['errors'] = lang('error_photo_delete');
            }
        } else {
            $output['errors'] = lang('error_photo_not_found');
        }

        $this->echo_json($output);
    }

    public function edit_privacy() {
        if(IS_AJAX && $this->input->post()) {
            $output = array('errors' => "");

            $friend_id = (int) $this->input->post('friend_id', true);
            $privacy = $this->input->post('privacy', true);
            if($friend_id) {
                $res = $this->photos->setFriendPrivacy($this->user_id, $friend_id, $privacy);
                if(!$res) {
                    $output["errors"] = "Error updating privacy.";
                }
            } else {
                $output["errors"] = lang('error_empty_input_params');
            }

            $this->echo_json($output);
        } else {
            $this->load->model('friend_model', 'friends');
            $this->data['top_menu'] = 'edit';
            $granted_permissions = $this->users->get_privacy_permissions(null, array('type' => '"photo"', 'from_user' => $this->user_id));
            $data = array(
                'friends_list' => $this->friends->getUserFriends($this->user_id, array('forum')),
                'granted_permissions' => isset($granted_permissions['photo']) ? $granted_permissions['photo'] : array()
            );
            $this->tmpl->add_scripts("/photo-album/edit_privacy.js");
            $this->data['main_content'] = $this->load->view('photo/edit_privacy', $data, TRUE);
            $this->tmpl->show_template('include/layout', $this->data);
        }
    }

    public function get_album_photos() {
        if(IS_AJAX) {
            $photos = array();
            $album_id = (int) $this->input->post('album_id', true);
            $visitor_id = (int) $this->input->post('visitor_id', true);
            if($album_id) {
                if($visitor_id > 0) {
                    $privacy = $this->get_view_privacy($visitor_id);
                } else {
                    $privacy = array();
                }
                if(($visitor_id > 0 && count($privacy) > 0) || $visitor_id == 0) {
                    $photos = $this->photos->getAlbumPhotos($album_id, $this->user_id, $privacy);
                }
            }
            echo json_encode(array('count' => count($photos), 'photos' => $photos));
        } else {
            show_error('Access denied!');
        }
    }

    public function get_data() {
        $output = array('title' => '');

        $id = (int) $this->input->post('id', true);
        if($id) {
            $photo = $this->photos->getPhotoById($id, $this->user_id);
            if(!is_null($photo)) {
                $this->load->model('vote_model', 'votes');
                $this->votes->init('photo');
                $output = array(
                    'id' => $photo->id,
                    'title' => $photo->title,
                    'vote_totals' => $this->votes->getTotals($photo->id),
                    'who_voted' => $this->votes->whoVoted($photo->id),
                    'my_vote' => $photo->my_vote,
                    'shared' => $photo->shared,
                    'shared_text' => !!$photo->shared ? lang('btn_unshare_photo') : lang('btn_share_photo')
                );
                //remove notifications with type = photo_vote
                $this->load->model('notification_model', 'notifications');
                $this->notifications->deleteNotifications($this->user_id, $photo->id, array('photo_vote'));
            }
        }

        $this->echo_json($output);
    }

    private function get_page($album_id = '', $photo_id = '', $vid = null) {
        $album_data = array(
            'albums' => $this->photos->getAlbumsList(!empty($vid) ? $vid : $this->user_id)
        );
        if($vid) {
            $album_data['visitor_id'] = $vid;
        }
        $album_data['albums_count'] = count($album_data['albums']);
        if(!empty($album_id)) {
            $album_data['curaid'] = (int) $album_id;
        } elseif($album_data['albums_count'] > 0) {
            $c = current($album_data['albums']);
            $album_data['curaid'] = $c['id'];//current album id
        }
        $album_data["curpid"] = !empty($photo_id) ? (int) $photo_id : 0;//current photo id
        if(isset($album_data['curaid'])) {
            if (!empty($vid)) {
                $privacy = $this->get_view_privacy($vid);
            } else {
                $privacy = array();
            }
            if((!empty($vid) && count($privacy) > 0) || empty($vid)) {
                $album_data['photos'] = $this->photos->getAlbumPhotos($album_data['curaid'], $this->user_id, $privacy);
            }
        }
        return $this->load->view('photo/' . (!empty($vid) ? 'visitor_' : '') . 'page', $album_data, TRUE);
    }

    private function get_view_privacy($visitor_id) {
        $where = "type = 'photo' AND from_user = {$visitor_id}";
        $permissions = $this->users->get_privacy_permissions($this->user_id, $where);
        if (isset($permissions['photo'][$this->user_id]) && count($permissions['photo'][$this->user_id]) > 0) {
            $user_perm = current($permissions['photo'][$this->user_id]);
            $tmp = array_filter(explode('|', $user_perm['privacy']));
            $tmp[] = "";
            return $tmp;
        }
        return array("");
    }

}

/* End of file photo.php */
/* Location: ./application/controllers/photo.php */