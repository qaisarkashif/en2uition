<div class="col-xs-12 col-sm-12 page-message">
    <div id="msg-reply">
        <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
            <thead>
                <tr>
                    <th width="33%" class="username" align="right">
                        <span class="search_area">
                            <input type="text" placeholder="<?= strtolower(lang('placeholders_username')) ?>" style="width:160px;" name="search_text"/>
                            <input type="submit" value=""/>
                        </span>
                    </th>
                    <th width="67%">
                        <ul class="msg-menu">
                            <li><a href="<?= site_url("messages") ?>"><?= lang('btn_back') ?></a></li>
                            <li><a onclick="deleteMessages(<?= $msg_id?>); return false;"><?= lang('btn_delete') ?></a></li>
                        </ul>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $reply_to_user = 0;
                foreach ($messages_list as $id => $message) : 
                    $reply_to_user = $message['reply_to_user'];
                    ?>
                    <tr class="user-reply<?= $message['unread'] == 1 && $message['from_user'] != $uid ? ' unread' : '' ?>" id="msg-<?= $id ?>">
                        <td class="msg-user-info" valign="top">
                            <a href="<?= site_url("/visitor/uid-" . $message['from_user']) ?>"><img class="profile-img" src="<?= $message['ava'] ?>" alt=""/></a>
                            <span class="user-info">
                                <a href="<?= site_url("/visitor/uid-" . $message['from_user']) ?>"><span><?= $message['username'] ?></span></a>
                                <br>
                                <?php 
                                echo $message['date'];
                                if($message['from_user'] != $uid && $message['unread'] == 0) : ?>
                                    <br>
                                    <a class="msg-unread" onclick="markAsUnread(<?= $id ?>, this); return false;"><?= lang('btn_mark_unread') ?></a>
                                <?php endif; ?>
                             </span>
                        </td>
                        <td align="left" class="msg-txt">
                            <p>
                                <?php
                                    if(strlen($message['msg_text']) > 250) {
                                        echo substr($message['msg_text'], 0, 250) . '<span id="more-text' . $id . '" style="display:none;">'.substr($message['msg_text'], 250).'</span>'.
                                            '<a id="main-more-'.$id.'" class="more" onClick="$(\'#more-text'.$id.'\').toggle();">more</a>';
                                    } else {
                                        echo $message['msg_text'];
                                    }                                
                                ?>
                            </p>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="msg-reply-box">
                    <td width="33%" class="msg-user-info" valign="top">
                        <img class="profile-img" src="<?= $uava ?>" alt=""/>
                        <span class="user-info"><?= $uname ?><br> <?= strtolower(lang('now')) ?><br></span>
                    </td>
                    <td width="67%" align="left" valign="middle">
                        <textarea name="reply" id="reply" class="msg-reply" placeholder="<?= lang('placeholders_reply') ?>" data-msg-id="<?= $msg_id ?>" data-to-uid="<?= $reply_to_user ?>"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>