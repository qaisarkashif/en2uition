<div class="fmenu">
    <div id="tab-1" class="page tab-content" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="page-wrap">
                    <h4><?= lang("contactus_heading"); ?></h4>
                    <?= lang("contactus_address1"); ?>
                    <?= lang("contactus_address2"); ?>
                </div>
            </div>

            <div class="col-md-6">
                <form id="main-contact-form" class="contact-form" name="contact-form" onsubmit="return false;" autocomplete="off">
                    <h4><?= lang("contactus_form_heading") ?></h4>
                    <div class="form-group">
                        <textarea name="message" id="message" required class="form-control" rows="8" placeholder="<?= lang('placeholders_your_message') ?>"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary pull-right"><?= lang('btn_send') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Testimonials -->
    <div id="tab-2" class="page tab-content" style="display: none;">
        <div class="row">
            <div class="page-wrap testimonials-carousel">
                <div class="slider3">
                    <?php
                    $slide_arr = lang('testimonials_carousel');
                    if ($slide_arr):
                        foreach ($slide_arr as $key => $slide) :
                            ?>
                            <div class="slide">
                                <div class="testimonials-carousel-thumbnail">
                                    <img width="90" alt="" src="<?= $slide['img_src'] ?>"/>
                                </div>
                                <div class="testimonials-carousel-context">
                                    <div class="testimonials-name"><?= $slide['author'] ?></div>
                                </div>
                                <div class="testimonials-carousel-content"><?= $slide['text'] ?></div>
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="tab-3" class="page tab-content" style="display: none;">
        <div class="row">
            <div class="page-wrap">
                <h4><?= lang("privacy_heading"); ?></h4>
                <?= lang("privacy_text"); ?>
            </div>
        </div>
    </div>
    <div id="tab-4" class="page tab-content" style="display: none;">
        <div class="row">
            <div class="page-wrap">
                <h4><?= lang("tad_heading"); ?></h4>
                <?= lang("tad_text"); ?>
            </div>
        </div>
    </div>
</div>