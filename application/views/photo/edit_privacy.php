<div class="col-xs-12 col-sm-12">
    <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
        <thead>
            <tr>
                <th width="40%" class="username-clr" align="center">
                    <span class="search_area" style="margin-right:75px;">
                        <input type="text" placeholder="<?= lang('placeholders_username') ?>" style="width:200px;" name="search_text"/>
                        <input type="submit" value=""/>
                    </span>
                </th>
                <th width="20%">
                    <span class="photo-privacy-edit">
                        <a class="active"><?= lang('privacy_low') ?></a>
                        <a><?= lang('privacy_medium') ?></a>
                        <a><?= lang('privacy_high') ?></a>
                    </span>
                </th>
                <th width="20%">
                    <span class="photo-privacy-edit">
                        <a><?= lang('privacy_low') ?></a>
                        <a class="active"><?= lang('privacy_medium') ?></a>
                        <a><?= lang('privacy_high') ?></a>
                    </span>
                </th>
                <th width="20%">
                    <span class="photo-privacy-edit">
                        <a><?= lang('privacy_low') ?></a>
                        <a><?= lang('privacy_medium') ?></a>
                        <a class="active"><?= lang('privacy_high') ?></a>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($friends_list as $fid => $friend) { 
                if(isset($granted_permissions[$fid])) {
                    $key = key($granted_permissions[$fid]);
                    $inf = $granted_permissions[$fid][$key];
                    $privacy = array_filter(explode('|', $inf['privacy']));
                } else {
                    $privacy = array();
                    $key = -1;
                }
                ?>
                <tr>
                    <td>
                        <img class="profile-img" src="<?=$friend['avatars']['forum']?>" alt=""/>
                        <span class="user-name"><?=$friend['username']?></span>
                        <a id="edit" onClick="editRecord(this, <?= $fid ?>)"><?= strtolower(lang('btn_edit')) ?></a>
                    </td>
                    <?php 
                    foreach(array('chk1' => 'low', 'chk2' => 'medium', 'chk3' => 'high') as $key => $val) {
                        $_class = $key;
                        $html = '';
                        if(in_array($val, $privacy)) {
                            $_class .= ' checked';
                            $html = '<img src="' . base_url('/assets/img/checked1.png') . '" alt="' . $val . ' privacy"/>';
                        }
                        echo '<td align="center" class="' . $_class . '">' . $html . '</td>';
                    }
                    ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>