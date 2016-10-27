<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Outcome extends MY_Controller {

    private $type = 'past';
    public $_period = array(
        1 => 'early_winter',
        2 => 'late_winter',
        3 => 'early_spring',
        4 => 'late_spring',
        5 => 'early_summer',
        6 => 'late_summer',
        7 => 'early_fall',
        8 => 'late_fall'
    );
    
    public function __construct() {
        parent::__construct();
        $this->lang->load('outcome');
        $this->lang->load('question');
        $this->load->model('answer_model', 'answers');
    }

    public function index() {
        $this->load->model('questionnaire_model', 'quiz');
        $this->load->model('question_model', 'questions');
        $this->data = array(
            'q_type' => $this->type,
            'levels' => $this->quiz->getLevels($this->type, $this->user_id),
            'is_outcome' => true,
            'cur_lvl' => 7,
            'header_data' => array(
                'pagetitle' => "Outcome | en2uition",
                'is_joined' => $this->authm->is_joined()
            ),
            'questions' => $this->questions->getOutcomeQuestions($this->user_id),
            'period' => $this->_period,
            'answers' => $this->answers->getOutcomeAnswers($this->user_id)
        );
        $this->load->view('questionnaire/outcome', $this->data);
    }

}

/* End of file outcome.php */
/* Location: ./application/controllers/outcome.php */