<script type="text/javascript" src="<?=base_url('assets/js')?>/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/tipped.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/moment.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/jquery.bxslider.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/ion.rangeSlider.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/jquery.elastic.source.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js')?>/jquery.cookie.js"></script>
<?php if (isset($top_menu) && $top_menu == "neighborhood") { ?>
	<script type="text/javascript" src="<?= base_url('assets/js'); ?>/jquery.slimscroll.js"></script>
<?php } ?>
<?php
if (isset($additional_js)) {
    foreach ($additional_js as $filename) {
        echo '<script type="text/javascript" src="' . base_url('assets/js') . '/' . $filename . '"></script>';
    }
}
?>

<script type="text/javascript">
    $(document).ready(function(){

        Tipped.create('.cmt-tooltip',{ containment: 'viewport', position: 'bottom', inline: true });
		var nbrHeight = 1;
        if ($('.nbr-data.mn-data').length || $(".nbr-data.usr-hgt table tr").length > 15) {

			$('.nbr-data.usr-hgt table tr:lt(15)').each(function() {
				var h = $(this).innerHeight(true);
			   	nbrHeight += h.context.offsetHeight;
			});
			if (nbrHeight > 1) $('.nbr-data.usr-hgt').css("max-height",nbrHeight+"px");
            // Neighbour pages scroll
			$(".nbr-data").each(function( v ) {
			if ($(this).find("table").innerHeight(true).context.offsetHeight > 0)
				$(this).slimScroll({
					alwaysVisible: true,
					railVisible: true,
					size : '8px',
					color: '#8dd7fc',
					railColor: '#f1f1f1',
					railOpacity : .5,
					opacity : .9,
				});
			});
        }

        <?php if (isset($dailymood_hidden) && $dailymood_hidden == 1) { ?>$(".dragger").hide();<?php } ?>

        $('.dropdown-menu').click(function(e) {
            e.stopPropagation();
        });

        function showIframe(id, src, modal_title) {
            var modal = $('#modal-window');
            modal.find(".modal-hdr-welcme").html('<h2>' + modal_title + '</h2>');
            modal.find(".modal-body").html('<iframe id="' + id + '" src="' + src + '" frameborder="0" scrolling="no" width="99.6%"></iframe>');
            modal.modal('show');
        }

        $('#edit-profile').click(function() {
            showIframe('if-edit-profile', '/profile/edit/iframe', 'Edit Profile:');
        });

        $('#pro-setting').click(function() {
            showIframe('if-profile-settings', '/profile/settings/iframe', "Settings:");
        });

        $('#lite-settings').click(function() {
            showIframe('if-litepro-settings', '/relationship/lite_settings/iframe', "Settings:");
        });

        $('#accordion').on('click', '.panel-heading', function() {
            if ($(this).find("h4.panel-title i").hasClass("fa-caret-right"))
                $(this).find("h4.panel-title i").removeClass("fa-caret-right").addClass("fa-caret-down");
            else
                $(this).find("h4.panel-title i").removeClass("fa-caret-down").addClass("fa-caret-right");
        });

        $(document).on('click', ".more:not(.photo-menu .more)", function(e) {
            e.preventDefault();
            if ($(this).html() == "<?= strtolower(lang('btn_more')) ?>")
                $(this).html("<?= strtolower(lang('btn_less')) ?>");
            else
                $(this).html("<?= strtolower(lang('btn_more')) ?>");
        });
		$(".logo").mouseover(function(e) {
			$(this).find("span").css("visibility","hidden");
		});
		$(".logo").mouseleave(function(e) {
			$(this).find("span").css("visibility","visible")
		});
    });
</script>

        <div id="modal-window" class="modal fade top span8">
            <div class="modal-dialog modal-vertical-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                        <div class="modal-hdr-welcme"><h2><?= lang('modal_window') ?></h2></div>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>

        <?php if($top_menu == 'visitor') : ?>
            <div id="sendMessageModal" class="modal fade top span8">
                <div class="modal-dialog modal-vertical-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                            <div class="modal-hdr-welcme"><h2><?= lang('send_message_title') ?></h2></div>
                        </div>
                        <div class="modal-body">
                            <textarea placeholder="<?= lang('placeholders_your_message') ?>" rows="7" style="width: 100%"></textarea>
                            <input type="hidden" name="send_to_user" id="send_to_user" value="<?= $user['user_id'] ?>" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success"><?= lang('btn_send') ?></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('btn_cancel') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        endif;
        if(in_array($top_menu, array('profile', 'visitor', 'photo_page', 'homepage'))) {
            $this->load->view('/include/comment_html');
        }
        ?>
    </body>
</html>