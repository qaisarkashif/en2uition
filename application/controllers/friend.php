<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Friend extends MY_Controller {

    public function __construct() {
        parent::__construct();        
        $this->data = array(
            "top_menu" => 'friend',
            'inner_navbar' => true,
            'active_page' => 'friends',
            'user' => $this->authm->get_profile()
        );
        $this->tmpl->set_pagetitle('Friends | en2uition');
        $this->tmpl->add_scripts("friends/friend.js");        
        $this->load->model('friend_model', 'friends');
    }

    public function index() {
        $this->load->model('questionnaire_model', 'quiz');
        $stats = $this->quiz->getFriendsStatistic($this->user_id);
        $this->processFriendsStats($stats);
        $data = array(
            'friends_list' => $this->friends->getUserFriends($this->user_id, array('friend')),
            'stats' => $stats
        );
        $this->data['main_content'] = $this->load->view('friend/index', $data, TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }
    
    private function processFriendsStats(&$stats) {
        foreach($stats as $uid => $lvl_stat) {
            foreach($lvl_stat as $type => $stat) {
                if(count($stat) > 0) {
                    $j = $type == 'past' ? 7 : 6;
                    $k = 1;
                    for($i = 1; $i <= $j; $i++) {
                        if(!isset($stats[$uid][$type][$i]) || $stats[$uid][$type][$i] == 0) {
                            $k--;
                            break;
                        }
                        $k++;
                    }
                    $stats[$uid][$type] = $k > 0 ? 'L' . $k : '-';
                } else {
                    $stats[$uid][$type] = '-';
                }
            }
        }
    }

}

/* End of file friend.php */
/* Location: ./application/controllers/friend.php */