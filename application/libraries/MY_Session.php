<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once BASEPATH . '/libraries/Session.php';

class MY_Session extends CI_Session {
    
    public function __construct() {
        parent::__construct();
        $this->CI->session = $this;
    }

    public function sess_update() {
        // Do NOT update an existing session on AJAX calls.
        if (!$this->CI->input->is_ajax_request())
            return parent::sess_update();
    }

}
