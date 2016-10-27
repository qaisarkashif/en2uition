<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Neighborhood extends MY_Controller {

    private $my_color = null;
    private $my_shape_id = null;
    private $my_shape_name = null;

	public function __construct() {
		parent::__construct();

        $this->load->model('forum_model', 'forums');

        $this->data = array(
            "top_menu" => 'neighborhood',
            'inner_navbar' => true,
            'active_page' => 'neighborhood',
            'user' => $this->authm->get_profile()
        );

        if(!IS_AJAX) {
            $this->tmpl->add_scripts('neighborhood/neighborhood.js');
        }

        $this->my_color = $this->data['user']['color'];
        $this->my_shape_id = $this->data['user']['shapeid'];
        $this->my_shape_name = $this->data['user']['shapename'];
        $this->tmpl->set_pagetitle('Neighborhood | en2uition');
        $this->check_access();
	}

    public function index($color = 'green', $pre_shape = '') { 
        if(in_array($color, $this->base->colorArr)) {
            $filters = array();
            $pag_page = (int) $this->input->post('pag_page', true);
            $shape_id = (int) $this->input->post('shape_id', true);
            foreach(array('topic', 'country', 'state', 'city') as $f) {
                $v = $this->input->post($f, true);
                if($v) $filters[$f] = $v;
            }
            if($shape_id == 0) {
                if(!empty($pre_shape)) {
                    $_shape = $this->users->getShapeByName($pre_shape);
                    if($_shape) {
                        $shape_id = $_shape->id;
                    }
                } else {
                    $shape_id = $this->forums->getFirstShapeId($this->my_color, $this->my_shape_id, $color, $filters);
                }
            }
            if($shape_id !== 0) {
                $participants = $this->forums->getTopicParticipants($this->my_color, $this->my_shape_id, $color, $shape_id, $filters, $pag_page, NEIGHB_INDEX_LIMIT, true);
            } else {
                $participants = array();
            }
            $prt_count = count($participants);
            $stop_request = $prt_count < NEIGHB_INDEX_LIMIT;
            $unviewed_topics = $this->forums->getUnviewedTopicsCount($this->user_id, $this->my_color, $this->my_shape_id, $color, $shape_id > 0 ? $shape_id : null);
            
            $data = array(
                'participants' => $participants,
                'stop_request' => $stop_request,
                'prt_count' => $prt_count,
                'unviewed_ids' => isset($unviewed_topics['ids']) ? $unviewed_topics['ids'] : array()
            );
            
            if(IS_AJAX) {
                $this->echo_json($data);
            } else {
                
                $data['top_menu'] = $color;
                $data['shapes'] = $this->forums->getAllRows('shape');
                $data['unviewed_topics'] = $unviewed_topics;
                $data['my_color'] = $this->my_color;
                $data['my_shape_name'] = $this->my_shape_name;
                $data['pre_shape'] = $pre_shape;
                $data['cur_shapeid'] = $shape_id;
                
                $country = $this->forums->select_distinct('user_profile', 'country', 'country asc');
                $state = $this->forums->select_distinct('user_profile', 'state', 'state asc');
                $city = $this->forums->select_distinct('user_profile', 'city', 'city asc');
                
                $this->data['header_data'] = array(
                    'top_menu' => $color,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'input_topic_val' => isset($filters['topic']) ? $filters['topic'] : '',
                    'cur_country' => isset($filters['country']) ? $filters['country'] : '',
                    'cur_state' => isset($filters['state']) ? $filters['state'] : '',
                    'cur_city' => isset($filters['city']) ? $filters['city'] : ''
                );
                
                $this->data['main_content'] = $this->load->view('neighborhood/index', $data, TRUE);
                $this->tmpl->add_scripts("forum/neighborhood.js");
                $this->tmpl->show_template('include/layout', $this->data);
            }
        } else {
            $this->exit_404();
        }
    }

    public function by_username($username = '') {
        $username = urldecode($username);
        $user = $this->users->get_user_profile($username, 4);
        if(!$user) {
            $this->exit_404();
        }
        $user = current($user);
        $data = array(
            'topics' => $this->forums->getTopicsByUsername($this->my_color, $this->my_shape_id, $username),
            'my_color' => $this->my_color,
            'my_shape' => $this->my_shape_id
        );
        $this->data['header_data'] = array(
            'search_by' => "username",
            'search_text' => $username,
            'ava' => $this->authm->get_user_photo('forum', $user),
            'is_visitor' => $this->user_id != $user['user_id'],
            'uid' => $user['user_id']
        );
        $this->data['main_content'] = $this->load->view('neighborhood/by_username', $data, TRUE);
        $this->tmpl->add_scripts(array(
            "jquery-ui.min.js",
            "forum/username.js"
        ));
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function by_username_ajax() {
        if(IS_AJAX) {
            $username = $this->input->post('username', true);
            $last_id = (int) $this->input->post('last_id', true);
            if($username) {
                $topics = $this->forums->getTopicsByUsername($this->my_color, $this->my_shape_id, $username, $last_id);
            } else {
                $topics = array();
            }
            $output = array(
                'rows' => $topics,
                'stop_request' => count($topics) < NEIGHB_USERNAME_LIMIT
            );
            $this->echo_json($output);
        }
    }

    public function by_date($date = '', $color = '', $shape = '', $show_my_topics = false) {
        $_shape = $this->users->getShapeByName($shape);
        if(!in_array($color, $this->base->colorArr) || !$_shape || !$this->check_date($date)) {
            $this->exit_404();
        }
        $pag_page = (int) $this->input->post('pag_page', true);
        $filters = array();
        if($date) {
            $filters['lastpost_date'] = $date;
        }
        if($show_my_topics) {
            $filters['uid'] = $this->user_id;
        }
        if(IS_AJAX) {
            $topic_title = $this->input->post('topic', true);
            if($topic_title) {
                $filters['topic_title'] = $topic_title;
            }
        }
        $rows = $this->forums->getTopicParticipants($this->my_color, $this->my_shape_id, $color, $_shape->id, $filters, $pag_page, NEIGHB_LASTPOST_LIMIT);
        $data = array(
            'rows' => $rows,
            'my_color' => $this->my_color,
            'my_shape_id' => $this->my_shape_id,
            'my_shape' => $this->my_shape_name,
            'color2' => $color,
            'shape2' => $shape,
            'shape2_id' => $_shape->id
        );
        if(IS_AJAX) {
            $c = 0;
            foreach($rows as $key => $arr)
                $c += count($arr);
            $data['stop_request'] = $c < NEIGHB_LASTPOST_LIMIT;
            $this->echo_json($data);
        } else {
            $this->data['header_data'] = array(
                'search_by' => "date",
                'search_text' => $date,
                'input_topic_val' => isset($topic_title) && $topic_title ? $topic_title : "",
                'shape' => $shape,
                'top_menu' => $color,
                'dates' => $this->forums->getAvailablePostDate($this->my_color, $this->my_shape_id, $color, $_shape->id)
            );
            $this->tmpl->add_scripts("forum/filter_by_date.js");
            $this->data['main_content'] = $this->load->view('neighborhood/by_date', $data, TRUE);
            $this->tmpl->show_template('include/layout', $this->data);
        }
    }

    public function topic($id, $color, $shape) {
        $_shape = $this->users->getShapeByName($shape);
        if(in_array($color, $this->base->colorArr) && $_shape) {
            $topic = $this->forums->getTopics($this->my_color, $this->my_shape_id, $color, $_shape->id, $id);
            if(!$topic) {
                $this->exit_404();
            }

            //update log
            $this->forums->updateUserTopicsLog($topic->id, $this->user_id);

            //delete notifications
            $this->load->model('notification_model', 'notifications');
            $this->notifications->deleteNotifications($this->user_id, $topic->id, array('topic_comment', 'topic_comment_vote'));

            $comments = $this->forums->getCommentsToTopic($topic->id, $this->user_id);
            $data = array(
                'color' => $color,
                'shape' => $shape,
                'user' => $this->data['user'],
                'topic_title' => $topic->title,
                'topic_id' => $topic->id,
                'comments' => $comments,
                'ava' => $this->authm->get_user_photo('forum', $this->data['user'])
            );
            $this->data['header_data'] = array(
                'top_menu' => $color,
                'shape' => $shape
            );
            $this->data['active_page'] = "topic";
            $this->data['main_content'] = $this->load->view('neighborhood/topic', $data, TRUE);
            $this->tmpl->set_pagetitle("Topic | en2uition");
            $this->tmpl->add_css("wysiwyg-editor.css");
            $this->tmpl->add_scripts(array(
                "jquery.mousewheel.min.js",
                "wysiwyg.js",
                "wysiwyg-editor.js",
                "we-ini.js",
                "comments/share-comments.js",
                "votes/vote.js",
                "forum/topic.js"
            ));
            $this->tmpl->show_template('include/layout', $this->data);
        } else {
            $this->exit_404();
        }
    }

    public function new_topic($color, $shape) {
        if(in_array($color, $this->base->colorArr)) {
            $data = array(
                'color' => $color,
                'shape' => $shape,
                'referrer' => $this->agent->referrer(),
                'user' => $this->data['user'],
                'new_topic' => true,
                'ava' => $this->authm->get_user_photo('forum', $this->data['user'])
            );
            $this->data['header_data'] = array(
                'top_menu' => $color,
                'shape' => $shape
            );
            $this->data['active_page'] = "new_topic";
            $this->data['main_content'] = $this->load->view('neighborhood/topic', $data, TRUE);
            $this->tmpl->set_pagetitle("New topic | en2uition");
            $this->tmpl->add_css("wysiwyg-editor.css");
            $this->tmpl->add_scripts(array(
                "jquery.mousewheel.min.js",
                "wysiwyg.js",
                "wysiwyg-editor.js",
                "we-ini.js",
                "forum/topic.js"
            ));
            $this->tmpl->show_template('include/layout', $this->data);
        } else {
            $this->exit_404();
        }
    }

    public function add_new_topic() {
        $output = array('errors' => '');

        $title = $this->input->post('title', true);
        $text = $this->input->post('text', false);
        $color2 = $this->input->post('color', true);
        $shape2 = $this->input->post('shape', true);
        if($title && $color2 && $shape2 & $text) {
            $res = false;
            $shape2 = $this->users->getShapeByName($shape2);
            if($shape2) {
                $created = date("Y-m-d H:i:s");
                $row = array(
                    'color1' => $this->my_color,
                    'shape1' => $this->my_shape_id,
                    'color2' => $color2,
                    'shape2' => $shape2->id,
                    'created_datetime' => $created,
                    'created_by' => $this->user_id,
                    'title' => $title
                );
                $res = $this->forums->insertRow($row, 'topic');
                if($res) {
                    $topic_id = $this->db->insert_id();
                    $row = array(
                        'topic_id' => $topic_id,
                        'reply_to' => 0,
                        'created_by' => $this->user_id,
                        'created' => $created,
                        'txt' => htmlspecialchars($text)
                    );
                    $res = $this->forums->insertRow($row);
                    if($res) {
                        $output['url'] = sprintf('/forum/topic-%s/%s/%s', $topic_id, $color2, $shape2->name);
                    }
                }
            }
            if(!$res) {
                $output['errors'] = lang('error_add_new_topic');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        $this->echo_json($output);
    }

    public function comment_topic() {
        $output = array('errors' => '');

        $topic_id = (int) $this->input->post('topic_id', true);
        $reply_to = (int) $this->input->post('reply_to', true);
        $text = $this->input->post('text', false);

        if($topic_id > 0 && $text) {
            $created = date("Y-m-d H:i:s");
            $row = array(
                'topic_id' => $topic_id,
                'reply_to' => $reply_to,
                'created_by' => $this->user_id,
                'created' => $created,
                'txt' => htmlspecialchars($text)
            );
            $res = $this->forums->insertRow($row);
            if($res) {
                $created = $this->forums->server_to_client_localtime($created);
                $new_id = $this->db->insert_id();
                $output['comment'] = array(
                    'id' => $new_id,
                    'date' => date("M j, Y", $created),
                    'time' => date("g:ia", $created),
                    'short_text' => $this->getShortText($text)
                );

                //add notification
                if($reply_to > 0) {
                    $prev_cmt = $this->forums->getCommentById($reply_to);
                } else {
                    $prev_cmt = $this->forums->getPreviousComment($topic_id, $new_id);
                }
                if($prev_cmt && $prev_cmt->created_by !== $this->user_id) {
                    $this->sendNotification($prev_cmt->created_by, $new_id);
                    $this->send_email_notification('reply-to-comment', $prev_cmt->created_by, array('from_user' => $this->authm->get_username()));
                }
            } else {
                $output['errors'] = lang('error_to_comment_topic');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        $this->echo_json($output);
    }

    public function delete_comment() {
        $output = array(
            'errors' => '',
            'topic_removed' => 'no'
        );
        $id = (int) $this->input->post('id', true);
        $undelete = $this->input->post('undelete', true);
        if($id > 0) {
            if($undelete == 'yes') {
                $res = $this->forums->updateRow($id, array('deleted' => 0));
                if($res) {
                    $cmt = $this->forums->getCommentById($id);
                    $output['comment'] = array(
                        'short_text' => $this->getShortText($cmt->txt),
                        'full_text' => html_entity_decode($cmt->txt)
                    );
                } else {
                    $output['errors'] = lang('error_undelete_post');
                }
            } else {
                $del_topic_id = $this->forums->needDeleteTopic($this->user_id, $id);
                if($del_topic_id !== FALSE) {
                    $res = $this->forums->deleteTopic($del_topic_id);
                    if($res) {
                        $output['topic_removed'] = 'yes';
                    } else {
                        $output['errors'] = lang('error_topic_delete');
                    }
                } else {
                    $last_comment = $this->forums->isLastCommentInLeftColumn($id);
                    $res = $this->forums->deleteComment($this->user_id, $id, $last_comment);
                    if($res) {
                        if($last_comment) {
                            $output['full_del'] = 'yes';
                        } else {
                            $output['del_text'] = lang('error_comment_deleted_by_user');
                        }
                    } else {
                        $output['errors'] = lang('error_comment_delete');
                    }
                }
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }
        $this->echo_json($output);
    }

    public function load_replies() {
        $id = (int) $this->input->get('id', true);
        $replies = $this->forums->getReplies($id, $this->user_id);
        $output = array(
            'replies' => $replies,
            'length'  => count($replies),
            'del_text' => lang('error_comment_deleted_by_user')
        );
        $this->echo_json($output);
    }

    public function get_usernames() {
        $term = $this->input->get('term', true);
        $names = $this->users->searchUsernames($term);
        $this->echo_json($names);
    }

    private function check_access() {
        if(empty($this->my_color) || empty($this->my_shape_id)) {
            $this->lang->load('core');
            show_error(lang("neighborhood_tooltip_no_shape"));
            exit;
        }
    }

    private function check_date($date) {
        $tmp = explode('.', $date);
        if(count($tmp) == 3) {
            if($tmp[1][0] == '0') {
                $tmp[1] = $tmp[1][1];
            }
            if($tmp[2][0] == '0') {
                $tmp[2] = $tmp[2][1];
            }
            return checkdate($tmp[1], $tmp[2], $tmp[0]);
        }
        return FALSE;
    }

    private function sendNotification($to_user, $cmt_id) {
        $this->load->model('notification_model', 'notifications');
        $row = array(
            'from_user' => $this->user_id,
            'type' => "topic_comment",
            'to_user' => $to_user,
            'created' => date("Y-m-d H:i:s"),
            'optional' => $cmt_id
        );
        $this->notifications->insertRow($row);
    }

    private function getShortText($text) {
        $short_text = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), " ", $text));
        if (strlen($short_text) > 150) {
            $short_text = substr($short_text, 0, 150);
        } else {
            $short_text = "";
        }
        return $short_text;
    }

}

/* End of file neighborhood.php */
/* Location: ./application/controllers/neighborhood.php */