<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shape_model extends Default_model {
    
    public function __construct() {
        parent::__construct();
        $this->_table = 'shape';
        $this->_pk = 'id';
    }
    
}