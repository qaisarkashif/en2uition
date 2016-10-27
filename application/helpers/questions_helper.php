<?php

function get_question_mode($opt) {
    if (preg_match("/mode=([SD]{1})/i", $opt, $matches)) {
        return $matches[1] == 'S' ? 'single' : 'double';
    } else {
        return 'double';
    }
}

function has_subquestion($opt) {
    if(preg_match("/subquestion/i", $opt)) {
        return TRUE;
    }
    return FALSE;
}

function get_yesno_text($str) {
    if ($str == 'Y') {
        return lang('q_btn_yes');
    } else if ($str == 'N') {
        return lang('q_btn_no');
    }
    return "";
}

function get_range_text($answer) {
    $minmax = explode(';', $answer);
    return $minmax[0] == $minmax[1] ? $minmax[0] : str_replace(';', "-", $answer);
}

function get_spinner_text($qlbl, $answer, $optional = "") {
    if (preg_match("/type=([a-z]+)/", $optional, $matches)) {
        $type = $matches[1];
    } else {
        $type = "boolean";
    }
    $res = array();
    $tmp = array_filter(explode(";", $answer));
    foreach ($tmp as $item) {
        $val = explode('=', $item);
        $l = $val[0][1];
        $i = substr($val[0], 3);
        if ($type == "boolean") {
            $v = $val[1] == '0' ? lang("q_btn_no") : lang("q_btn_yes");
        } else if ($type == "numeric") {
            $v = $val[1];
        } else {
            $v = "";
        }
        $res[] = lang("{$qlbl}s{$l}_spin{$i}") . ': ' . $v;
    }
    return (count($res) > 1 ? '<br>' : '') . implode('<br>', $res);
}

function get_dragdrop_text($qlbl, $answer) {
    if ($answer == '0') {
        return preg_match("/^l1q34/i", $qlbl) ? lang('q_btn_none') : lang("q_btn_nodeal");
    } else {
        $res = array();
        $arr = explode(',', $answer);
        foreach ($arr as $i) {
            $res[] = lang("{$qlbl}_dd{$i}");
        }
        return (count($res) > 1 ? '<br>' : '') . implode('<br>', $res);
    }
}

function get_household_text($qlbl, $answer) {
    $res = array();
    if (preg_match("/hh=([123]{1})/i", $answer, $matches)) {
        if ($matches[1] == 1) {
            $res[] = lang("{$qlbl}_one");
        } elseif ($matches[1] == 2) {
            $res[] = lang("{$qlbl}_two");
        } elseif ($matches[1] == 3) {
            $res[] = lang("{$qlbl}_multiple");
        }
    }
    foreach (array("ha", "hb", "hc") as $hh) {
        if (preg_match("/{$hh}=(two|one)\;([0123]{1})/i", $answer, $matches)) {
            $str = lang("{$qlbl}_{$hh}") . ': ';
            $str .= lang("{$qlbl}_" . $matches[1]) . ': ';
            $str .= lang("{$qlbl}_" . $matches[1] . "_opt" . $matches[2]);
            $res[] = $str;
        }
    }
    return implode('<br>', $res);
}

function get_ethnicity_text($qlbl, $ethnicity, $answer) {
    if (preg_match("/yn=(Y|N)/i", $answer, $matches)) {
        $yn = $matches[1];
    } else {
        $yn = 'N';
    }
    $t = $yn == 'Y' ? 2 : 1;
    $res = array();
    for ($h = 1; $h <= $t; $h++) {
        $eth = "";
        foreach (array("main", "sub", "subsub") as $var) {
            if (preg_match("/" . $var . $h . "=([0-9]+)/i", $answer, $matches)) {
                if (isset($ethnicity[$matches[1]])) {
                    $eth = $ethnicity[$matches[1]]['info']["name"];
                }
            }
        }
        if (!empty($eth)) {
            $res[] = $eth;
        }
    }
    if (count($res) > 1) {
        return lang("{$qlbl}_md") . ": " . implode('-', $res);
    } else {
        return implode($res);
    }
}

function get_ynselection_text($qlbl, $answer) {
    $res = array();
    if (preg_match("/yn1=([YN]{1})/", $answer, $matches)) {
        if($matches[1] == 'Y') {
            $res[] = lang('q_btn_yes');
            if (preg_match("/yn2=([YN]{1})/", $answer, $matches)) {
                if ($matches[1] == 'Y') {
                    $res[] = lang("{$qlbl}_txt") . ': ' . lang('q_btn_yes');
                    if (preg_match("/spin1=([0-9]+)/", $answer, $matches)) {
                        $res[] = lang("{$qlbl}_spin1") . ": " . $matches[1];
                    }
                } else {
                    $res[] = lang("{$qlbl}_txt") . ': ' . lang('q_btn_no');
                    if (preg_match("/opt=(c|spin)/", $answer, $matches)) {
                        if ($matches[1] == 'c') {
                            $res[] = lang("{$qlbl}_opt2");
                        } else {
                            $res[] = lang("{$qlbl}_opt1");
                            if (preg_match("/spin2=([0-9]+)/", $answer, $matches)) {
                                $res[] = lang("{$qlbl}_spin2") . ": " . $matches[1];
                            }
                        }
                    }
                }
            }
        } else {
            $res[] = lang('q_btn_no');
        }
    }
    return implode('<br>', $res);
}

function get_ynselection2_text($qlbl, $answer) {
    $res = array();
    $answer = explode(';', $answer);
    $yn = $answer[0];
    if($yn == 'Y' && isset($answer[1])) {
        $res[] = lang('q_btn_yes');
        $res[] = lang("{$qlbl}_subheading2") . ": " . lang("{$qlbl}_opt" . ($answer[1]+1));
    } elseif($yn == 'N') {
        $res[] = lang('q_btn_no');
    }
    return implode('<br>', $res);
}

function get_noselection_text($qlbl, $answer) {
    $res = array();
    if (preg_match("/yn=([YN]{1})/", $answer, $matches)) {
        if ($matches[1] == 'Y') {
            $res[] = lang('q_btn_yes');
            if (preg_match("/spin1=([0-9]+)/", $answer, $matches)) {
                $res[] = lang("{$qlbl}_spin1") . ": " . $matches[1];
            }
        } else {
            $res[] = lang('q_btn_no');
            if (preg_match("/opt=(c|spin)/", $answer, $matches)) {
                if ($matches[1] == 'c') {
                    $res[] = lang("{$qlbl}_opt2");
                } else {
                    $res[] = lang("{$qlbl}_opt1");
                    if (preg_match("/spin2=([0-9]+)/", $answer, $matches)) {
                        $res[] = lang("{$qlbl}_spin2") . ": " . $matches[1];
                    }
                }
            }
        }
    }
    return implode('<br>', $res);
}

function get_noselection2_text($qlbl, $answer) {
    $res = array();
    if (preg_match("/yn=([YN]{1})/", $answer, $matches)) {
        if ($matches[1] == 'Y') {
            $res[] = lang('q_btn_yes');
            if (preg_match("/opt=([0-9]+)/", $answer, $matches)) {
                $res[] = lang("{$qlbl}_subheading2") . ": " . lang("{$qlbl}_opt" . ($matches[1] + 1));
            }
            if (preg_match("/spin1=([0-9]+)/", $answer, $matches)) {
                $res[] = lang("{$qlbl}_subheading3");
                $res[] = lang("{$qlbl}_spin1") . ": " . $matches[1];
            }
        } else {
            $res[] = lang('q_btn_no');
        }
    }
    return implode('<br>', $res);
}

function get_notyet_scroller_text($qlbl, $answer) {
    $v = explode(';', $answer);
    if($v[0] == 'N') {
        return $v[1];
    } else {
        return lang("q_btn_notyet") . "<br>" . lang("{$qlbl}_subheading2") . ': ' . lang("{$qlbl}_opt" . ($v[1]+1));
    }
}

function get_multispinner_text($qlbl, $answer, $optional = "") {
    for($i = 1; $i < 3; $i++) {
        if (preg_match("/opt_count{$i}=([0-9]+)/", $optional, $matches)) {
            ${"cnt".$i} = $matches[1];
        }
    }
    $res = array();
    $tmp = array_filter(explode(";", $answer));
    foreach(array($cnt1 => "numeric", $cnt2 => 'boolean') as $s => $type) {
        $k = !isset($k) ? 0 : $k;
        for($i = $k; $i < ($s + $k); $i++) {
            $val = explode('=', $tmp[$i]);
            $l = $val[0][1];
            $j = substr($val[0], 3);
            if ($type == "boolean") {
                $v = $val[1] == '0' ? lang("q_btn_no") : lang("q_btn_yes");
            } else if ($type == "numeric") {
                $v = $val[1];
            } else {
                $v = "";
            }
            $res[] = lang("{$qlbl}s{$l}_spin{$j}") . ': ' . $v;
        }
        $k = $s;
    }
    return (count($res) > 1 ? '<br>' : '') . implode('<br>', $res);
}

function get_yesno_spinner_text($qlbl, $answer, $optional = "") {
    $res = array();
    if (preg_match("/type=([a-z]+)/", $optional, $matches)) {
        $type = $matches[1];
    } else {
        $type = "boolean";
    }
    if (preg_match("/yn=([YN]{1})/", $answer, $matches)) {
        if ($matches[1] == 'Y') {
            $tmp = array_filter(explode(";", $answer));
            foreach($tmp as $item) {
                $val = explode('=', $item);
                if($val[0] != 'yn') {
                    $l = $val[0][1];
                    $j = substr($val[0], 3);
                    if ($type == "boolean") {
                        $v = $val[1] == '0' ? lang("q_btn_no") : lang("q_btn_yes");
                    } else if ($type == "numeric") {
                        $v = $val[1];
                    } else {
                        $v = "";
                    }
                    $res[] = lang("{$qlbl}s{$l}_spin{$j}") . ': ' . $v;
                }
            }
            $l = !isset($l) ? 1 : $l;
            $r = lang('q_btn_yes') . '<br>';
            $r .= lang("{$qlbl}s{$l}_subheading2");
            $r .= (count($res) > 1 ? '<br>' : ": ") . implode('<br>', $res);
            return $r;
        } else {
            return lang('q_btn_no');
        }
    }
    return "";
}

function get_selection_text($qlbl, $answer) {
    $arr = explode(',', $answer);
    $res = array();
    foreach($arr as $s) {
        $res[] = lang("{$qlbl}_opt" . ($s+1));
    }
    return (count($res) > 1 ? '<br>' : '') . implode('<br>', $res);
}

function get_ynrange_text($qlbl, $answer) {
    $res = array();
    $answer = explode('#', $answer);
    $yn = $answer[0];
    if($yn == 'Y' && isset($answer[1])) {
        $res[] = lang('q_btn_yes');
        $minmax = explode(';', $answer[1]);
        $answer = $minmax[0] == $minmax[1] ? $minmax[0] : str_replace(';', "-", $answer[1]);
        $res[] = lang("{$qlbl}_subheading2") . ": " . $answer;
    } elseif($yn == 'N') {
        $res[] = lang('q_btn_no');
    }
    return implode('<br>', $res);
}