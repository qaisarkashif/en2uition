<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=980">
        <meta name="description" content="">
        <meta name="author" content="">
        <title><?php echo isset($pagetitle) ? $pagetitle : 'Home | en2uition'; ?></title>

        <!-- core CSS -->
        <link href="<?= base_url('assets/css'); ?>/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('assets/css'); ?>/main.css" rel="stylesheet" type="text/css" />
        <?php
        if (isset($additional_css)) :
            foreach ($additional_css as $filename) :
                echo '<link href="' . base_url('assets/css') . '/' . $filename . '" rel="stylesheet" type="text/css" />';
            endforeach;
        endif;
        ?>
    </head><!--/head-->
    <body <?php if (isset($top_menu) && ($top_menu == "home" || $top_menu == "edit_profile")) { echo 'class="nobg"'; } ?>>