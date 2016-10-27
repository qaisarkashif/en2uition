<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comment extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('comment_model', 'comments');
    }

    public function get() {
        $table = $this->input->post('table', true);
        $target_id = (int)$this->input->post('target-id', true);
        $target_id = $target_id > 0 ? $target_id : null;
        $comments = array();
        
        if(!$table) {//if it's for updates
            $last_ids = $this->input->post('last_ids', true);
            
            //profile comments
            $last_id = isset($last_ids['profile_comment']) ? $last_ids['profile_comment'] : -1;
            $profileCommentsArray = $this->comments->getComments('profile_comment', $last_id, $target_id, $this->user_id, true);
            
            //photo comments
            $last_id = isset($last_ids['photo_comment']) ? $last_ids['photo_comment'] : -1;
            $photoCommentsArray = $this->comments->getComments('photo_comment', $last_id, $target_id, $this->user_id, true);
                        
            //topic comments
            $this->load->model('forum_model', 'forums');
            $my_group = $this->authm->get_my_group();
            $last_id = isset($last_ids['topic_comment']) ? $last_ids['topic_comment'] : -1;
            $topicShCommentsArray = $this->forums->getSharedTopicComments($this->user_id, $my_group, $last_id);
            
            //shared photos
            $this->load->model('photo_model', 'fotos');
            $last_id = isset($last_ids['photo']) ? $last_ids['photo'] : -1;
            $sharedPhotosArray = $this->fotos->getSharedPhotos($this->user_id, $last_id);
            
            $index = UPDATES_LIMIT;
            $c1 = $c2 = $c3 = $c4 = true;
            while($index > 0) {
                $c1 = current($profileCommentsArray);
                $c2 = current($photoCommentsArray);
                $c3 = current($topicShCommentsArray);
                $c4 = current($sharedPhotosArray);
                if($c1 === FALSE && $c2 === FALSE && $c3 === FALSE && $c4 === FALSE) {
                    break;
                }
                $tmp = array(
                    'c1' => $c1['unix_date'],
                    'c2' => $c2['unix_date'],
                    'c3' => $c3['unix_date'],
                    'c4' => $c4['unix_date']
                );
                $max = max(array_values($tmp));
                $var = array_search($max, $tmp);
                $comments[$index] = ${$var};
                
                if(${$var}['target'] == 'topic_comment' || ${$var}['target'] == 'photo') {
                    $ind = ${$var}['shared_id'];
                } else {
                    $ind = ${$var}['id'];
                }
                
                if($var == 'c1') {
                    unset($profileCommentsArray[$ind]);
                } elseif($var == 'c2') {
                    unset($photoCommentsArray[$ind]);
                } elseif($var == 'c3') {
                    unset($topicShCommentsArray[$ind]);
                } elseif($var == 'c4') {
                    unset($sharedPhotosArray[$ind]);
                }
                
                $index--;
            }
            unset($profileCommentsArray, $photoCommentsArray, $topicShCommentsArray, $sharedPhotosArray);
        } else {
            $last_id = (int) $this->input->post('last_id', true);
            $comments = $this->comments->getComments($table, $last_id, $target_id, $this->user_id);
        }

        $output = array(
            'comments' => $comments,
            'stop_request' => count($comments) < (!$table ? UPDATES_LIMIT : COMMENTS_LIMIT),
            'autor_uid' => $this->user_id,
            'autor_name' => $this->authm->get_username(),
            'autor_ava' => $this->authm->get_user_photo('forum')
        );
        
        $this->echo_json($output);
    }

    public function add($target = '') {
        $output = array('errors' => "");

        $target_id = (int)$this->input->post('target_id', true);
        $reply_to = (int)$this->input->post('reply_to', true);
        $comment = $this->input->post('comment', true);

        if($target && $target_id && $reply_to && $comment) {
            $created = date("Y-m-d H:i:s");
            $row = array(
                'user_id' => $this->user_id,
                'target_id' => $target_id,
                'reply_to' => $reply_to > 0 ? $reply_to : NULL,
                'comment' => $comment,
                'created' => $created
            );
            $res = $this->comments->insertRow($row, $target);
            if(!$res) {
                $output['errors'] = lang('error_comment_add');
            } else {
                $new_comment_id = $this->db->insert_id();
                // convert to user's localtime
                $clienttime = $this->comments->server_to_client_localtime($created);
                $created = date('\o\n l, F j, Y \a\t g.iA', $clienttime);
                $created = str_replace(array("AM", "PM"), array("a.m.", "p.m."), $created);
                $output['comment_info'] = array(
                    'id' => $new_comment_id,
                    'username' => $this->authm->get_username(),
                    'ava' => $this->authm->get_user_photo('forum'),
                    'date' => $created,
                    'uid' => $this->user_id
                );

                if(in_array($target, array('profile_comment', 'photo_comment'))) {
                    $data = array();
                    $row = array(
                        'from_user' => $this->user_id,
                        'to_user' => null,
                        'type' => $target,
                        'optional' => $new_comment_id
                    );
                    $prof_id = $this->authm->get_profile_id();
                    switch($target) {
                        case 'profile_comment' :
                            if($prof_id != $target_id) {
                                $row['to_user'] = $target_id;
                                $data[] = $row;
                            }
                            break;
                        case 'photo_comment' :
                            $this->load->model('photo_model', 'photos');
                            $photoOwner = $this->photos->getPhotoOwner($target_id);
                            if($photoOwner != $this->user_id) {
                                $row['to_user'] = $photoOwner;
                                $data[] = $row;
                            }
                            break;
                    }
                    $email_data = array('from_user' => $this->authm->get_username());
                    if($reply_to > 0) {
                        $cmt = $this->comments->getCommentById($target, $reply_to);
                        if($cmt && $cmt->user_id != $prof_id) {
                            $row['to_user'] = $cmt->user_id;
                            $data[] = $row;
                            $this->send_email_notification('reply-to-comment', $cmt->user_id, $email_data);
                        }
                    }
                    if(count($data) > 0) {
                        if($reply_to <= 0) {
                            $template = str_replace('_', '-', $target);
                            $this->send_email_notification($template, $row['to_user'], $email_data);
                        }
                        $this->load->model('notification_model', 'notifications');
                        for ($i = 0; $i < count($data); ++$i) {
                            // store this in GMT
                            $data[$i]['created'] = date("Y-m-d H:i:s");
                        }

                        $this->notifications->insertRow(count($data) > 1 ? array_values($data) : $data[0], '', count($data) > 1);
                    }
                }
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

    public function share($target = '') {
        $output = array('errors' => "");
        $id = (int) $this->input->post('id', true);
        $type = $this->input->post('type', true);
        if ($target && $id && $type) {
            $data = array(
                ($target == 'photo' ? 'photo' : 'comment'). '_id' => $id,
                'user_id' => $this->user_id
            );
            $table = $target . '_share';
            if ($type == 'share') {
                $already_shared = $this->comments->isAlreadyShared($data, $table);
                if($already_shared) {
                    $res = true;
                } else {
                    $shared = date("Y-m-d H:i:s");
                    $data['shared'] = $shared;
                    $res = $this->comments->insertRow($data, $table);
                }
            } else {
                $res = $this->comments->unshareComment($data, $table);
            }
            if (!$res) {
                $output['errors'] = lang("error_{$type}_comment");
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

    public function delete($target = '') {
        $output = array('errors' => '');
        $target_id = (int) $this->input->post('target_id', true);
        if ($target && $target_id) {
            $res = $this->comments->deleteComment($this->user_id, $target, $target_id);
            if(!$res) {
                $output['errors'] = lang('error_comment_delete');
            }
        }
        $this->echo_json($output);
    }

}

/* End of file comment.php */
/* Location: ./application/controllers/comment.php */