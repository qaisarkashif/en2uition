<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Forum_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'forum';
        $this->_pk = 'id';
    }

    /**
     * Get topics
     *
     * @param string $my_color - color of my shape
     * @param int $my_shape - id of my shape
     * @param string $color - shape color
     * @param int $shape (optional) - shape id
     * @param int $id (optional) - topic ID
     * @return array
     */
    public function getTopics($my_color, $my_shape, $color, $shape = null, $id = null) {
        $where = "((t.color1 = '{$my_color}' AND t.shape1 = {$my_shape} AND t.color2 = '{$color}'" . (!empty($shape) ? " AND t.shape2 = {$shape}" : "") . ")";
        $where .= " OR (t.color2 = '{$my_color}' AND t.shape2 = {$my_shape} AND t.color1 = '{$color}'" . (!empty($shape) ? " AND t.shape1 = {$shape}" : "") . "))";
        $this->db
                ->select('t.*, count(distinct f.created_by) as users_count, count(f.id) as posts_count, count(tcs.id) as shares_count, max(f.created) as date_lastpost', false)
                ->from('topic as t')
                ->join('user_profile as u', 'u.user_id = t.created_by')
                ->join($this->_table . ' as f', 'f.topic_id = t.id', 'left')
                ->join('topic_comment_share as tcs', 'tcs.comment_id = f.id', 'left')
                ->where($where, null, false)
                ->group_by('t.id')
                ->order_by('t.created_datetime', 'desc');

        if(!empty($id)) {
            $this->db->where('t.id', $id)->limit(1);
        }
        $res = $this->db->get();
        $topics = array();
        if($res !== FALSE) {
            if(!empty($id)) {
                return $res->num_rows() == 1 ? $res->row() : NULL;
            }
            foreach($res->result_array() as $row) {
                $key = $row['color2'] == $my_color && $row['shape2'] == $my_shape ? $row['shape1'] : $row['shape2'];
                if(!isset($topics[$key])) {
                    $topics[$key] = array();
                }
                $row['created_datetime'] = date("c", $this->server_to_client_localtime($row['created_datetime']));
                $row['date_lastpost'] = date("c", $this->server_to_client_localtime($row['date_lastpost']));
                $topics[$key][] = $row;
            }
        }
        return $topics;
    }
    
    /**
     * Get list of the participants (on the specified last post date)
     * 
     * @param string $c1 - my color
     * @param int $s1 - my shape ID
     * @param string $c2 - color 2
     * @param int $s2 - shape 2 ID
     * @param array $f (optional) - filters
     * @param int $page (optional)
     * @param int $limit (optional)
     * @param boolean $skip_key (optional)
     * @return array
     */
    public function getTopicParticipants($c1, $s1, $c2, $s2, $f = array(), $page = 0, $limit = 40, $skip_key = false) {
        $where = "((t.color1 = '{$c1}' and t.shape1 = {$s1} and t.color2 = '{$c2}' and t.shape2 = {$s2})";
        $where .= " or (t.color2 = '{$c1}' and t.shape2 = {$s1} and t.color1 = '{$c2}' and t.shape1 = {$s2}))";
        
        $where .= isset($f['uid']) ? " and t.created_by = " . $f['uid'] : "";
        $where .= isset($f['topic']) ? " and t.title like '%" . $f['topic'] . "%'" : "";
        
        $where2 = "((up.color = '{$c1}' and up.shape_id = {$s1}) or (up.color = '$c2' and up.shape_id = {$s2}))";
        
        $where2 .= isset($f['country']) ? " and up.country = '" . $f['country'] . "'" : "";
        $where2 .= isset($f['state']) ? " and up.state = '" . $f['state'] . "'" : "";
        $where2 .= isset($f['city']) ? " and up.city = '" . $f['city'] . "'" : "";
        
        if(isset($f['lastpost_date'])) {
            $having = "having date(max(f.created)) = date('" . str_replace('.', '-', $f['lastpost_date']) . "')";
        } else {
            $having = "";
        }
        
        $offset = ($page * $limit) . "," . $limit;
        
        $query = "
            select distinct up.user_id, up.username, up.profile_image, up.color, up.shape_id, sh.name as shape_name, nt.*
            from forum 
                join (
                    select 
                        t.id, t.title, t.created_datetime, count(distinct f.created_by) as users_count, count(f.id) as posts_count, count(tcs.id) as shares_count, max(f.created) as date_lastpost
                    from 
                        topic as t
                        left join forum as f on (f.topic_id = t.id)
                        left join topic_comment_share as tcs on (tcs.comment_id = f.id)
                    where {$where} 
                    group by t.id
                    {$having}
                ) as nt on (forum.topic_id = nt.id)
                join user_profile as up on (up.user_id = forum.created_by)
                join shape as sh on (sh.id = up.shape_id)
            where {$where2}
            order by nt.created_datetime desc
            limit {$offset}";
            
        $res = $this->db->query($query);
        $resArr = array();
        if($res !== false) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                $d1 = $this->server_to_client_localtime($row['created_datetime']);
                $d2 = $this->server_to_client_localtime($row['date_lastpost']);
                $row['created_datetime'] = date("M j, Y", $d1);
                $row['last_post1'] = date("Y.m.d", $d2);
                $row['last_post2'] = date('M j, Y \a\t g:i a', $d2);
                if(strlen($row['title']) > 25) {
                    $row['title'] = substr($row['title'], 0, 25) . "...";
                }
                $uinf = array(
                    'user_id' => $row['user_id'], 
                    'profile_image' => $row['profile_image']
                );
                $row['ava'] = $this->authm->get_user_photo('forum', $uinf);
                $row['encoded_username'] = urlencode($row['username']);
                if($skip_key) {
                    $resArr[] = $row;
                } else {
                    $key = $row['color'] . $row['shape_id'];
                    if(!isset($resArr[$key])) {
                        $resArr[$key] = array();
                    }
                    $resArr[$key][] = $row;
                }
            }
        }
        return $resArr;
    }

    /**
     * Get Topics by username
     *
     * @param string $my_color
     * @param int $my_shape
     * @param string $username
     * @param int $last_id (optional)
     * @return array
     */
    public function getTopicsByUsername($my_color, $my_shape, $username, $last_id = -1) {
        $join_my_topics = "(SELECT DISTINCT f.topic_id, u.shape_id, u.color FROM " . $this->_table .
                " as f JOIN user_profile as u ON u.user_id = f.created_by WHERE u.username = '{$username}')";
        $sfields = array(
            't.*',
            'sh1.name as shape1_name',
            'sh2.name as shape2_name',
            'sh3.name as u2_shape',
            'mt.color as u2_color',
            'count(distinct f.created_by) as users_count',
            'count(f.id) as posts_count',
            'count(tcs.id) as shares_count',
            'max(f.created) as date_lastpost',
        );
        $this->db
                ->select(implode(',', $sfields), false)
                ->from('topic as t')
                ->join($join_my_topics . ' as mt', 'mt.topic_id = t.id')
                ->join($this->_table . ' as f', 'f.topic_id = t.id', 'left')
                ->join('shape as sh1', 'sh1.id = t.shape1')
                ->join('shape as sh2', 'sh2.id = t.shape2')
                ->join('shape as sh3', 'sh3.id = mt.shape_id')
                ->join('topic_comment_share as tcs', 'tcs.comment_id = f.id', 'left')
                ->where("((t.color1 = '{$my_color}' AND t.shape1 = {$my_shape}) OR (t.color2 = '{$my_color}' AND t.shape2 = {$my_shape}))", null, false)
                ->group_by('t.id')
                ->order_by('t.id', 'desc')
                ->limit(NEIGHB_USERNAME_LIMIT);
        if($last_id > 0) {
            $this->db->where('t.id <', $last_id);
        }
        $res = $this->db->get();

        $resArray = $res !== FALSE ? $res->result_array() : array();
        foreach ($resArray as $idx => $topic) {
            $d1 = $this->server_to_client_localtime($topic['created_datetime']);
            $d2 = $this->server_to_client_localtime($topic['date_lastpost']);
            $resArray[$idx]['date_posted'] = date("M j, Y", $d1);
            $resArray[$idx]['last_post_url'] = date("Y.m.d", $d2);
            $resArray[$idx]['last_post_text'] = date('M j, Y \a\t g:i a', $d2);
            if($my_color == $topic['color2'] && $my_shape == $topic['shape2']) {
                $resArray[$idx]['row_color'] = $topic['color1'];
                $resArray[$idx]['row_shape'] = $topic['shape1_name'];
            } else {
                $resArray[$idx]['row_color'] = $topic['color2'];
                $resArray[$idx]['row_shape'] = $topic['shape2_name'];
            }
        }

        return $resArray;
    }

    /**
     * Get array with available post dates (for filter)
     *
     * @param int $my_color
     * @param string $my_shape
     * @param int $color
     * @param string $shape
     * @return array
     */
    public function getAvailablePostDate($my_color, $my_shape, $color, $shape) {
        $where = "((t.color1 = '{$my_color}' AND t.shape1 = {$my_shape} AND t.color2 = '{$color}' AND t.shape2 = {$shape})";
        $where .= " OR (t.color2 = '{$my_color}' AND t.shape2 = {$my_shape} AND t.color1 = '{$color}' AND t.shape1 = {$shape}))";
        $this->db
                ->select('distinct date(max(f.created)) as d', false)
                ->from($this->_table . ' as f')
                ->join('topic as t', 'f.topic_id = t.id')
                ->where($where, null, false)
                ->order_by('d', 'desc')
                ->group_by('t.id');
        $res = $this->db->get();
        return $res !== FALSE ? $res->result_array() : array();
    }

    /**
     * Get comments to a topic
     *
     * @param int $id - topic ID
     * @param int $my_id
     * @return array
     */
    public function getCommentsToTopic($id, $my_id) {
        $comments = array();
        $query = $this->getCommentsQuery($id, "all", $my_id);
        $res = $this->db->query($query);

        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            $rel = array();
            foreach($res->result_array() as $row) {
                $k = $row['id'];
                $r = $row['reply_to'];
                $row['txt'] = htmlspecialchars_decode($row['txt']);
                $created = $this->server_to_client_localtime($row['created']);
                $short_text = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), " ", htmlspecialchars_decode($row['txt'])));
                if (strlen($short_text) > 150) {
                    $short_text = substr($short_text, 0, 150);
                } else {
                    $short_text = "";
                }
                $ava = $this->authm->get_user_photo('forum', $row);
                if ($r == 0) {
                    $comments[$k] = array(
                        'author_id' => $row['user_id'],
                        'author' => $row['username'],
                        'ava' => $ava,
                        'date' => date("M j, Y", $created),
                        'time' => date("g:ia", $created),
                        'short_text' => $short_text,
                        'full_text' => $row['txt'],
                        'like_count' => $row['like_cnt'],
                        'dislike_count' => $row['dislike_cnt'],
                        'like_tooltip_title' => $this->getTooltipTitle($row['who_voted_like'], 'like', $row['id']),
                        'dislike_tooltip_title' => $this->getTooltipTitle($row['who_voted_dislike'], 'dislike', $row['id']),
                        'shares_count' => $row['shares_count'],
                        'my_vote' => $row['my_vote'],
                        'my_share' => $row['my_share'],
                        'deleted' => $row['deleted'],
                        'replies' => array()
                    );
                } else if ($r > 0) {
                    if (isset($comments[$r])) {
                        $rel[$k] = $r;
                        $comments[$r]['replies'][$k] = array(
                            'ava' => $ava,
                            'rc' => 1
                        );
                    } elseif(isset($rel[$r])) {
                        $comments[$rel[$r]]['replies'][$r]['rc']++;
                    }
                }
            }
        }

        return $comments;
    }

    /**
     * Get replies to specified comment
     *
     * @param int $id - comment ID
     * @param int $my_id
     * @return array
     */
    public function getReplies($id, $my_id) {
        $replies = array();
        $query = $this->getCommentsQuery($id, "replies", $my_id);
        $res = $this->db->query($query);
        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                $row['txt'] = htmlspecialchars_decode($row['txt']);
                $created = $this->server_to_client_localtime($row['created']);
                $short_text = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), " ", htmlspecialchars_decode($row['txt'])));
                if(strlen($short_text) > 150) {
                    $short_text = substr($short_text, 0, 150);
                } else {
                    $short_text = "";
                }
                $replies[$row['id']] = array(
                    'author_id' => $row['user_id'],
                    'author' => $row['username'],
                    'ava' => $this->authm->get_user_photo('forum', $row),
                    'date' => date("M j, Y", $created),
                    'time' => date("g:ia", $created),
                    'short_text' => $short_text,
                    'full_text' => $row['txt'],
                    'reply_to' => $row['reply_to'],
                    'like_count' => $row['like_cnt'],
                    'dislike_count' => $row['dislike_cnt'],
                    'like_tooltip_title' => $this->getTooltipTitle($row['who_voted_like'], 'like', $row['id']),
                    'dislike_tooltip_title' => $this->getTooltipTitle($row['who_voted_dislike'], 'dislike', $row['id']),
                    'shares_count' => $row['shares_count'],
                    'my_vote' => $row['my_vote'],
                    'my_share' => $row['my_share'],
                    'deleted' => $row['deleted']
                );
            }
        }
        return $replies;
    }

    /**
     * Get the number of topics with unviewed replies
     *
     * @param int $my_id
     * @param string $c1 - my color
     * @param int $s1 - my shape
     * @param string $c2 (optional)
     * @param int $s2 (optional)
     * @return array
     */
    public function getUnviewedTopicsCount($my_id, $c1, $s1, $c2 = null, $s2 = null) {
        $forums = array("red" => 0, "green" => 0, "blue" => 0, "yellow" => 0, 'ids' => array());
        $where = "(t.color1 = '{$c1}' AND t.shape1 = {$s1}" . (!empty($c2) ? " AND t.color2 = '{$c2}'" : "") . (!empty($s2) ? " AND t.shape2 = {$s2}" : "") . ")";
        $where .= " or (t.color2 = '{$c1}' AND t.shape2 = {$s1}" . (!empty($c2) ? " AND t.color1 = '{$c2}'" : "") . (!empty($s2) ? " AND t.shape1 = {$s2}" : "") . ")";
        $res = $this->db
                ->select('t.id, t.color1, t.shape1, t.color2, t.shape2, if(max(f.created) > log.last_view or log.last_view is null, 1, 0) as unviewed', false)
                ->from('topic as t')
                ->join('user_topic_log as log', "log.topic_id = t.id AND log.user_id = {$my_id}", 'left')
                ->join('forum as f', "f.topic_id = t.id AND f.created_by <> {$my_id}")
                ->where($where, null, false)
                ->group_by('t.id')
                ->having('unviewed = 1')
                ->get();

        if ($res !== FALSE) {
            foreach ($res->result_array() as $row) {
                if ($row['color1'] == $c1 && $row['shape1'] == $s1) {
                    $forums[$row['color2']]++;
                    $shape_key = $row['shape2'];
                } else {
                    $forums[$row['color1']]++;
                    $shape_key = $row['shape1'];
                }
                if (!isset($forums[$shape_key])) {
                    $forums[$shape_key] = 0;
                }
                $forums[$shape_key]++;
                $forums['ids'][] = $row['id'];
            }
        }

        return $forums;
    }

    /**
     * Mark comment as 'deleted'
     *
     * @param int $user_id - user ID
     * @param int $id - comment ID
     * @param boolean $full_delete (optional) - completely delete a comment
     * @return boolean
     */
    public function deleteComment($user_id, $id, $full_delete = false) {
        if($full_delete) {
            $this->db->where('id', $id);
            $res = $this->db->delete($this->_table);
        } else {
            $this->db
                    ->set('deleted', 1)
                    ->where('created_by', $user_id)
                    ->where('id', $id);
            $res = $this->db->update($this->_table);
        }
        return $res !== FALSE;
    }

    private function getCommentsQuery($id, $target, $my_id) {
        $subquery = 'SELECT v.comment_id, substring_index(group_concat(u.username SEPARATOR "|"), "|", 10) as who_voted FROM topic_comment_vote as v JOIN user_profile AS u USING (user_id) WHERE v.type = "%s" GROUP BY comment_id';
        $select_fields = 'f.id, f.reply_to, f.created, f.txt, f.deleted, u.user_id, u.username, u.profile_image, t.like_cnt, t.dislike_cnt, t2.who_voted as who_voted_like, t3.who_voted as who_voted_dislike, s.shares_count, v.type as my_vote, ps.id as my_share ';
        $join = " LEFT JOIN user_profile as u ON (u.user_id = f.created_by)";
        $join .= ' LEFT JOIN (SELECT comment_id, SUM(IF(type = "like", 1, 0)) AS like_cnt, SUM(IF(type = "dislike", 1, 0)) AS dislike_cnt FROM topic_comment_vote GROUP BY comment_id) as t ON (t.comment_id = f.id)';
        $join .= ' LEFT JOIN (' . sprintf($subquery, "like") . ') as t2 ON (t2.comment_id = f.id) ';
        $join .= ' LEFT JOIN (' . sprintf($subquery, "dislike") . ') as t3 ON (t3.comment_id = f.id) ';
        $join .= ' LEFT JOIN (SELECT comment_id, COUNT(id) AS shares_count FROM topic_comment_share GROUP BY comment_id) as s ON (f.id = s.comment_id)';
        $join .= ' LEFT JOIN topic_comment_vote AS v ON (v.comment_id = f.id AND v.user_id = ' . $my_id . ')';
        $join .= ' LEFT JOIN topic_comment_share AS ps ON (ps.comment_id = f.id AND ps.user_id = ' . $my_id . ')';
        if($target == 'replies') {
            $where = "f.id = {$id} OR f.reply_to = {$id}";
        } else {
            $where = "f.topic_id = " . $id;
        }
        $query = "SELECT {$select_fields} FROM " . $this->_table. " AS f {$join} WHERE {$where} ORDER BY f.reply_to ASC, f.created ASC";
        return $query;
    }

    /**
     * Get tooltip
     *
     * @param string $str - input string
     * @param string $type - 'like' or 'dislike'
     * @param int $cid - comment ID
     * @return string
     */
    private function getTooltipTitle($str, $type, $cid) {
        $title = str_replace("|", "<br/>", $str);
        if (substr_count($str, '|') >= 10) {
            $title .= "<br/><a class='show-all-voters' data-type='{$type}' data-cid='{$cid}' style='color: blue; text-decoration: underline;'>" . lang('btn_show_all_voters') . "</a>";
        }
        return lang('voted') . ($type == 'like' ? ' +' : ' -') . "1: <br/>" . $title;
    }

    /**
     * Update the time of the last view of Topic
     *
     * @param int $topic_id - topic ID
     * @param int $user_id - user ID
     * @return boolean
     */
    public function updateUserTopicsLog($topic_id, $user_id) {
        $data = array(
            'user_id' => $user_id,
            'topic_id' => $topic_id
        );
        $log = $this->db->where($data)->get('user_topic_log');
        if($log !== FALSE) {
            if($log->num_rows() > 0) {
                $this->db->set('last_view', date('Y-m-d H:i:s'))->where($data);
                return $this->db->update('user_topic_log');
            } else {
                return $this->insertRow($data, 'user_topic_log');
            }
        }
        return FALSE;
    }

    /**
     * Check whether to delete the topic
     *
     * @param int $user_id - user ID
     * @param int $cmt_id - comment ID
     * @return mixed (FALSE - if not, and topic ID - if yes)
     */
    public function needDeleteTopic($user_id, $cmt_id) {
        $this->db
                ->select('f.topic_id, f.created_by')
                ->from($this->_table . ' as f')
                ->join($this->_table . ' as f2', "f.topic_id = f2.topic_id AND f2.id = {$cmt_id}")
                ->where('f.deleted', 0);
        $res = $this->db->get();
        if ($res !== FALSE && $res->num_rows() == 1) {
            $row = $res->row();
            if($row->created_by == $user_id) {
                return $row->topic_id;
            }
        }
        return FALSE;
    }

    /**
     * Delete topic
     *
     * @param int $topic_id - topic ID
     * @return boolean
     */
    public function deleteTopic($topic_id) {
        $this->db->trans_start();

        $del_shares = "DELETE tcs.* FROM topic_comment_share as tcs JOIN forum as f ON (f.topic_id = {$topic_id} AND f.id = tcs.comment_id)";
        $this->db->query($del_shares);
        $del_votes = "DELETE tcv.* FROM topic_comment_vote as tcv JOIN forum as f ON (f.topic_id = {$topic_id} AND f.id = tcv.comment_id)";
        $this->db->query($del_votes);
        $del_comments = "DELETE FROM forum WHERE topic_id = {$topic_id}";
        $this->db->query($del_comments);
        $del_topic = "DELETE FROM topic WHERE id = {$topic_id}";
        $this->db->query($del_topic);
        $del_log = "DELETE FROM user_topic_log WHERE topic_id = {$topic_id}";
        $this->db->query($del_log);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Get shared topic's comments (for homepage updates)
     *
     * @param int $user_id - user ID
     * @param array $my_group - my forum's group
     * @param int $last_id
     */
    public function getSharedTopicComments($user_id, $my_group, $last_id) {
        $my_group_join = "(topic.color1 = '" . $my_group['color'] . "' AND topic.shape1 = " . $my_group['shape_id'] . ") OR ";
        $my_group_join .= "(topic.color2 = '" . $my_group['color'] . "' AND topic.shape2 = " . $my_group['shape_id'] . ")";
        $subquery = 'SELECT v.comment_id, substring_index(group_concat(u.username SEPARATOR "|"), "|", 10) as who_voted FROM topic_comment_vote as v JOIN user_profile AS u USING (user_id) WHERE v.type = "%s" GROUP BY comment_id';
        $select_fields = array(
            '"topic_comment" as target',
            'f.id',
            'f.txt',
            'f.topic_id',
            'topic.color1',
            'topic.color2',
            'topic.shape1',
            'topic.shape2',
            'topic.title as topic_title',
            'sn1.name as shape1_name',
            'sn2.name as shape2_name',
            'tcs.id as shared_id',
            'tcs.shared',
            'u.user_id',
            'u.username',
            'u.profile_image',
            't.like_cnt',
            't.dislike_cnt',
            't2.who_voted as who_voted_like',
            't3.who_voted as who_voted_dislike',
            's.shares_count',
            'v.type as my_vote',
            'ps.id as my_share'
        );
        $join = " JOIN topic ON (f.topic_id = topic.id AND ({$my_group_join})) ";
        $join .= " JOIN topic_comment_share as tcs ON (f.id = tcs.comment_id AND f.created_by = {$user_id})";
        $join .= " JOIN shape as sn1 ON (topic.shape1 = sn1.id) ";
        $join .= " JOIN shape as sn2 ON (topic.shape2 = sn2.id) ";
        $join .= " LEFT JOIN user_profile as u ON (u.user_id = f.created_by)";
        $join .= ' LEFT JOIN (SELECT comment_id, SUM(IF(type = "like", 1, 0)) AS like_cnt, SUM(IF(type = "dislike", 1, 0)) AS dislike_cnt FROM topic_comment_vote GROUP BY comment_id) as t ON (t.comment_id = f.id)';
        $join .= ' LEFT JOIN (' . sprintf($subquery, "like") . ') as t2 ON (t2.comment_id = f.id) ';
        $join .= ' LEFT JOIN (' . sprintf($subquery, "dislike") . ') as t3 ON (t3.comment_id = f.id) ';
        $join .= ' LEFT JOIN (SELECT comment_id, COUNT(id) AS shares_count FROM topic_comment_share GROUP BY comment_id) as s ON (f.id = s.comment_id)';
        $join .= ' LEFT JOIN topic_comment_vote AS v ON (v.comment_id = f.id AND v.user_id = ' . $user_id . ')';
        $join .= ' LEFT JOIN topic_comment_share AS ps ON (ps.comment_id = f.id AND ps.user_id = ' . $user_id . ')';
        $where = $last_id > 0 ? "AND tcs.id < {$last_id}" : "";
        
        $comments = array();
        
        $query = "SELECT " . implode(', ', $select_fields) . " FROM " . $this->_table. " AS f {$join} WHERE f.deleted = 0 {$where} ORDER BY tcs.id DESC LIMIT " . UPDATES_LIMIT;
        $res = $this->db->query($query);

        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                $key = $this->server_to_client_localtime($row['shared']);
                $row['unix_date'] = $key;
                $row['date'] = date('\o\n l, F j, Y \a\t g.ia', $key);
                $tmp_inf = array(
                    'user_id' => $row['user_id'],
                    'profile_image' => $row['profile_image']
                );
                $row['ava'] = $this->authm->get_user_photo('forum', $tmp_inf);
                $row['comment'] = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), " ", htmlspecialchars_decode($row['txt'])));
                if($my_group['color'] == $row['color2'] && $my_group['shape_id'] == $row['shape2']) {
                    $row['color'] = $row['color1'];
                    $row['shapename'] = $row['shape1_name'];
                } else {
                    $row['color'] = $row['color2'];
                    $row['shapename'] = $row['shape2_name'];
                }
                $comments[$row['shared_id']] = $row;
            }
            $res->free_result();
        }
        
        return $comments;
    }

    /**
     * Get topic's comment by ID
     *
     * @param int $id - comment ID
     * @return object or null (if comment not found)
     */
    public function getCommentById($id) {
        $cmt = $this->db
                ->where($this->_pk, $id)
                ->limit(1)
                ->get($this->_table);
        return $cmt !== FALSE && $cmt->num_rows() == 1 ? $cmt->row() : null;
    }

    /**
     * Get previous comment in the left column
     *
     * @param int $topic_id - topic ID
     * @param int $new_id - comment ID
     * @return object or null (if comment not found)
     */
    public function getPreviousComment($topic_id, $new_id) {
        $cmt = $this->db
                ->where('reply_to', 0)
                ->where('topic_id', $topic_id)
                ->where('id != ', $new_id)
                ->limit(1)
                ->order_by('created', 'desc')
                ->get($this->_table);
        return $cmt !== FALSE && $cmt->num_rows() == 1 ? $cmt->row() : null;
    }

    /**
     * Check if this comment is the last comment in the left column
     *
     * @param int $cmt_id - comment id
     * @return boolean
     */
    public function isLastCommentInLeftColumn($cmt_id) {
        $query = "SELECT f.id FROM " . $this->_table . " as f JOIN (SELECT topic_id FROM " . $this->_table . " WHERE id = {$cmt_id}) as tmp ON (tmp.topic_id = f.topic_id)
            WHERE f.reply_to = 0 ORDER BY f.created desc LIMIT 1";
        $cmt = $this->db->query($query);
        if ($cmt !== FALSE && $cmt->num_rows() == 1) {
            $row = $cmt->row();
            return $row->id == $cmt_id;
        }
        return FALSE;
    }
    
    /**
     * Get an ID of the first shape in topics
     * 
     * @param string $c1 - color of my shape
     * @param int $s1 - id of my shape
     * @param string $c2 - shape color
     * @param array $f (optional) - filters
     * @return int
     */
    public function getFirstShapeId($c1, $s1, $c2, $f = array()) {
        $where = "((t.color1 = '{$c1}' and t.shape1 = {$s1} and t.color2 = '{$c2}')";
        $where .= " or (t.color2 = '{$c1}' and t.shape2 = {$s1} and t.color1 = '{$c2}'))";
        $this->db
                ->select("if(t.color1 = '{$c1}' and t.shape1 = {$s1} and t.color2 = '{$c2}', t.shape2, t.shape1) as shapeid", false)
                ->from('topic as t')
                ->where($where, null, false)
                ->order_by('shapeid asc')
                ->limit(1);
        
        if(isset($f['topic'])) {
            $this->db->like("t.title", $f['topic']);
        }
        if(isset($f['country']) || isset($f['state']) || isset($f['city'])) {
            $this->db
                    ->join('forum as f', 'f.topic_id = t.id')
                    ->join('user_profile as up', 'up.user_id = f.created_by');
        }
        if(isset($f['country'])) {
            $this->db->where('up.country', $f['country']);
        }
        if(isset($f['state'])) {
            $this->db->where('up.state', $f['state']);
        }
        if(isset($f['city'])) {
            $this->db->where('up.city', $f['city']);
        }
        
        $res = $this->db->get();
        
        return $res !== FALSE && $res->num_rows() == 1 ? $res->row()->shapeid : 0;
    }

}

/* End of file forum_model.php */
/* Location: ./application/models/forum_model.php */