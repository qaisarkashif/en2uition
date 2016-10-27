<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Message extends MY_Controller {

	public function __construct() {
		parent::__construct();
        $this->data = array(
            "top_menu" => 'messages',
            'inner_navbar' => true,
            'active_page' => 'messages',
            'user' => $this->authm->get_profile()
        );
        $this->tmpl->set_pagetitle('Messages | en2uition');
        $this->tmpl->add_scripts('messages/message.js');
        $this->load->model('message_model', 'messages');
	}

    public function index() {
        $data = array(
            'messages_list' => $this->messages->getGeneralMessages($this->user_id),
            'black_list' => $this->users->getMyBlackList($this->user_id)
        );
        $this->data['main_content'] = $this->load->view('message/index', $data, TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function send() {
        $output = array('errors' => '');

        $to_user = $this->input->post('to_user', true);
        $msg_text = $this->input->post('msg_text', true);
        $reply_to = $this->input->post('reply_to', true);

        if($to_user && $msg_text) {
            if(!$this->users->in_black_list($to_user, $this->user_id)) {
                $row = array(
                    'from_user' => $this->user_id,
                    'to_user' => $to_user,
                    'reply_to' => $reply_to === false ? NULL : $reply_to,
                    'created' => date("Y-m-d H:i:s"),
                    'msg_text' => nl2br(htmlspecialchars(strip_tags($msg_text)))
                );
                $res = $this->messages->insertRow($row);
                if($res) {
                    $this->send_email_notification('received-message', $to_user, array('from_user' => $this->authm->get_username()));
                    $output['success_text'] = lang('success_send_message');
                    if(!is_null($row['reply_to'])) {
                        $output['message'] = array(
                            'username' => $this->data['user']['username'],
                            'ava' => $this->authm->get_user_photo('forum'),
                            'mid' => $this->db->insert_id(),
                            'txt' => $row['msg_text'],
                            'date' => date('g:i a M j, Y', $this->users->server_to_client_localtime($row['created'])),
                            'from_user' => $this->user_id
                        );
                    }
                } else {
                    $output['errors'] = lang('error_send_message');
                }
            } else {
                $output["errors"] = lang('error_blocked_user');
            }
        } else {
            $output['errors'] = !$msg_text ? lang('error_empty_message') : lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

    public function view_history($msg_id = null) {
        $data = array(
            'messages_list' => $this->messages->getHistoryMessages($msg_id, $this->user_id),
            'uname' => $this->data['user']['username'],
            'uava' => $this->authm->get_user_photo('forum'),
            'uid' => $this->user_id,
            'msg_id' => $msg_id
        );
        if($data['messages_list']) {
            $this->messages->markAsRead($msg_id, true, $this->user_id);
            $this->data['main_content'] = $this->load->view('message/history', $data, TRUE);
            $this->tmpl->show_template('include/layout', $this->data);
        } else {
            show_404();
            exit;
        }
    }

    public function get_unread_number() {
        $output = array(
            'count' => $this->messages->getUnreadMessagesNumber($this->user_id)
        );
        $this->echo_json($output);
    }

    public function mark_as_unread() {
        $output = array("errors" => "");
        $msg_id = $this->input->get('id', true);
        if($msg_id) {
            $res = $this->messages->markAsUnread($msg_id);
            if(!$res) {
                $output['errors'] = lang('error_mark_as_unread');
            }
        } else {
            $output['errors'] = lang('error_empty_input_param');
        }
        $this->echo_json($output);
    }

    public function delete() {
        $output = array("errors" => "");
        $msg_id = $this->input->get('id', true);
        if($msg_id) {
            $res = $this->messages->deleteMessage($msg_id);
            if(!$res) {
                $output['errors'] = lang('error_delete_message');
            }
        } else {
            $output['errors'] = lang('error_empty_input_param');
        }
        $this->echo_json($output);
    }
}

/* End of file message.php */
/* Location: ./application/controllers/message.php */