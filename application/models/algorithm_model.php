<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Algorithm_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper('questions');
        $this->load->helper('math');
        $this->lang->load('question');
        $this->lang->load('questionpast');
        $this->lang->load('questionpresent');
    }

    public function runZeroOne($file_handle = null, $user_id = null, $type = 'past', $level = 1) {
        if(is_null($file_handle))
        {
            $level_id = $this->db
                ->where('type', $type)
                ->where('level', $level)
                ->get('q_level')
                ->row()->id;
        }
        $levels = array();
        for($i = 1; $i <= $level; $i++)
            $levels[] = $i;
        $lstr = implode(',', $levels);
        $lcount = count($levels);
        $join = "
            SELECT user_id, COUNT(id) AS lcount 
            FROM 
            (
                SELECT l.id, a.user_id, COUNT(a.id) AS ac, ls.qc
                FROM q_level AS l
                JOIN 
                (
                    SELECT lvl.id, COUNT(q_question.id) AS qc
                    FROM q_level AS lvl
                    LEFT JOIN q_question ON (q_question.level_id = lvl.id)
                    WHERE lvl.type = '{$type}' AND lvl.level IN ({$lstr})
                    GROUP BY lvl.id
                ) AS ls ON (ls.id = l.id)
                LEFT JOIN q_question AS q ON q.level_id = l.id
                LEFT JOIN q_answer AS a ON (a.question_id = q.id and a.me_status = 1 and a.partner_status = 1)
                GROUP BY l.id, a.user_id
                HAVING ac = qc AND qc <> 0
            ) tmp
            GROUP BY user_id
            HAVING lcount = {$lcount}
        ";
            
        $this->db
                ->select("u.id AS uid, q.label, q.quest_type_id AS qtype, q.optional, a.me, a.partner", false)
                ->from("user AS u, q_question AS q")
                ->join('q_answer AS a', 'a.user_id = u.id AND q.id = a.question_id AND a.me_status = 1 AND a.partner_status = 1', 'left')
                ->join("({$join}) AS lr", 'lr.user_id = u.id')
                ->join('q_level as l', 'q.level_id = l.id')
                ->where('l.type', $type)
                ->where_in('l.level', $levels)
                ->order_by("u.id, q.level_id, q.qnum");

        if($user_id)
            $this->db->where('u.id', $user_id);
                
        $answers = $this->db->get();
        
        if ($answers !== FALSE) {
            $cur_uid = null;
            $conv_answ = '';
            foreach ($answers->result_array() as $answer) {
                extract($answer);
                if (!is_null($cur_uid) && $cur_uid != $uid) {
                    if($file_handle)
                        fwrite($file_handle, "\n");
                    elseif(!empty($conv_answ)) {
                        self::saveConvertedStringToDB($user_id, $level_id, $conv_answ);
                        $conv_answ = '';
                    }
                }
                $cur_uid = $uid;
                if($qtype == 22 && $optional == 'main') {
                    $label = "main_outcome";
                }
                $txt = $this->getZOStr($qtype, $label, $optional, $me, $partner);
                if ($txt != "") {
                    $txt .= ";\t";
                    $conv_answ .= $txt;
                }
                if($file_handle)
                    fwrite($file_handle, $txt);
            }
            if(is_null($file_handle) && !empty($conv_answ)) {
                self::saveConvertedStringToDB($user_id, $level_id, $conv_answ);
            }
            $answers->free_result();
        }
    }

    /**
     * Convert answer to zero/one string
     * 
     * @param int $qtype - question type
     * @param string $label - question label
     * @param string $opt - question optional
     * @param string $me_answ - my answer
     * @param string $partner_answ - partner's answer
     * @return string
     */
    private function getZOStr($qtype, $label, $opt, $me_answ, $partner_answ) {
        $res_str = "";
        if($label == "main_outcome") {
            $j = 1;
        } else {
            $mode = get_question_mode($opt);
            $has_subq = has_subquestion($opt);
            $j = $mode == 'single' ? 1 : 2;
            $j *= $has_subq ? 2 : 1;
        }
        $answ_arr = $this->processAnswers($qtype, $label, $me_answ, $partner_answ);
        $Arr = array();
        switch ($qtype) {

            case 1:
                $def = array("scr_min" => 0, "scr_max" => 120);
                foreach (array("scr_min", "scr_max") as $var) {
                    ${$var} = preg_match("/{$var}=([0-9]+)/i", $opt, $matches) ? $matches[1] : $def[$var];
                }
                $vc = abs($scr_max - $scr_min) + 1;
                $last_opt = $custom_options = null;
                if (preg_match('/last_opt=([^\|]+)/', $opt, $matches)) {
                    $vc++;
                    $last_opt = $matches[1];
                }
                if(preg_match('/custom_options/', $opt)) {
                    $custom_options = lang("{$label}_options");
                    $vc = count($custom_options);
                }
                if($label == 'l1q21' || $label == 'l1q23') {
                    $custom_options = array_keys($custom_options);
                }
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        if($custom_options) {
                            $pos = array_search($answ_arr[$i], $custom_options);
                        } else {
                            $pos = $last_opt == $answ_arr[$i] ? $vc - 1 : $answ_arr[$i] - $scr_min;
                        }
                        $tmp_arr[$pos] = 1;
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;

            case 2:
            case 5:
                $opt_count = preg_match('/opt_count=([0-9]+)/', $opt, $matches) ? $matches[1] : 0;
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $opt_count, 0);
                    if (isset($answ_arr[$i])) {
                        $tt = $qtype == 5 ? $answ_arr[$i] : array($answ_arr[$i]);
                        foreach ($tt as $pos) {
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;

            case 3:
            case 19:
                if ($label == 'l1q8' || $label == 'l2q37' || $label == 'l2q38') {
                    $ranges = array(
                        array(0, 0), array(1, 20), array(21, 40), array(41, 60), array(61, 80), array(81, 100), array(101, 120),
                        array(121, 140), array(141, 160), array(161, 180), array(181, 200), array(201, 220), array(221 - 250)
                    );
                } elseif ($label == 'l1q9') {
                    $ranges = array(
                        array(0, 0), array(1, 10), array(11, 20), array(21, 30), array(31, 40), array(41, 50),
                        array(51, 60), array(61, 70), array(71, 80), array(81, 100), array(101, 120), array(121, 150)
                    );
                } elseif ($label == 'l1q10') {
                    $ranges = array(
                        array(0, 0), array(1, 40), array(41, 80), array(81, 120), array(121, 160), array(161, 200), array(201, 250), array(251, 300), array(301, 350), 
                        array(351, 400), array(401, 450), array(451, 500), array(501, 600), array(601, 700), array(701, 800), array(801, 900), array(901, 990)
                    );
                } elseif($label == 'l2q4') {
                    $def = array("min" => 0, "max" => 60);
                    foreach (array("min", "max") as $var) {
                        ${$var} = preg_match("/{$var}=([0-9]+)/i", $opt, $matches) ? $matches[1] : $def[$var];
                    }
                    for($y = $min; $y <= $max; $y++) { $ranges[] = $y; }
                }
                $vc = count($ranges);
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        $v = $answ_arr[$i];
                        if(is_null($v)) continue;
                        for ($l = 0; $l < $vc; $l++) {
                            $r = $ranges[$l];
                            if($label == 'l2q4' && ($v[0] == $r || $v[1] == $r)) {
                                $tmp_arr[$l] = 1;
                            } elseif ((count($r) == 1 && $r == $v) || (count($r) == 2 && $r[0] <= $v && $v <= $r[1])) {
                                $tmp_arr[$l] = 1;
                                break;
                            }
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
            
            case 4:
                for ($i = 0; $i < $j; $i++) {
                    $Arr[] = isset($answ_arr[$i]) && $answ_arr[$i] == 'Y' ? 1 : 0;
                }
                break;
            
            case 6:
            case 17:
                $def = array("type" => 'boolean', "opt_count" => 4, "min_val" => -1, "max_val" => 1);
                foreach (array("opt_count", "min_val", "max_val") as $var) {
                    ${$var} = preg_match("/{$var}=([0-9]+)/i", $opt, $matches) ? $matches[1] : $def[$var];
                }
                $type = preg_match("/type=([a-z]+)/", $opt, $matches) ? $matches[1] : $def['type'];
                $step = $type == 'numeric' ? $max_val - $min_val : 1;
                $vc = $step * $opt_count;
                if($label == 'l1q50') {
                    $vc = 14;
                }
                if($qtype == 17) {
                    $vc++;
                    $sd = 1;
                } else {
                    $sd = 0;
                }
                for($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if(isset($answ_arr[$i])) {
                        foreach($answ_arr[$i] as $s => $v) {
                            if ($qtype == 17 && $v == 'yn=Y') {
                                $pos = 0;
                            } else if ($type == 'boolean') {
                                if ($v == 0)
                                    continue;
                                $pos = $s * $step;
                            } else {
                                $pos = ($s * $step) + $v + $sd;
                            }
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 7:
                $vc = preg_match('/opt_count=([0-9]+)/', $opt, $matches) ? $matches[1] : 0;
                if($label == 'l1q34') {
                    $vc++;
                }
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        foreach($answ_arr[$i] as $pos) {
                            if($label != 'l1q34') {
                                if($pos == 0) continue;
                                $pos--;
                            }
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 8:
            case 12:
                $vc = $qtype == 8 ? 10 : 15;
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        foreach($answ_arr[$i] as $pos) {
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 9:
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, 11, 0);
                    if (isset($answ_arr[$i])) {
                        $pos = $answ_arr[$i] / 10;
                        $tmp_arr[$pos] = 1;
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;

            case 10:
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, 15, 0);
                    if (isset($answ_arr[$i])) {
                        foreach($answ_arr[$i] as $s => $h) {
                            $pos = $s * 5 + $h;
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 11:
                if(!function_exists('get_ethnicity')) {
                    function get_ethnicity($id, $arr, &$res) {
                        if(count($arr[$id]['childs']) > 0) {
                            foreach($arr[$id]['childs'] as $cid) {
                                get_ethnicity($cid, $arr, $res);
                            }
                        } else if(!in_array($id, $res)) {
                            $res[] = $id;
                        }
                    }
                }
                
                $this->load->model('question_model', 'questions');
                $tmp_arr = $this->questions->getEthnicity();
                $eth_arr = array();
                foreach(array_keys($tmp_arr) as $id) {
                    get_ethnicity($id, $tmp_arr, $eth_arr);
                }
                unset($tmp_arr);
                $ec = count($eth_arr);
                $vc = $ec * 2;
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        $eths = $answ_arr[$i];
                        if(count($eths[1]) == 0) {//if not mixed
                            $eths[1] = $eths[0];
                        }
                        foreach($eths as $s => $eth) {
                            $e = end($eth);
                            $pos = array_search($e, $eth_arr);
                            $tmp_arr[$pos + ($s * $ec)] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 15:
                $def = array("scr_min" => 0, "scr_max" => 120);
                foreach (array("scr_min", "scr_max") as $var) {
                    ${$var} = preg_match("/{$var}=([0-9]+)/i", $opt, $matches) ? $matches[1] : $def[$var];
                }
                $opt_count = preg_match('/opt_count=([0-9]+)/', $opt, $matches) ? $matches[1] : 0;
                $vc = abs($scr_max - $scr_min) + 2 + $opt_count;
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $vc, 0);
                    if (isset($answ_arr[$i])) {
                        $v = $answ_arr[$i];
                        if($v[0] == 'Y') {
                            $tmp_arr[0] = 1;
                            $tmp_arr[$v[1] + 1] = 1;
                        } else {
                            $pos = $v[1] - $scr_min + 3;
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 16:
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, 13, 0);
                    if (isset($answ_arr[$i])) {
                        foreach($answ_arr[$i] as $s => $pos) {
                            if($s < 2) {
                                $pos = $s * 6 + $pos;
                            } else {
                                if($pos == 0) continue;
                                $pos = 12;
                            }
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 18:
                $opt_count = preg_match('/opt_count=([0-9]+)/', $opt, $matches) ? $matches[1] : 0;
                $opt_count++;
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $opt_count, 0);
                    if (isset($answ_arr[$i])) {
                        if($answ_arr[$i][0] == 'Y') {
                            $pos = $answ_arr[$i][1] + 1;
                            $tmp_arr[0] = 1;
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 20:
                for($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, 11, 0);
                    if (isset($answ_arr[$i])) {
                        $v = $answ_arr[$i];
                        if($v[0] == 'Y') {
                            $tmp_arr[0] = 1;
                            $tmp_arr[$v[2] + 1] = 1;
                            $tmp_arr[$v[1] + 5] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
            
            case 14:
            case 21:
                if($qtype == 14) {
                    $opt_count = 7;
                } else {
                    $opt_count = preg_match('/opt_count=([0-9]+)/', $opt, $matches) ? $matches[1] : 0;
                }
                for ($i = 0; $i < $j; $i++) {
                    $tmp_arr = array_fill(0, $opt_count, 0);
                    if (isset($answ_arr[$i])) {
                        $pos = $answ_arr[$i];
                        $tmp_arr[$pos] = 1;
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                }
                break;
                
            case 22:
                if($label == 'main_outcome') {
                    $tmp_arr = array_fill(0, 57, 0);
                    if($answ_arr[2][0] == 2) {
                        $tmp_arr[0] = 1;
                    }
                    $n1 = array_search('1', $answ_arr[0]);
                    if($n1 !== FALSE) {
                        $answ_arr[0][$n1] = '0';
                        $n2 = array_search('1', $answ_arr[0]);
                        if($n2 !== FALSE) {
                            $pos = $n2 - $n1 + 1;
                            $tmp_arr[$pos] = 1;
                        }
                    }
                    $Arr[] = implode("\t", $tmp_arr);
                } else {
                    for ($i = 0; $i < $j; $i++) {
                        for($k = 2; $k >= 1; $k--) {
                            $tmp_arr = array_fill(0, 56, 0);
                            if (isset($answ_arr[$i])) {
                                foreach($answ_arr[$i] as $pos => $val) {
                                    if($val == $k) {
                                        $tmp_arr[$pos] = 1;
                                    }
                                }
                            }
                            $Arr[] = implode("\t", $tmp_arr);
                        }
                    }
                }
                break;
        }
        
        if (count($Arr)) {
            $res_str .= implode(",\t", $Arr);
        }
        
        return $res_str;
    }

    /**
     * Convert answer from string to array
     * 
     * @param int $qtype - question type
     * @param string $label - question label
     * @param string $me_answ - my answer
     * @param string $partner_answ - partner's answer
     * @return array
     */
    private function processAnswers($qtype, $label, $me_answ, $partner_answ) {
        if ($me_answ == "" || $partner_answ == "") {
            return array();
        }
        $me_answ = explode('|', $me_answ);
        $partner_answ = explode('|', $partner_answ);
        $res = array();
        foreach (array('me_answ', 'partner_answ') as $ind => $i) {
            foreach (${$i} as $v) {
                
                switch ($qtype) {
                    case 1:
                    case 2:
                    case 4:
                    case 9:
                    case 14:
                    case 21:
                        $res[] = $v;
                        break;
                    case 3:
                    case 19:
                        if($qtype == 19) {
                            $h = explode('#', $v);
                            $res[] = $h[0] == 'Y' ? avg(explode(';', $h[1])) : null;
                        } else {
                            $res[] = $label == 'l2q4' ? explode(';', $v) : avg(explode(';', $v));
                        }
                        break;
                    case 5:
                    case 7:
                    case 15:
                    case 18:
                        $d = ($qtype == 15 || $qtype == 18) ? ';' : ',';
                        $res[] = explode($d, $v);
                        break;
                    case 6:
                    case 16:
                    case 20:
                        if($qtype == 20) {
                            $v = str_ireplace(array('yn=','spin1=','opt='), "", $v);
                        } else {
                            $v = preg_replace('/q[0-9]+v[0-9]+\=/', '', $v, -1);
                        }
                        $res[] = explode(';', $v);
                        break;
                    case 8:
                        foreach (array("yn1", "yn2") as $var) {
                            ${$var} = preg_match("/{$var}=([YN]{1})/", $v, $matches) ? $matches[1] : 'N';
                        }
                        if($yn1 == 'Y') {
                            $tmp_res = array(0);
                            if ($yn2 == 'Y') {
                                $tmp_res[] = 1;
                                if (preg_match("/spin1=([0-9]+)/", $v, $matches)) {
                                    $tmp_res[] = $matches[1] + 4;
                                }
                            } else {
                                $no_choise = preg_match("/opt=(c|spin)/", $v, $matches) ? $matches[1] : 'c';
                                if($no_choise == 'spin') {
                                    $tmp_res[] = 2;
                                    if (preg_match("/spin2=([0-9]+)/", $v, $matches)) {
                                        $tmp_res[] = $matches[1] + 4;
                                    }
                                } else {
                                    $tmp_res[] = 3;
                                }
                            }
                            $res[$ind] = $tmp_res;
                        }
                        break;
                    case 10:
                        $tmp_res = array();
                        foreach (array('a', 'c', 'b') as $s) {
                            if (preg_match("/h{$s}=(one|two);([0123]{0,1})/i", $v, $matches)) {
                                $h = $matches[1];
                                $tmp_res[] = $h == 'two' ? $matches[2] + 2 : $matches[2] - 1;
                            }
                        }
                        $res[$ind] = $tmp_res;
                        break;
                    case 11:
                        $tmp_res = array(array(), array());
                        for ($h = 1; $h <= 2; $h++) {
                            foreach (array("main", "sub", "subsub") as $var) {
                                if (preg_match("/" . $var . $h . "=([0-9]+)/i", $v, $matches)) {
                                    $tmp_res[$h-1][] = $matches[1];
                                }
                            }
                        }
                        $res[$ind] = $tmp_res;
                        break;
                    case 12:
                        $tmp_res = array();
                        $yn = preg_match("/yn=([YN]{1})/", $v, $matches) ? $matches[1] : 'N';
                        if ($yn == 'Y') {
                            $tmp_res[] = 0;
                            if (preg_match("/spin1=([0-9]+)/", $v, $matches)) {
                                $tmp_res[] = $matches[1] + 1;
                            }
                        } else {
                            $no_choise = preg_match("/opt=(c|spin)/", $v, $matches) ? $matches[1] : 'c';
                            if($no_choise == 'spin') {
                                $tmp_res[] = 7;
                                if (preg_match("/spin2=([0-9]+)/", $v, $matches)) {
                                    $tmp_res[] = $matches[1] + 9;
                                }
                            } else {
                                $tmp_res[] = 8;
                            }
                        }
                        $res[$ind] = $tmp_res;
                        break;
                    case 17:
                        $v = preg_replace('/q[0-9]+v[0-9]+\=/', '', $v, -1);
                        $tmp_res = explode(';', $v);
                        if(end($tmp_res) == 'yn=Y') {
                            $res[$ind] = $tmp_res;
                        }
                        break;
                    case 22:
                        if($label == 'main_outcome') {
                            $res[] = explode(',', $v);
                        } else {
                            $res[$ind] = explode(',', $v);
                        }
                        break;
                }
                
            }
        }
        return $res;
    }

    private function  saveConvertedStringToDB($user_id, $level_id, $conv_answ)
    {
        $insert_query = "INSERT INTO converted_answer(user_id, level_id, result) VALUES ({$user_id}, {$level_id}, '{$conv_answ}') ON DUPLICATE KEY UPDATE result=VALUES(result)";
        $this->db->query($insert_query);
    }

}

/* End of file algorithm_model.php */
/* Location: ./application/models/algorithm_model.php */