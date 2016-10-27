<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("auth_model", "authm");
    }

    public function index() {
        $this->load->library('Tmpl');
        $this->tmpl->add_scripts(array("home.js", "video.js"));
        if(!$this->authm->is_logged_in()) {
            $this->tmpl->add_scripts("auth.js");
        }
        $data = array(
            "header_data" => array(
                "top_menu" => "home",
                "active_page" => "home"
            ),
            "main_content" => $this->load->view('home', array('top_menu' => "home"), TRUE)
        );
        $this->tmpl->show_template('page/layout', $data);
    }
    
    public function send_feedback() {
        $output = array('errors' => '');
        $feedback = (string) $this->input->post('feedback', true);
        if($feedback) {
            $data = array('feedback' => nl2br($feedback));
            $data['site_name'] = $website_name = $this->config->item('website_name');
            $webmaster_email = $this->config->item('webmaster_email');
            $this->load->library('email');
            $this->email->from($webmaster_email, $website_name);
            $this->email->reply_to($webmaster_email, $website_name);
            $this->email->to($webmaster_email);
            $this->email->subject(sprintf(lang('contact_us_email_subject'), $website_name));
            $this->email->message($this->load->view('email/contact-us-html', $data, TRUE));
            $this->email->set_alt_message($this->load->view('email/contact-us-txt', $data, TRUE));
            if(!$this->email->send()) {
                $output['errors'] = lang('error_not_send_email');
            }
        } else {
            $output['errors'] = lang('error_empty_feedback');
        }
        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */