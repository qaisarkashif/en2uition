<?php

/**
 * Delete all the files (specified type) in the folder
 * @param string $path - path to the folder (without a slash at the end)
 */
function delete_all_files($path, $type = "*.*") {
    $files = glob($path . '/' . $type); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            @unlink($file); // delete file
        }
    }
}

/**
 * Deleting the directory (including all files and folders in it)
 * @param string $directory - path to the directory (without a slash at the end)
 */
function recursiveRemoveDirectory($directory) {
    if (is_dir($directory)) {
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                recursiveRemoveDirectory($file);
            } else {
                @unlink($file);
            }
        }
        rmdir($directory);
    }
}

/**
 * Move all files and folders from one directory to another
 * @param string $source - path to the source directory
 * @param string $dest - path to the destination directory
 */
function merge_dir($source, $dest) {
    foreach (glob("{$source}/*") as $file) {
        if (is_dir($file)) {
            merge_dir("{$source}/" . basename($file), "{$dest}/" . basename($file));
        } else {
            rename($file, "{$dest}/" . basename($file));
        }
    }
    rmdir($source);
}

function make_old_files($path, $revert = false) {
    $files = glob($path . '/' . ($revert ? "*.old" : "*.*")); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            rename($file, ($revert ? substr($file, 0, -4) : $file . ".old")); // delete file
        }
    }
}

function create_thumbnails($filedata = array(), $thumbs = array()) {
    switch (str_replace(".", "", $filedata['file_ext'])) {
        case 'jpeg':
            $image = imagecreatefromjpeg($filedata['full_path']);
            break;
        case 'gif':
            $image = imagecreatefromgif($filedata['full_path']);
            break;
        case 'png':
            $image = imagecreatefrompng($filedata['full_path']);
            break;
        default:
            $image = imagecreatefromjpeg($filedata['full_path']);
            break;
    }

    foreach ($thumbs as $thumb) {

        $dest_image = $filedata['file_path'] . $thumb['thumb_name'] . $filedata['file_ext'];

        $thumb_width = $thumb['width'];
        $thumb_height = $thumb['height'];

        $width = $filedata['image_width'];
        $height = $filedata['image_height'];

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        //Resize and crop
        imagecopyresampled($thumb, $image, 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0, 0, $new_width, $new_height, $width, $height);

        //save result image
        switch (str_replace(".", "", $filedata['file_ext'])) {
            case 'jpeg':
                imagejpeg($thumb, $dest_image, 90); //90 = jpeg quality
                break;
            case 'gif':
                imagegif($thumb, $dest_image);
                break;
            case 'png':
                imagepng($thumb, $dest_image);
                break;
            default:
                imagejpeg($thumb, $dest_image, 90); //90 = jpeg quality
                break;
        }
        imagedestroy($thumb);
    }
    imagedestroy($image);
}
