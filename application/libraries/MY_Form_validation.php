<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Form_Validation extends CI_Form_validation {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function get_errors_array() {
        if (count($this->_error_array) === 0)
            return FALSE;
        else
            return $this->_error_array;
    }

}
