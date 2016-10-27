<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comment_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->load->model('friend_model', 'friends');
    }
    
    /**
     * Get comments
     *
     * @param string $table
     * @param int $last_id - last viewed comment
     * @param int $target_id (optional)
     * @param int $my_id (optional) - user ID
     * @param boolean $for_updates (optional)
     * @return array
     */
    public function getComments($table, $last_id, $target_id = null, $my_id = null, $for_updates = false) {
        $comments = $replies_to = array();
        
        if($table == 'profile_comment' && !empty($target_id) && !empty($my_id) && $my_id != $target_id) {
            if(!$this->friends->isMyFriend($my_id, $target_id)) {
                return array();
            }
        }
        
        if(!empty($my_id) && $last_id == -1 && !$for_updates) {
            //remove notifications
            $this->load->model('notification_model', 'notifications');
            $this->notifications->deleteNotifications($my_id, $target_id, array($table, $table . "_vote"));
        }
        
        $p = 'comment';
        $subquery = "SELECT v.{$p}_id, substring_index(group_concat(u.username SEPARATOR '|'), '|', 10) as who_voted FROM {$table}_vote as v JOIN user_profile AS u USING (user_id) WHERE v.type = '%s' GROUP BY {$p}_id";
        $select_fields = "'{$table}' as target, c.*, u.username, u.profile_image, t.like_cnt, t.dislike_cnt, t2.who_voted as who_voted_like, t3.who_voted as who_voted_dislike, s.shares_count";
        $join = " LEFT JOIN user_profile as u ON (u.user_id = c.user_id)";
        $join .= " LEFT JOIN (SELECT {$p}_id, SUM(IF(type='like', 1, 0)) AS like_cnt, SUM(IF(type='dislike', 1, 0)) AS dislike_cnt FROM {$table}_vote GROUP BY {$p}_id) as t ON (t.{$p}_id = c.id)";
        $join .= " LEFT JOIN (" . sprintf($subquery, "like") . ") as t2 ON (t2.{$p}_id = c.id)";
        $join .= " LEFT JOIN (" . sprintf($subquery, "dislike") . ") as t3 ON (t3.{$p}_id = c.id)";
        $join .= " LEFT JOIN (SELECT {$p}_id, COUNT(id) AS shares_count FROM {$table}_share GROUP BY {$p}_id) as s ON (c.id = s.{$p}_id OR s.{$p}_id = c.reply_to)";
        
        if (!empty($my_id)) {
            $select_fields .= ", v.type as my_vote, ps.id as my_share";
            $join .= " LEFT JOIN {$table}_vote as v ON (v.comment_id = c.id AND v.user_id = {$my_id})";
            $join .= " LEFT JOIN {$table}_share as ps ON (ps.comment_id = c.id AND ps.user_id = {$my_id})";
        }
        
        if($for_updates && !empty($my_id)) {
            $friends_ids = $this->friends->getFriendsIDs($my_id);
            $friends_ids[] = $my_id;
            $friends_ids = implode(',', $friends_ids);
            if($table == 'profile_comment') {
                $sub = " AND c.user_id = c.target_id";
            } else {
                $select_fields .= ", alb.owner, alb.id as album_id ";
                $join .= " JOIN photo_album as alb ON (alb.owner IN ({$friends_ids})) ";
                $join .= " JOIN photo ON (alb.id = photo.album_id AND (photo.privacy_code IS NULL OR photo.privacy_code = '') AND c.target_id = photo.id)";
                $sub = " AND c.user_id = alb.owner";
            }
        }
        
        for($i = 1; $i <= 2; $i++) {
            if($i == 2) {
                $ord = 'ASC';
                if(count($replies_to) == 0) continue;
                $where = 'WHERE c.reply_to IS NOT NULL AND c.reply_to IN (' . implode(',', $replies_to) . ")";
            } else {
                $ord = 'DESC';
                $where = 'WHERE c.reply_to IS NULL';
                if($for_updates) {
                    $where .= " AND ((c.user_id IN ({$friends_ids}) {$sub}) OR (c.id IN (SELECT comment_id FROM {$table}_share WHERE user_id IN ({$friends_ids}))))";
                }
            }
            if($last_id > 0) {
                $where .= " AND c.id < {$last_id}";
            }
            if(!empty($target_id) && !$for_updates) {
                $where .= " AND target_id = {$target_id}";
            }
            $query = "SELECT {$select_fields} FROM {$table} as c {$join} " . (!empty($where) ? $where : "") . " ORDER BY c.id {$ord} LIMIT " . ($for_updates ? UPDATES_LIMIT : COMMENTS_LIMIT);
            $result = $this->db->query($query);

            if ($result !== FALSE) {
                $this->load->model('auth_model', 'authm');
                foreach ($result->result_array() as $row) {
                    $key = $this->server_to_client_localtime($row['created']);
                    $row['unix_date'] = $key;
                    $row['date'] = date('\o\n l, F j, Y \a\t g.ia', $key);
                    $tmp_inf = array(
                        'user_id' => $row['user_id'],
                        'profile_image' => $row['profile_image']
                    );
                    $row['ava'] = $this->authm->get_user_photo('forum', $tmp_inf);
                    if($i == 1) {
                        $row['replies'] = array();
                        $comments[$row['id']] = $row;
                        $replies_to[] = $row['id'];
                    } else {
                        $comments[$row['reply_to']]['replies'][$row['id']] = $row;
                    }
                }
                $result->free_result();
            }
        }
        return $comments;
    }

    /**
     * Unshare comment
     *
     * @param array $data
     * @param string $table
     * @return boolean
     */
    public function unshareComment($data, $table) {
        $res = $this->db
                ->where($data)
                ->delete($table);
        return $res !== FALSE;
    }

    /**
     * Check if comment is already shared
     *
     * @param array $data
     * @param string $table
     * @return boolean
     */
    public function isAlreadyShared($data, $table) {
        $res = $this->db
                ->where($data)
                ->get($table);
        return $res !== FALSE && $res->num_rows() > 0;
    }

    /**
     * Delete comment
     * 
     * @param int $user_id - user ID
     * @param string $target - table name
     * @param int $target_id
     * @return boolean 
     */
    public function deleteComment($user_id, $target, $target_id) {
        $this->db->trans_start();

        $sub_where = "user_id = {$user_id} AND (id = {$target_id} OR reply_to = {$target_id})";

        $del_vote_query = "DELETE t.* FROM {$target}_vote as t WHERE t.comment_id IN (SELECT id FROM {$target} WHERE {$sub_where})";
        $this->db->query($del_vote_query);
        $del_share_query = "DELETE t.* FROM {$target}_share as t WHERE t.comment_id IN (SELECT id FROM {$target} WHERE {$sub_where})";
        $this->db->query($del_share_query);
        $del_comment_query = "DELETE FROM {$target} WHERE {$sub_where}";
        $this->db->query($del_comment_query);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    /**
     * Get comment by ID
     * 
     * @param string $table - table name
     * @param int $cmt_id - comment ID
     * @return object or null
     */
    public function getCommentById($table, $cmt_id) {
        $cmt = $this->db
                ->where('id', $cmt_id)
                ->limit(1)
                ->get($table);
        if($cmt !== FALSE && $cmt->num_rows() == 1) {
            return $cmt->row();
        }
        return null;
    }

}

/* End of file comment_model.php */
/* Location: ./application/models/comment_model.php */