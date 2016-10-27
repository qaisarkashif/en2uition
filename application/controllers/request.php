<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Request extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('request_model', 'requests');
    }

    public function check_requests() {
        $output = array(
            'requests' => $this->requests->checkRequests($this->user_id)
        );
        $this->echo_json($output);
    }

    public function add_request() {
        $output = array('errors' => '');
        $type = $this->input->post('type', true);
        $to_user = (int)$this->input->post('to_user', true);
        $optional = $this->input->post('optional', true);

        if($type && $to_user){
            $data = array(
                'from_user' => $this->user_id,
                'to_user' => $to_user,
                'type' => $type
            );

            switch ($type) {

                case 'friendship':
                    $this->load->model('friend_model', 'friends');
                    $my_friend = $this->friends->isMyFriend($this->user_id, $to_user);
                    if($my_friend) {
                        $res = true;
                        $output['btn_text'] = lang('friends');
                        $output['btn_unfriend'] = lang('btn_unfriend');
                    } else {
                        $request = $this->requests->getFriendshipRequest($data);
                        if (count($request) == 0) {
                            $data['created'] = date("Y-m-d H:i:s");
                            $res = $this->requests->insertRow($data);
                            if($res) {
                                $this->send_email_notification('request-friendship', $to_user, array('from_user' => $this->authm->get_username()));
                            }
                        } else {
                            $res = true;
                        }
                        if($res) {
                            $output['btn_text'] = lang('friendship_requested');
                        }
                    }
                    break;

                case 'question_privacy':

                    $this->load->model('friend_model', 'friends');

                    $qtype = $optional['qtype'];
                    $lnum = $optional['lnum'];
                    
                    $permissions = $this->users->get_privacy_permissions($this->user_id, array('type' => '"question"', 'from_user' => $to_user));
                    $permissions = isset($permissions['question'][$this->user_id]) ? current($permissions['question'][$this->user_id]) : array();
                    if(count($permissions) > 0) {
                        $arr = $permissions['privacy'];
                        if(isset($arr[$qtype][$lnum])) {
                            foreach($arr[$qtype][$lnum] as $code) {
                                $key = array_search($code, $optional['codes']);
                                if($key !== FALSE) {
                                    unset($optional['codes'][$key]);
                                    $have_access = true;
                                }
                            }
                        }
                    }

                    if(count($optional['codes']) > 0) {
                        $data['optional like'] = "%" . sprintf('"qtype":"%s","lnum":"%s"', $optional['qtype'], $optional['lnum']) . "%";
                        $requests = $this->requests->getRequests($data);
                        unset($data['optional like']);
                        $optional['codes'] = array_values($optional['codes']);
                        if (count($requests) == 0) {
                            $data['created'] = date("Y-m-d H:i:s");
                            $data['optional'] = json_encode($optional);
                            $res = $this->requests->insertRow($data);
                            if($res) {
                                $this->send_email_notification('view-answers-request', $to_user, array('from_user' => $this->authm->get_username()));
                            }
                        } else {
                            $requests = current($requests);
                            $arr = json_decode($requests['optional'], true);
                            $arr['codes'] = array_unique(array_merge($optional['codes'], $arr['codes']));
                            $arr['codes'] = array_values($arr['codes']);
                            $res = $this->requests->updateRow($requests['id'], array('optional' => json_encode($arr)));
                            $already_sent = true;
                        }
                    } else {
                        $res = true;
                    }
                    break;

                default: $res = false;
            }
            if ($res) {
                if (isset($already_sent) && $already_sent) {
                    $output['success_msg'] = lang("success_request_already_sent");
                } elseif (isset($have_access) && $have_access) {
                    $output['success_msg'] = lang("success_already_have_access");
                } else {
                    $output['success_msg'] = lang('success_request_sent');
                }
            } else {
                $output['errors'] = lang('error_request_process');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

    public function response_to_request() {
        $output = array('errors' => '');

        $type = $this->input->post('type', true);
        $action = $this->input->post('action', true);
        $id = (int) $this->input->post('id', true);

        if ($type && $action && $id) {
            $request = $this->requests->getRequestById($id);
            if (!is_null($request)) {
                switch ($type) {

                    case 'friendship':
                        $to_user = $this->user_id == $request->to_user ? $request->from_user : $request->to_user;
                        if ($action == 'accept') {
                            $this->load->model('friend_model', 'friends');
                            $friend = $this->friends->isMyFriend($this->user_id, $to_user);
                            if (!$friend) {
                                $row = array(
                                    'user_id' => $this->user_id,
                                    'friend_id' => $to_user
                                );
                                $res = $this->friends->insertRow($row);
                                if($res) {
                                    $this->send_email_notification('accept-friendship', $to_user, array('from_user' => $this->authm->get_username()));
                                }
                            } else {
                                $res = true;
                            }
                            if ($res) {
                                $res = $this->requests->deleteRow($id);
                            }
                        } else {
                            $res = $this->requests->deleteRow($id);
                        }
                        if($res) {
                            $output['optional'] = array(
                                'btn_text' => $action == 'accept' ? lang('friends') : lang('btn_request_friendship'),
                                'to_user' => $to_user,
                                'action' => $action
                            );
                        }
                        break;

                    case 'question_privacy':
                        if($action == 'accept') {
                            $data = array(
                                'from_user' => $this->user_id,
                                'to_user' => $request->from_user,
                                'type' => "question",
                                'privacy' => ""
                            );
                            $opt = json_decode($request->optional, true);
                            $permissions = $this->users->get_privacy_permissions($request->from_user, array('type' => '"question"', 'from_user' => $this->user_id));
                            $permissions = isset($permissions['question'][$request->from_user]) ? $permissions['question'][$request->from_user] : array();
                            if(count($permissions) > 0) {
                                $pk = key($permissions);
                                $arr = current($permissions);
                                $i = $opt['qtype'];
                                $j = $opt['lnum'];
                                if(!isset($arr['privacy'][$i][$j])) {
                                    $arr['privacy'][$i][$j] = array();
                                }
                                foreach($opt['codes'] as $code) {
                                    if(!in_array($code, $arr['privacy'][$i][$j])) {
                                        $arr['privacy'][$i][$j][] = $code;
                                    }
                                }
                                $res = $this->users->updateRow($pk, array('privacy' => json_encode($arr['privacy'])), 'privacy_permission');
                                if($res) {
                                    $this->send_email_notification('accept-answers-request', $data['to_user'], array('from_user' => $this->authm->get_username()));
                                }
                            } else {
                                $data['privacy'] = json_encode(array($opt['qtype'] => array($opt['lnum'] => $opt['codes'])));
                                $res = $this->users->insertRow($data, 'privacy_permission');
                                if($res) {
                                    $this->send_email_notification('accept-answers-request', $data['to_user'], array('from_user' => $this->authm->get_username()));
                                }
                            }
                            if($res) {
                                $res = $this->requests->deleteRow($id);
                            }
                        } else {
                            $res = $this->requests->deleteRow($id);
                        }
                        break;

                    default:
                        $res = false;

                }
                if (!$res) {
                    $output['errors'] = lang('error_request_process');
                }
            } else {
                $output['errors'] = lang('error_request_not_found');
            }
        } else {
            $output['errors'] = lang('error_empty_input_params');
        }

        $this->echo_json($output);
    }

}

/* End of file request.php */
/* Location: ./application/controllers/request.php */