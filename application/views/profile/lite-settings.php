<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <?php $this->load->view('include/head'); ?>
    </head>
    <body class="nobg">
        <?php
        if ($this->session && $this->session->userdata['user_info']) {
            $user_info = $this->session->userdata['user_info'];
        } else {
            $user_info = array("language" => "english");
        }
        $pwd_lng = lang('change_password');
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
                            <div class="panel-heading" data-toggle="collapse" data-target="#collapse1">
                                <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= lang('username') ?></a></h4>
                            </div>
                            
                            <div id="collapse1" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <input type="text" name="username" id="username" value="<?= $user_info['username'] ?>" maxlength="30" style="width: 375px;"/>
                                </div>
                            </div>
                        </div>
                        
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
                        
                    </div>
                </div><!--/.row-->
                <div class="modal-footer ifrm-footer"><button type="button" class="btn btn-green btn-update-setting" onclick="$('#frm-profile-setting').submit();"><?= lang('btn_update') ?></button></div>
            </form>
        </div>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.cookie.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/all_site/ajax.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/profile/profile-lite-settings.js') ?>"></script>
    </body>
</html>