<div class="account-area">
    <div class="container">
        <?php if (isset($this->session->userdata['logged'])) { ?>
            <form method="post" action="/signout" style="float:left; margin-left:30px;">
                <input type="submit" name="btn-signout" value="<?=lang('signout')?>" class="btn-signout"/>
            </form>
        <?php } ?>
    </div>
</div>
<div class="acc-area">
    <div class="container">
        <?php if (!isset($this->session->userdata['logged']) && isset($top_menu) && $top_menu == "home") { ?>
            <div id="signup">
                <form id="frm-signup" name="signup" method="post" action="/signup" autocomplete="off">
                    <ul>
                        <li id="show-su"><div class="btn-su"><a class="btn-signup"><?=lang('signup')?></a></div>
                            <ul class="signup">
                                <li class="form-group"><input class="form-control" type="text" name="email" id="email" placeholder="<?= lang('placeholders_email') ?>"/></li>
                                <li class="form-group"><input class="form-control" type="text" name="username" id="username" placeholder="<?= lang('placeholders_username') ?>"/></li>
                                <li class="form-group"><input class="form-control" type="password" name="password" id="password" placeholder="<?= lang('placeholders_password') ?>"/></li>
                                <li class="form-group"><input class="form-control" type="password" name="passconf" id="passconf" placeholder="<?= lang('placeholders_reenter_password') ?>"/></li>
                                <li><input type="submit" name="btnsubmit" value="<?=lang('signup')?>" class="btn btn-green"></li>
                            </ul>
                        </li>
                    </ul>
                </form>
            </div>
            <div id="signin">
                <form id="frm-signin" name="signin" method="post" action="/signin" autocomplete="off">
                    <ul>
                        <li>
                        <li id="show-si"><div class="btn-si"><a class="btn-signin"><?=lang('signin')?></a></div>
                            <ul class="signin">
                                <li class="form-group"><input class="form-control" type="text" name="email" id="email" placeholder="<?= lang('placeholders_email') ?>"/></li>
                                <li class="form-group"><input class="form-control" type="password" name="password" placeholder="<?= lang('placeholders_password') ?>"/></li>
                                <li>
                                    <input type="submit" name="btnsubmit" value="<?=lang('signin')?>" class="btn btn-green"/>
                                    <a id="btn_forgot_pwd" onclick="$('#forgotModal').modal('show'); return false;"><?=lang('btn_forgot_pwd')?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </form>
            </div>
        <?php } ?>
    </div>
</div>
<div class="logo-area"><a id="en_logo">&nbsp;</a></div>
<div class="slider-area">
    <div id="slide"class="slider-content">
        <video id="video" controls preload="metadata" poster="<?= base_url('assets/img'); ?>/gear.jpg">
            <source src="<?= base_url('assets/img'); ?>/gear.mp4" type='video/mp4' />
            <source src="<?= base_url('assets/img'); ?>/gear.webm" type='video/webm' />
            <source src="<?= base_url('assets/img'); ?>/gear.ogg" type='video/ogg' />
            <object type="application/x-shockwave-flash" width="1200" height="461">
                <param name="movie" value="<?= base_url('assets'); ?>/flash-player51af.swf?videoUrl=<?= base_url('assets/img'); ?>/gear.mp4" />
                <param name="allowfullscreen" value="true" />
                <param name="wmode" value="transparent" />
                <param name="flashvars" value="controlbar%3dover%26image%3dimg/poster.jpg%26file%3dflash-player51af.swf?videoUrl=video/<?= base_url('assets/img'); ?>/gear.mp4" />
                <img alt="Tears of Steel poster image" src="<?= base_url('assets/img'); ?>/gear.jpg" width="1200" height="461" title="" />
            </object>
        </video>
        <ul id="video-controls" class="controls" style="display:none;">
            <li><button id="playpause" type="button"></button></li>
            <li><button id="stop" type="button"></button></li>
            <li class="progress">
                <progress id="progress" value="0" min="0">
                    <span id="progress-bar"></span>
                </progress>
            </li>
            <li><button id="mute" type="button"></button></li>
            <li><button id="volinc" type="button"></button></li>
            <li><button id="voldec" type="button"></button></li>
            <li><button id="fs" type="button"></button></li>
        </ul>
    </div>
</div>
<div class="bottom-area">
    <div class="container">
        <div class="left-text"><?= lang('home_left_text') ?></div>
        <div class="right-text"><?= lang('home_right_text') ?></div>
    </div>
</div>

<div id="forgotModal" class="modal fade top span8">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                <div class="modal-hdr-welcme"><h2><?= lang('btn_forgot_pwd') ?></h2></div>
            </div>
            <div class="modal-body">
                <input class="form-control" type="text" name="frgt-email" id="frgt-email" placeholder="<?= lang('placeholders_email') ?>"/>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" onclick="forgotPassword(); return false;"><?= lang('btn_send') ?></button>
                <button class="btn btn-default btn-sm" onclick="$('#forgotModal').modal('hide'); return false;"><?= lang('btn_cancel') ?></button>
            </div>
        </div>
    </div>
</div>