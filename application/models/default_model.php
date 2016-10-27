<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Default_model extends CI_Model {

    protected $_table = '<table>';
    protected $_pk = 'id';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Insert a new row into the table
     *
     * @param array $row - an array of values to insert
     * @param string $tablename
     * @param boolean $batch - insert multiple rows
     * @return boolean
     */
    public function insertRow($row = array(), $tablename = '', $batch = false) {
        $tablename = $tablename != ''? $tablename : $this->_table;
        if($batch) {
            $res = $this->db->insert_batch($tablename, $row);
        } else {
            $res = $this->db->insert($tablename, $row);
        }
        return $res !== FALSE;
    }

    /**
     * Update a row
     *
     * @param mixed $pkvalue - primary key value
     * @param array $row - update data
     * @param string $tablename
     * @return boolean
     */
    public function updateRow($pkvalue, $row = array(), $tablename = '') {
        $tablename = $tablename != ''? $tablename : $this->_table;
        $res = $this->db
                ->where($this->_pk, $pkvalue)
                ->update($tablename, $row);
        return $res !== FALSE;
    }

    /**
     * Delete a row
     *
     * @param mixed $pkvalue - primary key value
     * @param string $tablename
     * @return boolean
     */
    public function deleteRow($pkvalue, $tablename = '') {
        $tablename = $tablename != ''? $tablename : $this->_table;
        $res = $this->db
                ->where($this->_pk, $pkvalue)
                ->delete($tablename);
        return $res !== FALSE;
    }

    /**
     * Get number of rows in the table
     *
     * @return int
     */
    public function getRowCount($tablename = '') {
        $tablename = $tablename != '' ? $tablename : $this->_table;
        return $this->db->count_all($tablename);
    }

    /**
     * Get all rows from the table
     *
     * @param string $tablename (optional)
     * @param string $where (optional)
     * @return array
     */
    public function getAllRows($tablename = '', $where = '') {
        $tablename = $tablename != '' ? $tablename : $this->_table;
        if(!empty($where)) {
            $this->db->where($where, null, false);
        }
        $res = $this->db->get($tablename);
        return $res !== FALSE ? $res->result_array() : array();
    }

    /**
     * Get distinct values
     *
     * @param string $from_table
     * @param string $field_name
     * @param string $order_by
     * @return array
     */
    public function select_distinct($from_table, $field_name, $order_by = '') {
        $this->db
                ->select("distinct " . $field_name, false)
                ->where("{$field_name} is not null AND {$field_name} <> ''", null, false);
        if(!empty($order_by)) {
            $this->db->order_by($order_by);
        }
        $res = $this->db->get($from_table);
        return $res !== FALSE ? $res->result_array() : array();
    }

    public function server_to_client_localtime($date_str) {
        $client_timezone_offset = $this->input->cookie('timezoneoffset', true) | 0;
        $server_timezone_offset = $this->get_server_timezone_offset();
        $tz_offset = $client_timezone_offset - $server_timezone_offset;

        return strtotime("$tz_offset hours", strtotime($date_str));
    }

    private function get_server_timezone_offset() {
        $this_tz_str = date_default_timezone_get();
        $this_tz = new DateTimeZone($this_tz_str);
        $now = new DateTime("now", $this_tz);
        return $this_tz->getOffset($now)/60/60;

    }

}

/* End of file default_model.php */
/* Location: ./application/models/default_model.php */