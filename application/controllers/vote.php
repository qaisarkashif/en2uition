<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vote extends MY_Controller {
    
    private $notifyTables = array(
        'photo', 
        'photo_comment',
        'profile_comment',
        'topic_comment'
    );
    private $uri_seg = null;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('vote_model', 'votes');
        if ($this->uri->segment(3)) {
            $this->votes->init($this->uri->segment(3));
            $this->uri_seg = $this->uri->segment(3);
        }
    }
    
    public function add_vote() {
        $output = array('errors' => '');
        
        $id = (int) $this->input->get('id', true);
        $type = $this->input->get('type', true);
        
        if($id && $type) {
            $data = array(
                'id' => $id,
                'type' => $type,
                'user_id' => $this->user_id
            );
            $res = $this->votes->add($data);
            if($res) {
                $output['vote_totals'] = $this->votes->getTotals($id);
                $output['who_voted'] = $this->votes->whoVoted($id);
                
                if(in_array($this->uri_seg, $this->notifyTables)) {
                    $to_user = null;
                    if($this->uri_seg == 'photo') {
                        $this->load->model('photo_model', 'photos');
                        $photoOwner = $this->photos->getPhotoOwner($id);
                        if($photoOwner != $this->user_id) {
                            $to_user = $photoOwner;
                        }
                    } else {
                        $this->load->model('comment_model', 'comments');
                        $table = $this->uri_seg == 'topic_comment' ? 'forum' : $this->uri_seg;
                        $cmt = $this->comments->getCommentById($table, $id);
                        $prof_id = $this->authm->get_profile_id();
                        $aid = $this->uri_seg == 'topic_comment' ? $cmt->created_by : $cmt->user_id;
                        if($cmt && $aid != $prof_id) {
                            $to_user = $aid;
                        }
                    }
                    if(!is_null($to_user)) {
                        $this->load->model('notification_model', 'notifications');
                        $row = array(
                            'from_user' => $this->user_id,
                            'type' => $this->uri_seg . "_vote",
                            'to_user' => $to_user,
                            'optional' => $id
                        );
                        $created = date("Y-m-d H:i:s");
                        $ntf = $this->notifications->getSimpleNotification($row);
                        if($ntf) {
                            $this->notifications->updateRow($ntf->id, array('created' => $created));
                        } else {
                            $row['created'] = $created;
                            $this->notifications->insertRow($row);
                        }
                    }
                }
            } else {
                $output['errors'] = lang('error_vote_process');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        
        $this->echo_json($output);
    }
    
    public function all_voters() {
        $output = array('voters' => array());
        
        $id = (int) $this->input->post('id', true);
        $type = $this->input->post('type', true);
        if($id && $type) {
            $voters = $this->votes->whoVoted($id, $type, null);
            $output['voters'] = $voters[$type];
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        
        $this->echo_json($output);
    }
} 

/* End of file vote.php */
/* Location: ./application/controllers/vote.php */