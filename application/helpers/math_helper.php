<?php

function avg($input_arr) {
    $c = count($input_arr);
    return $c > 0 ? array_sum($input_arr) / $c : 0;
}