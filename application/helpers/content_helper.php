<?php

function set_image($image) {
    $rand = random_string('numeric', 8);
    if(preg_match('/^(.*)\?rand=[0-9]*$/i', $image, $matches)) {
        return sprintf("%s?rand=%s", $matches[1], $rand);
    }
    return sprintf("%s?rand=%s", $image, $rand);
}