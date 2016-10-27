        <script type="text/javascript" src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.flip.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.easing.min.js') ?>"></script>
        <script type="text/javascript" src="<?=base_url('assets/js')?>/tipped.js"></script>
        <script type="text/javascript" src="<?=base_url('assets/js')?>/jquery.selectBox.js"></script>
        <!--[if lt IE 9]>
        <script src="<?php echo site_url(); ?>assets/js/html5shiv.js"></script>
        <script src="<?php echo site_url(); ?>assets/js/respond.min.js"></script>
        <![endif]-->
        <script language="javascript">
			$(".logo").mouseover(function(e) {
				$(this).find("span").css("visibility","hidden");
			});
			$(".logo").mouseleave(function(e) {
				$(this).find("span").css("visibility","visible") 
			});
            $('select')
                    .selectBox({
                        mobile: true
                    })
		</script>
        <div id="results" class="modal fade top span8">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                        <div class="modal-hdr-welcme"><h2><?= lang('q_answered_title') ?></h2></div>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>

        <?php if ((!isset($is_outcome) || !$is_outcome) && isset($qnum) && $lnum == 1 && $qnum == 4) : ?>
            <div id="definitions" class="modal fade top span8">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                            <div class="modal-hdr-welcme"><h2><?= lang('q_definitions_title') ?></h2></div>
                        </div>
                        <div class="modal-body"><?= lang("l1q4_text") ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </body>
</html>