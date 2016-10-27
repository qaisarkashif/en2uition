<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Algo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('algorithm_model', 'algorithm');

        if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] != '') {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] != '') {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
        }

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You are not authorized to access this page';
            exit;
        } else {
            $this->config->load('algo');
            if ($_SERVER['PHP_AUTH_USER'] != $this->config->item('get_zero_one_username') || $_SERVER['PHP_AUTH_PW'] != $this->config->item('get_zero_one_password')) {
                $this->load->view('index.html');
            }
        }
    }

    public function get_zero_one($type = 'past', $level = 1, $mode = 'save') {
        $this->lang->load('error');
        $path = "./uploads/";
        $filename = "zero_one.txt";
        $file_handle = fopen($path . $filename, "wb") or die(lang('error_open_file'));
        $this->algorithm->runZeroOne($file_handle, null, $type, $level);
        fclose($file_handle);
        if($mode == 'download') {
            $this->load->helper('download');
            $data = file_get_contents($path . $filename);
            force_download("ZeroOne.txt", $data);
        }
    }

    private function http_digest_parse($txt)
    {
        // protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

}

/* End of file algo.php */
/* Location: ./application/controllers/algo.php */
