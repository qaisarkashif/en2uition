<div id="main">	
    <?php
    $this->load->view('include/header', isset($header_data) ? $header_data : array());
    ?>
    <div class="<?php if ($top_menu != "home") { echo 'container main-container'; } ?>">
        <?php
        echo $main_content;
        if ($top_menu != "profile" && $top_menu != "home") {
            $this->load->view('page/bottom');
        }
        if ($top_menu == "home") {
            echo '<div id="bottom-home"></div>';
        }
        ?>
    </div>
    <?php if ($top_menu != 'home' && $top_menu != 'homepage') : ?>
        <div class="container copyright"><p><?= lang('copyright') ?></p></div>
    <?php endif; ?>
    <?php $this->load->view('page/footer'); ?>
</div>