<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Language extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        
    }

    public function set_language($lang = 'english') {
        $cookie = array(
            'name' => 'site_lang',
            'value' => $lang,
            'expire' => 365 * 24 * 60 * 60,
            'prefix' => 'en2_',
        );

        set_cookie($cookie);
        
        $this->load->model(array("auth_model", "user_model"));
        
        if($this->auth_model->is_logged_in()) {
            $this->session->set_userdata('language', $lang);
            $this->user_model->updateRow($this->auth_model->get_user_id(), array('language' => $lang));
        }
        
        redirect($this->agent->referrer());
        exit;
    }

}

/* End of file language.php */
/* Location: ./application/controllers/language.php */