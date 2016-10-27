<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'notification';
        $this->_pk = 'id';
    }
    
    /**
     * Get simple notification
     * 
     * @param array $where
     * @return boolean
     */
    public function getSimpleNotification($where) {
        $res = $this->db
                ->where($where)
                ->get($this->_table);
        return $res !== FALSE && $res->num_rows() > 0 ? $res->row() : null;
    }
    
    /**
     * Get notifications
     * 
     * @param int $user_id - user ID
     * @param array $notifications - Array with notifications
     * @param string $type - type of notification
     * @return array
     */
    public function getNotifications($user_id, &$notifications, $type) {
        $lang_notifications = lang('notifications');
        $this->db
                ->select('n.*, up.username, up.profile_image')
                ->from($this->_table . ' as n')
                ->join('user_profile as up', 'up.user_id = n.from_user')
                ->where('n.to_user', $user_id)
                ->order_by('n.created', 'desc');
        
        if(!empty($type)) {
            $this->db->where('n.type', $type);
            switch($type) {
                case 'profile_comment' : 
                    $this->db
                        ->select('pc.target_id')
                        ->join('profile_comment as pc', 'pc.id = n.optional');
                    break;
                case 'photo_comment' : 
                    $this->db
                        ->select('pc.target_id, alb.owner, p.album_id, p.title as photo_title')
                        ->join('photo_comment as pc', 'pc.id = n.optional')
                        ->join('photo as p', 'p.id = pc.target_id')
                        ->join('photo_album as alb', 'alb.id = p.album_id');
                    break;
                case 'photo_vote' : 
                    $this->db
                        ->select('pv.photo_id as target_id, alb.owner, p.album_id, pv.type as vote_type', false)
                        ->join('photo_vote as pv', 'pv.photo_id = n.optional AND pv.user_id = n.from_user')
                        ->join('photo as p', 'p.id = pv.photo_id')
                        ->join('photo_album as alb', 'alb.id = p.album_id');
                    break;
                case 'profile_comment_vote' :
                    $this->db
                        ->select('pc.target_id, pcv.type as vote_type', false)
                        ->join('profile_comment_vote as pcv', 'pcv.comment_id = n.optional AND n.from_user = pcv.user_id')
                        ->join('profile_comment as pc', 'pc.id = pcv.comment_id');
                    break;
                case 'photo_comment_vote' : 
                    $this->db
                        ->select('pc.target_id, alb.owner, p.album_id, pcv.type as vote_type', false)
                        ->join('photo_comment_vote as pcv', 'pcv.comment_id = n.optional AND n.from_user = pcv.user_id')
                        ->join('photo_comment as pc', 'pc.id = pcv.comment_id')
                        ->join('photo as p', 'p.id = pc.target_id')
                        ->join('photo_album as alb', 'alb.id = p.album_id');
                    break;
                case 'topic_comment':
                case 'topic_comment_vote':
                    $select_fields = array(
                        'tc.topic_id as target_id',
                        'if(u2.color = t.color1 AND u2.shape_id = t.shape1, t.color2, t.color1) as url_color',
                        'if(u2.color = t.color1 AND u2.shape_id = t.shape1, sh2.name, sh1.name) as url_shape'
                    );
                    if($type == 'topic_comment_vote') {
                        $select_fields[] = 'tcv.type as vote_type';
                    }
                    $this->db
                        ->select(implode(',', $select_fields), false)
                        ->join('user_profile as u2', 'u2.user_id = n.to_user');
                    if($type == 'topic_comment_vote') {
                        $this->db
                                ->join('topic_comment_vote as tcv', 'tcv.comment_id = n.optional AND n.from_user = tcv.user_id')
                                ->join('forum as tc', 'tc.id = tcv.comment_id');
                    } else {
                        $this->db->join('forum as tc', 'tc.id = n.optional AND n.from_user = tc.created_by');
                    }
                    $this->db
                        ->join('topic as t', 't.id = tc.topic_id')
                        ->join('shape as sh1', 'sh1.id = t.shape1')
                        ->join('shape as sh2', 'sh2.id = t.shape2')
                        ->where("((u2.color = t.color1 AND u2.shape_id = t.shape1) OR (u2.color = t.color2 AND u2.shape_id = t.shape2))", null, false);
                    break;
            }
        }
        $res = $this->db->get();
        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                $time_key = $this->server_to_client_localtime($row['created']);
                if(!isset($notifications['items'][$time_key])) {
                    $notifications['items'][$time_key] = array();
                }
                $key = $row['from_user'] . "|" . $row['type'] . "|" . $row['optional'];
                if(isset($notifications['items'][$time_key][$key])) {
                    continue;
                }
                $row['when'] = date('\a\t g:ia M j, Y', $time_key);
                $tmp_inf = array(
                    'user_id' => $row['from_user'],
                    'profile_image' => $row['profile_image']
                );
                $row['ava'] = $this->authm->get_user_photo('forum', $tmp_inf);
                if(in_array($row['type'], array('profile_comment', 'photo_comment', 'topic_comment'))) {
                    $row['notify_text'] = $lang_notifications[$row['type']];
                    if($row['type'] == 'photo_comment') {
                        $row['notify_text'] .= ": " . $row['photo_title'];
                    }
                } else {
                    $lkey = $row['type'] . "_" . $row['vote_type'];
                    $row['notify_text'] = $lang_notifications[$lkey];
                    unset($row['vote_type']);
                }
                if (in_array($row['type'], array('photo_comment', 'photo_comment_vote', 'photo_vote'))) {
                    $row['url_part'] = "/" . $row['album_id'] . "/" . $row['target_id'] . "/" . $row['owner'];
                    unset($row['album_id'], $row['owner']);
                } elseif ($row['type'] == 'profile_comment' || $row['type'] == 'profile_comment_vote') {
                    $row['optional'] = $row['target_id'];
                } elseif($row['type'] == 'topic_comment' || $row['type'] == 'topic_comment_vote') {
                    $row['url_part'] = $row['target_id'] . "/" . $row['url_color'] . "/" . $row['url_shape'];
                    unset($row['url_color'], $row['url_shape']);
                }
                $notifications['items'][$time_key][$key] = $row;
                $notifications['count']++;
            }
        }
    }
    
    /**
     * Delete notifications
     * 
     * @param int $to_user - user ID
     * @param int $tid - target ID
     * @param array $types
     * @return boolean 
     */
    public function deleteNotifications($to_user, $tid, $types = array()) {
        $this->db->trans_start();
        foreach ($types as $type) {
            if ($type == 'photo_vote') {
                $query = "DELETE FROM notification WHERE to_user = {$to_user} AND optional = {$tid} AND type = 'photo_vote'";
            } elseif ($type == 'photo_comment' || $type == 'profile_comment') {
                $query = "DELETE n.* FROM notification as n JOIN {$type} as pc on (n.optional = pc.id AND n.from_user = pc.user_id) WHERE n.to_user = {$to_user} AND n.type = '{$type}' AND pc.target_id = {$tid}";
            } elseif ($type == 'photo_comment_vote' || $type == 'profile_comment_vote') {
                $query = "DELETE n.* FROM notification as n JOIN {$type} as pcv on (pcv.comment_id = n.optional AND n.from_user = pcv.user_id) 
                    JOIN " . str_replace("_vote", "", $type) . " as pc on (pcv.comment_id = pc.id) WHERE n.to_user = {$to_user} AND n.type = '{$type}' AND pc.target_id = {$tid}";
            } elseif($type == 'topic_comment_vote') {
                $query = "DELETE n.* FROM notification as n JOIN {$type} as tcv on (tcv.comment_id = n.optional AND n.from_user = tcv.user_id) 
                JOIN forum as tc on (tcv.comment_id = tc.id) WHERE n.to_user = {$to_user} AND n.type = '{$type}' AND tc.topic_id = {$tid}";
            } elseif($type == 'topic_comment') {
                $query = "DELETE n.* FROM notification as n JOIN forum as tc on (tc.id = n.optional AND n.from_user = tc.created_by)
                JOIN topic as t on (t.id = tc.topic_id) WHERE n.to_user = {$to_user} AND n.type = '{$type}' AND tc.topic_id = {$tid}";
            }
            $this->db->query($query);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}

/* End of file notification_model.php */
/* Location: ./application/models/notification_model.php */