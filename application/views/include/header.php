<?php
if (!isset($active_page)) {
    $active_page = "homepage";
}
?>

<div class="header">
    <div class="container">
        <?php if (isset($this->session->userdata['logged']) && isset($inner_navbar) && $inner_navbar === TRUE) { ?>
            <div class="pull-left navigation<?= $user['joined'] != 1 ? ' not-joined' : '' ?>" id="top-nav">
                <ul>
                    <li class="logo"><a href="/homepage"><span><?= lang('home') ?></span></a></li>
                    <?php
                    if (!in_array($top_menu, array('profile', 'visitor', 'homepage'))) :
                        if ($user['profile_image']) {
                            $avatar_preview = sprintf(USER_AVA_FORUM, $user['user_id'], substr(strrchr($user['profile_image'], '.'), 1));
                        } else {
                            $avatar_preview = DEF_USER_AVA_FORUM;
                        }
                        ?>
                        <li>
                            <a href="/profile" class="pic">
                                <img src="<?= set_image($avatar_preview) ?>" alt="ava" width="33" height="33"/>
                                &nbsp;<?= $user['username'] ? $user['username'] : "<span>&nbsp;</span>" ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="pull-right navigation">
                <ul>
                    <?php 
                    if(($top_menu == 'visitor' && $is_joined) || $user['joined'] == 1) :
                    if ($active_page == "homepage") {
                        echo '<li><a id="userguide" data-id="0"' . ($user['users_guide_hidden'] == 1 ? 'class="hidden"' : '') . '>' . lang('users_guide') . '</a></li>';
                    }
                    if ($top_menu == 'visitor') {
                        $btn_text = $onclick = "";
                        echo '<li><a onclick="return false;" class="btn-message btn-lblue">' . lang('btn_send_message') . '</a></li>';
                        if (isset($my_friend) && $my_friend) {
                            echo '<li><a onclick="return false;" class="btn-friendship friends btn-lblue"><span class="fr">' . lang('friends') .
                            '</span><span class="unfr hide">' . lang('btn_unfriend') . '</span></a></li>';
                        } elseif (isset($my_friend)) {
                            if (isset($frshp_request)) {
                                if ($frshp_request['from_user'] == $visitor_id) {
                                    $btn_text = lang('accept_friendship');
                                    $onclick = "$('.friendship.frm-fri .content[data-id=" . $frshp_request['id'] . "] .accept').click(); ";
                                } else {
                                    $btn_text = lang('friendship_requested');
                                }
                            } else {
                                $btn_text = lang('btn_request_friendship');
                                $onclick = "sendRequest('friendship', " . $visitor_id . "); ";
                            }
                            echo '<li><a onclick="' . $onclick . 'return false;" class="btn-friendship btn-lblue">' . $btn_text . '</a></li>';
                        }
                    }
                    if ($active_page == "friends") :
                        ?>
                        <li>
                            <div class="search_area">
                                <input type="text" placeholder="<?= lang('placeholders_search_friends') ?>" name="search_text"/>
                                <input type="submit" value=""/>
                            </div>
                        </li>
                    <?php endif; ?>
                    <li class="dropdown<?= $active_page == "profile" ? ' selected' : ""; ?>" id="profile-notifications">
                        <a href="<?= site_url("profile"); ?>" class="disabled" id="profile-notify" role="button" data-hover="dropdown" data-toggle="dropdown">
                            <?= lang('profile') ?> <span class="badge hide">0</span>
                        </a>
                    </li>
                    <?php if ($active_page != "friends") : ?>
                        <li class="dropdown<?= $active_page == "photos" ? ' selected' : ""; ?>" id="photo-notifications">
                            <a href="<?= site_url("/photo/page"); ?>" class="disabled" id="photo-notify" role="button" data-hover="dropdown" data-toggle="dropdown">
                                <?= lang('photos') ?> <span class="badge hide">0</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="dropdown<?= $active_page == "friends" ? ' selected' : ""; ?>" id="friends-requests">
                        <a href="<?= site_url("friend"); ?>" class="disabled" id="friends" role="button" data-hover="dropdown" data-toggle="dropdown">
                            <?= lang('friends') ?> <span class="badge hide">0</span>
                        </a>
                    </li>
                    <li<?= $active_page == "messages" ? ' class="selected"' : ""; ?> id="messages"><a href="<?= site_url("messages"); ?>"><?= lang('messages') ?> <span class="badge hide">0</span></a></li>
                    <?php endif; ?>
                    <li class=""><a onclick="return false;"><?= lang('my_account') ?></a>
                        <ul>
                            <?php if(($top_menu == 'visitor' && $is_joined) || $user['joined'] == 1) : ?>
                                <li><a id="edit-profile" data-dismiss="modal"><?= lang('edit_profile') ?></a></li>
                                <li><a id="pro-setting" data-dismiss="modal"><?= lang('settings') ?></a></li>
                                <li><a href="/home"><?= lang('frontpage') ?></a></li>
                            <?php else: ?>
                                <li><a id="lite-settings" data-dismiss="modal"><?= lang('settings') ?></a></li>
                            <?php endif; ?>
                            <li><a href="/signout"><?= lang('signout') ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php } else { ?>
            <div style="position:relative;">
                <div class="navbar-inverse page-nav">
                    <ul class="navbar-nav">
                        <?php if (isset($this->session->userdata['logged'])) { ?>
                            <li class="home-logo"><a href="/homepage">&nbsp;</a></li>
                        <?php } else { ?>
                            <li><a href="/home"><?= lang('home') ?></a></li>
                        <?php } ?>
                        <li <?php if ($top_menu == 'concept') echo "class='active'"; ?>><a href="/page/concept"><?= lang('concept') ?></a></li>
                        <li <?php if ($top_menu == 'features') echo "class='active'"; ?>><a href="/page/features"><?= lang('features') ?></a></li>
                        <li <?php if ($top_menu == 'faq') echo "class='active'"; ?>><a href="/page/faq"><?= lang('faq') ?></a></li>
                        <li <?php if ($top_menu == 'aboutus') echo "class='active'"; ?>><a href="/page/aboutus"><?= lang('about_us') ?></a></li>
                    </ul>
                </div>
				<?php if (isset($joined) && !$joined) { ?>
                    <div class="so-pge"><a href="/signout"><?= lang('signout') ?></a></div>
                <?php } ?>
            </div>
			<?php
        	}
        ?>
    </div><!--/.container-->

</div><!--/header-->