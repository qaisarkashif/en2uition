<?php

class Tmpl {

    private $_scripts = array(
        "all_site/ajax.js"
    );
    private $_css = array(
        "font-awesome.css",
        "shapes.css",
        "jquery.bxslider.css",
        "tipped.css"
    );
    private $_pagetitle;

    public function __construct() {
        
    }

    public function set_pagetitle($title = '') {
        $this->_pagetitle = $title;
    }

    public function add_css($css) {
        if (is_array($css)) {
            $this->_css = array_merge($this->_css, $css);
        } elseif (is_string($css)) {
            $this->_css[] = $css;
        }
    }

    public function add_scripts($js) {
        if (is_array($js)) {
            $this->_scripts = array_merge($this->_scripts, $js);
        } elseif (is_string($js)) {
            $this->_scripts[] = $js;
        }
    }

    public function show_template($layout, $data) {
        $ci = &get_instance();

        $ldata = array(
            "pagetitle" => $this->_pagetitle,
            "additional_css" => $this->_css,
            "additional_js" => $this->_scripts
        );
        $ldata = array_merge($data, $ldata);

        $ci->load->view('include/head', $ldata);
        $ci->load->view($layout, $ldata);
        $ci->load->view('include/foot', $ldata);
    }

}
