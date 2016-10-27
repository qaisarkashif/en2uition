<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Request_model extends Default_model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'request';
        $this->_pk = 'id';
    }

    /**
     * Get a list of requests
     *
     * @param int $user_id
     * @return array
     */
    public function checkRequests($user_id) {
        $requests = array();

        $res = $this->db
                ->select('r.*, f.username, f.profile_image')
                ->from($this->_table . ' as r')
                ->join('user_profile as f', 'f.user_id = r.from_user')
                ->where('r.to_user', $user_id)
                ->order_by('r.created', 'desc')
                ->get();

        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                if(!isset($requests[$row['type']])) {
                    $requests[$row['type']] = array();
                }
                $row['when'] = date('\a\t g:ia M j, Y', $this->server_to_client_localtime($row['created']));
                $tmp_inf = array(
                    'user_id' => $row['from_user'],
                    'profile_image' => $row['profile_image']
                );
                $row['ava'] = $this->authm->get_user_photo('forum', $tmp_inf);
                $requests[$row['type']][] = $row;
            }
        }

        return $requests;
    }

    /**
     * Get requests
     *
     * @param array $where - search terms
     * @param int $limit
     * @return array
     */
    public function getRequests($where = array(), $limit = null) {
        if(!empty($limit)) {
            $this->db->limit($limit);
        }
        $requests = $this->db->where($where)->get($this->_table);
        return $requests !== FALSE ? $requests->result_array() : array();
    }

    /**
     * Get friendship request
     *
     * @param array $data
     * @return array
     */
    public function getFriendshipRequest($data) {
        $where = "((from_user = " . $data['from_user'] . " AND to_user = " . $data['to_user'] . ") OR (from_user = " . $data['to_user'] . " AND to_user = " . $data['from_user'] . "))";
        $request = $this->db
                ->where("type", $data['type'])
                ->where($where, null, false)
                ->limit(1)
                ->get($this->_table);
        return $request !== FALSE ? $request->result_array() : array();
    }

    /**
     * Get request by his id
     *
     * @param int $id
     * @return null (if not found) or object
     */
    public function getRequestById($id) {
        $request = $this->db
                ->where($this->_pk, $id)
                ->limit(1)
                ->get($this->_table);
        if ($request !== FALSE && $request->num_rows() == 1) {
            return $request->row();
        }
        return NULL;
    }

}

/* End of file request_model.php */
/* Location: ./application/models/request_model.php */