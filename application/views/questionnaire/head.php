<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="Content-Type" content="text/html; encoding=UTF-8" />
        <meta content='width=device-width, initial-scale=1.0' name='viewport'/>
        <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>
        <link rel='shortcut icon' href='favicon.ico'/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>        
        <title><?php echo isset($pagetitle) ? $pagetitle : 'Home | en2uition'; ?></title>
        <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('assets/css/FontAwesome/css/font-awesome.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('assets/css/tipped.css'); ?>" rel="stylesheet" type="text/css" />
        <?php if(!isset($is_outcome) || !$is_outcome) : ?>
            <link href="<?= base_url('assets/css/ion.rangeSlider.css'); ?>" rel="stylesheet" type="text/css" />
            <link href="<?= base_url('assets/css/ion.rangeSlider.skinFlat.css'); ?>" rel="stylesheet" type="text/css" />
            <link href="<?= base_url('assets/css/jquery.ui.spinner.css'); ?>" rel="stylesheet" type="text/css" />
        <?php endif; ?>
        <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet" type="text/css" />
        <?php if(isset($is_analyze_page) && $is_analyze_page) { ?>
            <link href="<?= base_url('assets/css/shapes.css'); ?>" rel="stylesheet" type="text/css" />
        <?php } ?>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.cookie.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/all_site/ajax.js') ?>"></script>
        <?php if(isset($is_analyze_page) && $is_analyze_page) { ?>
            <script type="text/javascript" src="<?= base_url('assets/js/questionnaire/analyze.js') ?>"></script>
        <?php } ?>
        <?php if(!isset($is_outcome) || !$is_outcome) : ?>
            <script type="text/javascript" src="<?= base_url('assets/js/jquery.knob.js') ?>"></script>
            <script type="text/javascript" src="<?= base_url('assets/js/questions/questions.js') ?>"></script>
        <?php endif; ?>
        <link href="<?= base_url('assets/css/jquery.selectBox.css'); ?>" rel="stylesheet" type="text/css" />
    </head>
    <body>