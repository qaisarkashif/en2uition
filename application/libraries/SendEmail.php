<?php

if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class Sendemail {

    private $_ci;
    private $_email;
    private $_site_name = '';
    private $_webmaster = '';

    public function __construct() {
        $this->_ci = &get_instance();
        $this->_ci->load->library('email');
        $this->_email = $this->_ci->email;
        $this->_site_name = $this->_ci->config->item('website_name');
        $this->_webmaster = $this->_ci->config->item('webmaster_email');
    }

    public function send($type, $email, $data = array()) {
        // if running locally, don't send email
        if ($_SERVER['HTTP_HOST'] == 'en2uition')
            return true;

        $data['site_name'] = $this->_site_name;
        $data['site_url'] = base_url();
        $this->_email->from($this->_webmaster, $this->_site_name);
        $this->_email->reply_to($this->_webmaster, $this->_site_name);
        $this->_email->to($email);
        $this->_email->subject(sprintf($this->_ci->lang->line('auth_subject_' . $type), $this->_site_name));
        $this->_email->message($this->_ci->load->view('email/' . $type . '-html', $data, TRUE));
        $this->_email->set_alt_message($this->_ci->load->view('email/' . $type . '-txt', $data, TRUE));
        return $this->_email->send();
    }

}
