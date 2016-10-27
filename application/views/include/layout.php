<div id="main">
    <?php 
    if(isset($active_page) && in_array($active_page, array('neighborhood', 'new_topic', 'topic'))) {
        $this->load->view('neighborhood/header', isset($header_data) ? $header_data : array());
    } else {
        $this->load->view('include/header', isset($header_data) ? $header_data : array());
    }
    ?>
    <div class="container main-container">
        <?= $main_content ?>
    </div>	
    <?php $this->load->view('include/footer', isset($footer_data) ? $footer_data : array()); ?>
</div>