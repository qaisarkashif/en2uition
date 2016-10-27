<div id="qprivacy" class="col-xs-12 col-sm-12">
    <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
        <caption>
            <div class="be-head">
                <?= lang('view') ?>&nbsp;
                <a href="/questionnaire/answers/shared/past"<?= $qtype == "past" ? ' class="active"' : "" ?> data-v="past"><?= lang('questionnaire_past') ?></a>&nbsp;
                <a href="/questionnaire/answers/shared/present"<?= $qtype == "present" ? ' class="active"' : "" ?> data-v="present"><?= lang('questionnaire_present') ?></a>&nbsp;
                <?= strtolower(lang('relationship')) ?>
            </div>
        </caption>
        <thead>
            <tr>
                <th width="20%" class="username-clr" align="center">
                    <span class="search_area" style="margin-right:30px;">
                        <input type="text" placeholder="<?= lang('placeholders_username') ?>" style="width:150px;" name="search_text"/>
                        <input type="submit" value=""/>
                    </span>
                </th>
                <?php
                foreach ($levels as $lvl) {
                    echo '<th width="10%"><div class="q-privacy">' . lang("q_" . $lvl['label'] . "_title") . '</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody class="qu-list">
            <?php
            foreach ($granted_perms as $gp) :
                if (!isset($friends_list[$gp['from_user']])) {
                    continue;
                }
                $f = $friends_list[$gp['from_user']];
                $perms = $gp['privacy'][$qtype];
                ?>
                <tr data-uid="<?= $f['user_id'] ?>">
                    <td class="username-clr" align="center">
                        <a href="<?= site_url('/visitor/uid-' . $f['user_id']) ?>">
                            <img class="profile-img" src="<?= $f['avatars']['forum'] ?>" width="42" alt=""/>
                        </a>
                        <span class="user-name"><?= $f['username'] ?></span>
                    </td>
                    <?php
                    foreach ($levels as $lvl) {
                        $pArr = isset($perms[$lvl['level']]) ? array_values($perms[$lvl['level']]) : array();
                        echo '<td><div class="qu-privacy">';
                        foreach (array('low', 'medium', 'high') as $privacy) {
                            echo '<a data-code="' . $privacy . '" data-lid="' . $lvl['id'] . '"' . (in_array($privacy, $pArr) ? ' class="active"' : '') . '></a>';
                        }
                        echo '</div></td>';
                    }
                    ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>