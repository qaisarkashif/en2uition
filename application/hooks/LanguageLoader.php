<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class LanguageLoader {

    public function initialize($params) {
        $ci = & get_instance();
        $ci->load->helper(array('language', 'cookie'));

        $site_lang = get_cookie('en2_site_lang', true);
        if ($site_lang === FALSE) {
            if (isset($ci->session->userdata['language'])) {
                $site_lang = $ci->session->userdata['language'];
            } else {
                $site_lang = "english";
            }
        }

        if (isset($params["lang_files"])) {
            foreach ($params['lang_files'] as $key => $filename)
                $ci->lang->load($filename, $site_lang);
        }

        $ci->config->set_item('language', $site_lang);
    }

}
