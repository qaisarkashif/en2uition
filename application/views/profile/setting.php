<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <?php $this->load->view('include/head', array('additional_css' => array('tipped.css'))); ?>
    </head>
    <body class="nobg">
        <?php
        if ($this->session && $this->session->userdata['user_info']) {
            $user_info = $this->session->userdata['user_info'];
        } else {
            $user_info = array(
                "language" => "english",
                "active" => 1,
                "users_guide_hidden" => 0
            );
        }
        $pwd_lng = lang('change_password');
        $ntf_lng = lang('sett_email_notifications');
        ?>
        <div class="profile-setting">
            <form method="post" autocomplete="off" id="frm-profile-setting">
                <div class="wow fadeInDown">
                    <div class="alert" role="alert">
                        <button type="button" class="close" aria-label="Close" onclick="$(this).closest('.alert').hide()"><span aria-hidden="true">&times;</span></button>
                        <div class="alert-msg"></div>
                    </div>
                    <div id="accordion" class="panel-group col-md-6 col-md-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse2">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('password') ?></a></h4>
                            </div>

                            <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body edit-profile">
                                    <ul>
                                        <li>
                                            <label><?= $pwd_lng['old'] ?></label>
                                            <input type="password" name="old_password" id="password" value=""/>
                                        </li>
                                        <li>
                                            <label><?= $pwd_lng['new'] ?></label>
                                            <input type="password" name="new_password" id="password" value=""/>
                                        </li>
                                        <li>
                                            <label><?= $pwd_lng['reenter'] ?></label>
                                            <input type="password" name="new_password1" id="new_password1" value=""/>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse3">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('language') ?></a></h4>
                            </div>
                            <div id="collapse3" class="panel-collapse collapse">
                                <div class="panel-body edit-profile">
                                    <ul>
                                        <li>
                                            <label><?= lang('site_language') ?></label>
                                            <?php 
                                            $langs = array(
                                                "english" => "English",
                                                //"dutch" => "Dutch"
                                            ); 
                                            ?>
                                            <select name="lang" id="lang">
                                                <?php 
                                                foreach($langs as $key => $val) {
                                                    echo '<option value="' . $key . '" ' . ($key == $user_info["language"] ? "selected" : "") . '>' . $val . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse4">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('account_status') ?></a></h4>
                            </div>
                            <div id="collapse4" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <a class="profile-status<?= $user_info["active"] == 0 ? ' inactive' : '' ?>"><?= $user_info["active"] == 0 ? 'Inactive' : 'Active' ?></a>
                                    <input type="hidden" name="profile-status" id="profile-status" value="<?= $user_info["active"] ?>"/>
                                    <a class="site-vers<?= $user_info["joined"] == 0 ? ' inactive' : '' ?>"><?= $user_info["active"] == 0 ? 'en2uition-lite' : 'en2uition' ?></a>
                                    <input type="hidden" name="site-vers" id="site-vers" value="<?= $user_info["joined"] ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse5">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('messages') ?></a></h4>
                            </div>
                            <div id="collapse5" class="panel-collapse collapse">
                                <div id="blocked-user-list" class="panel-body blocked">
                                    <?php
                                    foreach ($black_list as $id => $name) {
                                        echo '<span class="user-name blocked-user"><a onclick="unblockUser(this, ' . $id . '); return false;" class="cmt-tooltip" title="unblock">' . $name . '</a></span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse6">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('top_menu_buttons') ?></a></h4>
                            </div>
                            <div id="collapse6" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <label><?= lang('users_guide') ?></label>
                                    <a class='users-guide-status<?= $user_info["users_guide_hidden"] == 1 ? ' inactive' : ''?>'><?= $user_info["users_guide_hidden"] == 1 ? 'Hidden' : 'Visible' ?></a>
                                    <input type='hidden' name='users_guide_hidden' value='<?= $user_info["users_guide_hidden"] ?>' />
                                </div>
                            </div>
                        </div>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse7">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= $ntf_lng['accordion_title'] ?></a></h4>
                            </div>
                            <div id="collapse7" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php
                                    $emnf = $user_info['email_notifications'] ? $user_info['email_notifications'] : '00000000';
                                    ?>
                                    <ul id="email-notifications-list">
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['msg'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_message"<?= $emnf[0] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['reply'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_reply"<?= $emnf[1] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['req_fr'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_frshp_req"<?= $emnf[2] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['acc_fr_req'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_accept_frshp_req"<?= $emnf[3] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['req_answ'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_answers_req"<?= $emnf[4] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['acc_answ_req'] ?>  
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_accept_answers_req"<?= $emnf[5] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['prof_cmt'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_profile_comment"<?= $emnf[6] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                        <li class="checkbox">
                                            <?= $ntf_lng['items']['photo_cmt'] ?>
                                            <input type="checkbox" class="emnf-checkbox" value="1" name="ntf_photo_comment"<?= $emnf[7] == 1 ? ' checked' : '' ?>/>
                                        </li>
                                    </ul>
                                    <input type="hidden" name="email_notifications" id="email_notifications" value="<?= $emnf ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--/.row-->
                <div class="modal-footer ifrm-footer"><button type="button" class="btn btn-green btn-update-setting" onclick="$('#frm-profile-setting').submit();"><?= lang('btn_update') ?></button></div>
            </form>
        </div>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.cookie.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/all_site/ajax.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/tipped.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/profile/profile-settings.js') ?>"></script>
    </body>
</html>