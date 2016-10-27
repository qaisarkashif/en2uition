<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Page extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load("page", !isset($this->session->userdata['language']) ? "english" : $this->session->userdata['language']);
        $this->load->library('Tmpl');
        $this->tmpl->add_scripts(array(
            "pages/send_contact_us.js",
            "pages/page.js"
        ));
    }

    public function index() {
        $data = array(
            "top_menu" => 'home',
            "main_content" => $this->load->view('page/index', '', TRUE)
        );
        $this->tmpl->show_template('page/layout', $data);
    }

    public function aboutus() {
        $data = array(
            "top_menu" => 'aboutus',
            "main_content" => $this->load->view('page/about-us', '', TRUE)
        );
        $this->tmpl->set_pagetitle('About Us | en2uition');
        $this->tmpl->show_template('page/layout', $data);
    }

    public function concept() {
        $data = array(
            "top_menu" => 'concept',
            "main_content" => $this->load->view('page/concept', '', TRUE)
        );
        $this->tmpl->set_pagetitle('Concept | en2uition');
        $this->tmpl->show_template('page/layout', $data);
    }

    public function features() {
        $data = array(
            "top_menu" => 'features',
            "main_content" => $this->load->view('page/features', '', TRUE)
        );
        $this->tmpl->set_pagetitle('Features | en2uition');        
        $this->tmpl->show_template('page/layout', $data);
    }

    public function faq() {
        $data = array(
            "top_menu" => 'faq',
            "main_content" => $this->load->view('page/faq', '', TRUE)
        );
        $this->tmpl->set_pagetitle('FAQ | en2uition');        
        $this->tmpl->show_template('page/layout', $data);
    }

    public function contactus() {
        $data = array(
            "top_menu" => 'contactus',
            "main_content" => $this->load->view('page/contactus', '', TRUE)
        );
        $this->tmpl->set_pagetitle('Contact Us | en2uition');        
        $this->tmpl->show_template('page/layout', $data);
    }

    public function testimonials() {
        $data = array(
            "top_menu" => 'testimonials',
            "main_content" => $this->load->view('page/testimonials', '', TRUE)
        );
        $this->tmpl->set_pagetitle('Testimonials | en2uition');        
        $this->tmpl->show_template('page/layout', $data);
    }

}

/* End of file page.php */
/* Location: ./application/controllers/page.php */