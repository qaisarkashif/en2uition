<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Base {

    public $educationArr = array(
        "middle school" => "middle school",
        "high school" => "high school",
        "some college" => "some college",
        "associates" => "associates",
        "bachelors" => "bachelors",
        "master" => "master",
        "doctorate" => "doctorate",
    );
    public $genderArr = array(
        "female" => "female",
        "male" => "male"
    );
    public $orientationArr = array(
        "straight" => "straight",
        "gay" => "gay",
        "bisexual" => "bisexual",
        "not sure" => "not sure"
    );
    public $relationshipArr = array(
        "serious" => "In a serious relationship",
        "casual" => "In a casual relationship",
        "single fun" => "Single and having fun",
        "single bored" => "Single and being bored",
    );
    public $colorArr = array(
        "red", 
        "yellow", 
        "green", 
        "blue"
    );
    public $quizTypesArr = array(
        'past', 
        'present'
    );
    public $privacyCodesArr = array(
        "low", 
        "medium", 
        "high"
    );
    
    private $ci;
    
    public function __construct() {
        $this->ci = &get_instance();
    }
    
    /**
     * Get shape color by relationship status
     * 
     * @param string $relationship - Relationship status
     * @return string or boolean (return FALSE if $relationship is not exist)
     */
    public function get_color($relationship) {
        switch($relationship) {
            case "serious"      : $color = 'red'; break;
            case "casual"       : $color = 'yellow'; break;
            case "single fun"   : $color = 'green'; break;
            case "single bored" : $color = 'blue'; break;
            default             : $color = FALSE; break;
        }
        return $color;
    }
    
    /**
     * Get relationship status by shape color
     * 
     * @param string $color - shape color
     * @return string or boolean (return FALSE if $color is not exist)
     */
    public function get_relationship($color) {
        switch ($color) {
            case "red"      : $relsh = "serious"; break;
            case "yellow"   : $relsh = "casual"; break;
            case "green"    : $relsh = "single fun"; break;
            case "blue"     : $relsh = "single bored"; break;
            default         : $relsh = FALSE; break;
        }
        return $relsh;
    }

    public function load_lang_file($filename = '') {
        if (!empty($filename)) {
            $lang = get_cookie('en2_site_lang', true);
            if ($lang === FALSE && isset($this->ci->session->userdata['language'])) {
                $lang = $this->ci->session->userdata['language'];
            } else {
                $lang = "english";
            }
            $this->ci->lang->load($filename, $lang);
        }
    }

}
