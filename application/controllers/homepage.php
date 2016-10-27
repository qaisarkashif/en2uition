<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->lang->load('page');

        $this->tmpl->add_css(array(
            "select2.css",
            "simple-slider.css",
            "magnific-popup.css",
            "imgareaselect-default.css",
            "homepage.css"
        ));

        $this->tmpl->add_scripts(array(
            "simple-slider.js",
            "jquery.knob.js",
            "jquery.updown.js",
            "jquery.magnific-popup.min.js",
            "jquery.mousewheel.min.js",
            "dailymood.js",
            "select2.js",
            "homepage/area-select.js",
            "comments/share-comments.js",
            "comments/comment.js",
            "votes/vote.js",
            "homepage/crop.js",
            "homepage/homepage.js",
            "userguide.js"
        ));

        $this->data = array(
            'top_menu' => 'homepage',
            'header_data' => array(
                'inner_navbar' => true,
                'active_page' => 'homepage'
            )
        );

        $this->load->model('questionnaire_model', 'qm');
        $this->load->model('request_model', 'requests');
        $this->load->model('forum_model', 'forums');

        $user = $this->authm->get_profile();
        $dailymood = $this->authm->get_dailymood();
        $progress_q1 = $this->qm->getOverallProgress('past', $this->user_id);
        $progress_q2 = $this->qm->getOverallProgress('present', $this->user_id);
        $where = array(
            'to_user' => $this->user_id,
            'type' => 'question_privacy'
        );
        $requests = array('past' => 0, 'present' => 0);
        $question_requests = $this->requests->getRequests($where);
        foreach($question_requests as $r) {
            $opt = json_decode($r['optional'], true);
            $requests[$opt['qtype']] += 1;
        }

        $selects = array(
            'username' => 'usernames',
            'country' => 'countries',
            'state' => 'states',
            'city' => 'cities'
        );
        $cur_high_lvl = $this->get_highest_level('present');
        $past_high_lvl = $this->get_highest_level('past');
        $data = array(
            'top_menu' => 'homepage',
            'unviewed_topics' => $this->forums->getUnviewedTopicsCount($this->user_id, $user['color'], $user['shapeid']),
            'progress_q1' => sprintf("%0.2f", $progress_q1),
            'progress_q2' => sprintf("%0.2f", $progress_q2),
            'user' => $user,
            'questionnaire_requests' => $requests,
            'avatar_preview' => $this->authm->get_user_photo('profile', $user),
            'avatar_original' => $this->authm->get_user_photo('orig', $user),
            'shapes' => $this->forums->getAllRows('shape'),
            'users' => $this->search_users(),
            'selects' => $selects,
            'my_id' => $this->user_id,
            'my_cur_relshp_url' => $cur_high_lvl !== false ? site_url("questionnaire/present/level{$cur_high_lvl}/analyze") : 'javascript: void(0);',
            'my_past_relshp_url' => $past_high_lvl !== false ? site_url("questionnaire/past/level{$past_high_lvl}/analyze") : 'javascript: void(0);'
        );
        foreach($selects as $k => $v) {
            ${$v} = $this->authm->select_distinct('user_profile', $k);
            asort(${$v});
            $data[$v] = ${$v};
        }

        $this->data['main_content'] = $this->load->view('homepage', array_merge($data, $dailymood), TRUE);
        $this->tmpl->show_template('page/layout', $this->data);
    }

    public function update_dailymood() {
        $profile_id = $this->authm->get_profile_id();
        $mood = $this->input->post('dm_id', true);
        $this->users->updateRow($profile_id, array('dailymood' => $mood), 'user_profile');
    }

    public function toggle_dailymood() {
        $profile_id = $this->authm->get_profile_id();
        $this->users->toggle_dailymood($profile_id);
    }

    public function search_users() {
        $where = $usersArr = array();
        $shape = (int) $this->input->post('shape', true);
        $page = (int) $this->input->post('page', true);
        $where[] = "u.active = 1";
        if ($shape === -1) {
            $where[] = "up.shape_id IS NULL";
        } elseif ($shape > 0) {
            $where[] = "up.shape_id = " . $shape;
        }
        foreach (array('color', 'country', 'state', 'city', 'username') as $s) {
            ${$s} = $this->input->post($s, true);
            if (${$s} !== FALSE && !empty(${$s})) {
                $where[] = "up.{$s} = '" . ${$s} . "'";
            }
        }
        $users = $this->users->searchUsers(implode(" AND ", $where), $page);
        foreach ($users as $user) {
            $user_foto = $this->authm->get_user_photo('profile', array('user_id' => $user['user_id'], 'profile_image' => $user['profile_image']));
            $usersArr[] = array(
                'uid' => $user['user_id'],
                'foto' => $user_foto,
                'username' => $user['username']
            );
        }
        if(IS_AJAX) {
            $output = array(
                'stop_request' => count($usersArr) < PROSLD_LIMIT,
                'profiles' => $usersArr
            );
            $this->echo_json($output);
        } else {
            return $usersArr;
        }
    }

    public function compare($pre_color = "", $pre_shape = "") {
        $this->data = array(
            'top_menu' => 'compare',
            'header_data' => array(
                'inner_navbar' => true,
                'active_page' => 'compare',
                'user' => $this->authm->get_profile()
            )
        );

        $selects = array(
            'country' => 'countries',
            'state' => 'states',
            'city' => 'cities'
        );

		$data = array(
            'colors' => $this->base->colorArr,
            'shapes' => $this->users->getAllRows('shape'),
            'selects' => $selects,
            'pre_color' => $pre_color,
            'pre_shape' => $pre_shape
        );

        foreach($selects as $k => $v) {
            ${$v} = $this->authm->select_distinct('user_profile', $k);
            asort(${$v});
            $data[$v] = ${$v};
        }

        $this->tmpl->set_pagetitle("Compare | en2uition");
        $this->tmpl->add_scripts("pages/compare.js");
        $this->data['main_content'] = $this->load->view('page/compare', $data, TRUE);
        $this->tmpl->show_template('include/layout', $this->data);
    }

    public function get_compare_data() {
        $filters = $this->input->post('filters', true);
        $chartValues = $this->users->getDailyMoodData($this->user_id, is_array($filters) ? $filters : array());
        $this->echo_json($chartValues);
    }

    public function group_description($my_color = '', $my_shape = '') {
        $this->lang->load('page');
        $color_descr = lang('gd_color_descr');
        $shape_descr = lang('gd_shape_descr');

        if(IS_AJAX) {
            $what = $this->input->post('what', true);
            $name = $this->input->post('name', true);
            if($what == 'color') {
                echo isset($color_descr[$name]) ? $color_descr[$name] : lang('error_incorrect_data');
            } elseif($what == 'shape') {
                echo isset($shape_descr[$name]) ? $shape_descr[$name] : lang('error_incorrect_data');
            } else {
               echo lang('error_incorrect_data');
            }
        } else {
            $this->data = array(
                'top_menu' => 'group_description',
                'header_data' => array(
                    'inner_navbar' => true,
                    'active_page' => 'group_description',
                    'user' => $this->authm->get_profile()
                )
            );

            $data = array(
                'my_color' => strtolower($my_color),
                'my_shape' => strtolower(str_replace('_', '-', $my_shape)),
                'shapes'   => $this->users->getAllRows('shape'),
                'colors'   => $this->base->colorArr
            );
            $cdr = !empty($my_color) ? $my_color : current($data['colors']);
            $data['color_descr'] = $color_descr[$cdr];
            $shdr = !empty($my_shape) ? $my_shape : current($data['shapes']);
            if(is_array($shdr)) {
                $shdr = strtolower(str_replace('_', '-', $shdr['name']));
            }
            $data['shape_descr'] = $shape_descr[$shdr];

            $this->tmpl->set_pagetitle("Group description | en2uition");
            $this->tmpl->add_scripts("pages/group_description.js");
            $this->data['main_content'] = $this->load->view('page/group_description', $data, TRUE);
            $this->tmpl->show_template('include/layout', $this->data);
        }
    }

    public function predictions() {
        show_error(lang('error_under_development'));
        exit;
    }
    
    private function get_highest_level($type) {
        $this->load->model('questionnaire_model', 'quiz');
        $levels = $this->quiz->getLevels($type, $this->user_id);
        $j = $type == 'past' ? 7 : 6;
        for($i = 1; $i <= $j; $i++) {
            if ($levels[$i]['quest_count'] > 0 && $levels[$i]['answered_count'] < $levels[$i]['quest_count']) {
                $i--;
                break;
            }
        }
        $i = $i > $j ? $i-1 : $i;
        return $i > 0 && $i <= $j ? $i : false;
    }

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */