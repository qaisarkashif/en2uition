<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Message_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'message';
        $this->_pk = 'id';
    }

    /**
     * Get the number of unread messages
     *
     * @param int $uid - user ID
     * @return int
     */
    public function getUnreadMessagesNumber($uid) {
        $res = $this->db
                ->where('unread', 1)
                ->where('to_user', $uid)
                ->get($this->_table);
        return $res !== FALSE ? $res->num_rows() : 0;
    }

    /**
     * Get messages (without replies)
     *
     * @param int $uid - user ID
     * @return array
     */
    public function getGeneralMessages($uid) {
        $messages = array();
        $select_fields = array(
            'm.*',
            's.username as s_username',
            's.profile_image as s_profile_image',
            'r.username as r_username',
            'r.profile_image as r_profile_image',
            'if(t1.unr_count > 0, 1, 0) as have_unread',
            'if(t2.fid IS NOT NULL, "friend", "stranger") as who',
            "if(t1.unr_count > 0, 1, if(m.unread AND {$uid} <> m.from_user, 1, 0)) as filter_unread",
            't3.msg_text as last_msg',
            'IF(t3.created IS NOT NULL, t3.created, m.created) as last_date'
        );
        $t1 = "(SELECT reply_to AS mh_id, COUNT(id) AS unr_count FROM " . $this->_table . " WHERE unread = 1 AND from_user <> {$uid} AND reply_to > 0 GROUP BY reply_to) AS t1";
        $t2 = "(SELECT DISTINCT IF(user_id = {$uid}, friend_id, user_id) AS fid FROM user_friend WHERE user_id = {$uid} OR friend_id = {$uid}) AS t2";
        $t3 = "(SELECT * FROM (SELECT reply_to, msg_text, created FROM " . $this->_table . " WHERE from_user = {$uid} OR to_user = {$uid} ORDER BY created DESC) AS tmp GROUP BY reply_to) AS t3";
        $this->db
            ->select(implode(',', $select_fields), false)
            ->from($this->_table . ' as m')
            ->join('user_profile as s', 's.user_id = m.from_user')
            ->join('user_profile as r', 'r.user_id = m.to_user')
            ->join($t1, 'm.id = t1.mh_id', 'left')
            ->join($t2, 'm.from_user = t2.fid OR m.to_user = t2.fid', 'left')
            ->join($t3, 'm.id = t3.reply_to', 'left')
            ->where('m.reply_to IS NULL AND (m.from_user = ' . $uid . ' OR m.to_user = ' . $uid . ')', null, false)
            ->order_by('filter_unread', 'desc')
            ->order_by('last_date', 'desc');

        $res = $this->db->get();

        if ($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach ($res->result_array() as $row) {
                $profile_image = $uid == $row['from_user'] ? $row['r_profile_image'] : $row['s_profile_image'];
                $user_id = $uid == $row['from_user'] ? $row['to_user'] : $row['from_user'];
                $tmp_inf = array(
                    'user_id' => $user_id,
                    'profile_image' => $profile_image
                );
                $messages[$row['id']] = array(
                    'date' => date('g:i a M j, Y', $this->server_to_client_localtime($row['last_date'])),
                    'username' => $uid == $row['from_user'] ? $row['r_username'] : $row['s_username'],
                    'msg_text' => !empty($row['last_msg']) ? $row['last_msg'] : $row['msg_text'],
                    'ava' => $this->authm->get_user_photo('forum', $tmp_inf),
                    'unread' => $row['have_unread'] || ($row['unread'] && $uid != $row['from_user']),
                    'who' => $row['who'],
                    'uid' => $user_id
                );
            }
        }

        return $messages;
    }

    /**
     * Get replies on a message
     *
     * @param int $msg_id - message ID
     * @param int $uid - user ID
     * @return array
     */
    public function getHistoryMessages($msg_id, $uid) {
        $messages = array();

        $this->db
            ->select('m.*, s.username, s.profile_image')
            ->join('user_profile as s', 's.user_id = m.from_user')
            ->from($this->_table . ' as m')
            ->where('m.id', $msg_id)
            ->or_where('m.reply_to', $msg_id)
            ->order_by('m.created', 'asc');

        $res = $this->db->get();

        if ($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach ($res->result_array() as $row) {
                $tmp_inf = array(
                    'user_id' => $row['from_user'],
                    'profile_image' => $row['profile_image']
                );
                $messages[$row['id']] = array(
                    'date' => date('g:i a M j, Y', $this->server_to_client_localtime($row['created'])),
                    'username' => $row['username'],
                    'msg_text' => $row['msg_text'],
                    'ava' => $this->authm->get_user_photo('forum', $tmp_inf),
                    'unread' => $row['unread'],
                    'from_user' => $row['from_user'],
                    'reply_to_user' => $row['from_user'] == $uid ? $row['to_user'] : $row['from_user']
                );
            }
        }

        return $messages;
    }

    /**
     * Mark the message as read
     *
     * @param int $id - message ID
     * @param boolean $replies_too - mark as read also replies on a message
     * @param int $uid - user ID
     * @return boolean
     */
    public function markAsRead($id, $replies_too = false, $uid = null) {
        $this->db->set('unread', 0);
        $where = "(id = {$id} " . ($replies_too ? " OR reply_to = {$id}" : "") . ")";
        if(!is_null($uid)) {
            $where .= " AND from_user <> " . $uid;
        }
        $res = $this->db->where($where)->update($this->_table);
        return $res !== FALSE;
    }

    /**
     * Mark the message as unread
     *
     * @param int $id - message ID
     * @return boolean
     */
    public function markAsUnread($id) {
        $this->db
                ->set('unread', 1)
                ->where('id', $id);
        $res = $this->db->update($this->_table);
        return $res !== FALSE;
    }

    /**
     * Delete the message
     *
     * @param int $id - message ID
     * @return boolean
     */
    public function deleteMessage($id) {
        $this->db
                ->where('id', $id)
                ->or_where('reply_to', $id);
        $res = $this->db->delete($this->_table);
        return $res !== FALSE;
    }
}

/* End of file message_model.php */
/* Location: ./application/models/message_model.php */