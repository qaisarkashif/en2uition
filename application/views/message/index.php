<div class="col-xs-12 col-sm-12 page-message">
	<div id="msg-list">
        <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3" class="msgs">
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
                            <li><a class="friends" data-v="friend"><?= lang('friends') ?>&nbsp;<span class="badge">0</span></a></li>
                            <li><a class="strangers" data-v="stranger"><?= lang('strangers') ?>&nbsp;<span class="badge">0</span></a></li>
                            <li><a class="unread" data-v="unread"><?= lang('unread') ?>&nbsp;<span class="badge">0</span></a></li>
                        </ul>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages_list as $id => $message) { ?>
                <tr onClick="javascript: window.location = '/messages/history/msg-<?= $id ?>';" class="<?= $message['who'] . ($message['unread'] == 1 ? ' unread' : '') ?>" id="msg-<?= $id ?>">
                    <td width="33%" class="msg-user-info">
                        <a href="<?= site_url("/visitor/uid-" . $message['uid']) ?>">
                            <img class="profile-img" src="<?= $message['ava'] ?>" alt=""/>
                        </a>
                        <span class="user-info"><a href="<?= site_url("/visitor/uid-" . $message['uid']) ?>"><span><?= $message['username'] . '</span></a><br>' . $message['date'] ?><br>
                            <a class="msg-delete" data-id="<?= $id ?>"><?= lang('btn_delete') ?></a>
                            <?php $blocked = in_array($message['uid'], $black_list); ?>
                            <a class="msg-block<?= $blocked ? ' hide' : '' ?>" data-uid="<?= $message['uid'] ?>"><?= lang('btn_block') ?></a>
                            <a class="msg-unblock<?= $blocked ? '' : ' hide' ?>" data-uid="<?= $message['uid'] ?>"><?= lang('btn_unblock') ?></a>
                        </span>
                    </td>
                    <td width="67%" align="left" class="msg-txt">
                        <p>
                            <?php
                                if(strlen($message['msg_text']) > 250) {
                                    echo substr($message['msg_text'], 0, 250) . "...";
                                } else {
                                    echo $message['msg_text'];
                                }                                
                            ?>
                        </p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>