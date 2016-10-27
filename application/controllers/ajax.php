<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!IS_AJAX) {
            exit;
        }
    }

    public function get_notifications() {
        $this->load->model('notification_model', 'notify');
        $notifyArr = array(
            'profile' => array('items' => array(), 'count' => 0),
            'photo' => array('items' => array(), 'count' => 0)
        );
        $enum = array(
            'photo_comment' => 'photo',
            'profile_comment' => 'profile',
            'photo_vote' => 'photo',
            'photo_comment_vote' => 'photo',
            'profile_comment_vote' => 'profile',
            'topic_comment' => 'profile',
            'topic_comment_vote' => 'profile'
        );
        foreach ($enum as $type => $arr_key) {
            $this->notify->getNotifications($this->user_id, $notifyArr[$arr_key], $type);
        }
        $this->echo_json(array('notifications' => $notifyArr));
    }

    public function crop_profile_image() {
        $output = array('errors' => '');
        foreach (array('x1', 'x2', 'y1', 'y2', 'w', 'h') as $var) {
            ${$var} = $this->input->post($var, true);
        }
        $img_src = $this->authm->get_user_photo('orig', null, false);
        $img_dest = $this->authm->get_user_photo('profile', null, false);
        $config = array(
            'image_library' => 'gd2',
            'source_image' => realpath(substr($img_src, 1)),
            'new_image' => realpath(substr($img_dest, 1)),
            'x_axis' => $x1,
            'y_axis' => $y1,
            'maintain_ratio' => FALSE,
            'width' => $w,
            'height' => $h
        );
        $this->load->library('image_lib');
        $this->image_lib->initialize($config);
        if (!$this->image_lib->crop()) {
            $output['errors'] = $this->image_lib->display_errors();
        } else {
            $config = array(
                'image_library' => 'gd2',
                'source_image' => realpath(substr($img_dest, 1)),
                'create_thumb' => FALSE,
                'maintain_ratio' => TRUE,
                'width' => 309,
                'height' => 196,
                'master_dim' => 'height'
            );
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
        }
        $this->image_lib->clear();
        $this->echo_json($output);
    }

}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */