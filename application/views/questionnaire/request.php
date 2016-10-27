<div class="questionnaire-past req-type-<?= $type ?>">
    <div class="col-xs-12 col-sm-12">
    	<h1 class="que-req-head"><?=$type == "past" ? "PAST" : "CURRENT";?> RELATIONSHIP</h1>
        <div class="text-block"><?= lang("q_top_text_{$type}") ?></div>
        <div class="q-level">
            <ul>
                <?php
                $access = true;
                foreach ($levels as $level) :
                    ?>
                    <li class="ql-rst">
			            <div class="q-level-left"><img src="../../assets/img/level/<?=$level['level'] == 7 ? 'outcome.png' : 'level-'.$level['level'].'.png' ?>" alt=""></div>
                        <div class="questionnaire-past-level" id="<?= $level['level'] ?>">
                            <a 
                                href="<?= site_url(($level['level'] == 7 && $type == 'past' ? "/outcome" : sprintf('questions/%s/l%s/q1', $type, $level['level']))) ?>" 
                                class="cmt-tooltip"
                                onclick="<?= !$access ? 'return false;' : '' ?>" 
                                title="<?= !$access ? lang('q_answer_previous_before') : '' ?>"
                            >
                                <span><?= lang('q_' . $level['label'] . '_title') ?></span>
                                <span><?= $level['answered_count'] . '/' . $level['quest_count'] ?></span>
                                <font><?= lang('q_answer') . " " . lang('q_' . $level['label'] . '_title') ?></font>
                            </a>
                        </div>
                        <div class="q-level-right lvl-info" id="lvl-det-<?= $level['level'] ?>"><?= lang("q_lvl_".$level['level']."_info")?></div>
                    </li>
                    <?php
                    if ($access && $level['quest_count'] > 0 && $level['answered_count'] < $level['quest_count']) {
                        $access = false;
                    }
                endforeach;
                ?>
            </ul>
        </div>
        <div class="text-block"><?= str_replace("#SHAPE_COUNT#", $shape_count, lang("q_middle_text_{$type}")) ?></div>
    </div>
</div>
<div class="clear"></div>

<?php if($is_joined) : ?>
<div id="accordion" class="panel-group col-md-10 col-md-offset-1 que-past-panel">
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#collapse2">
            <h4 class="panel-title"><i class="fa fa-caret-<?= $active_box == 'privacy' ? "down" : "right" ?>"></i><a><?= lang('q_review_degree') ?></a></h4>
        </div>

        <div id="collapse2" class="panel-collapse collapse<?= $active_box == 'privacy' ? " in" : "" ?>">
            <div class="panel-body">
                <table id="table" class="ql-list privacy-que" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
                    <thead>
                        <tr>
                            <?php
                            foreach ($levels as $level) {
                                echo '<th>' . lang('q_' . $level['label'] . '_title') . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="review-privacy-ans">
                            <?php foreach ($levels as $level) { ?>
                                <td align="center">
                                    <div class="profile-level-privacy">
                                        <a class="plp-low" data-id="<?= $level['id'] ?>" data-code="low"></a>
                                        <a class="plp-medium" data-id="<?= $level['id'] ?>" data-code="medium"></a>
                                        <a class="plp-high" data-id="<?= $level['id'] ?>" data-code="high"></a>
                                    </div>
                                </td>
                            <?php }
                            ?>
                        </tr>
                        <tr id="rst-block" class="hide">
                            <td colspan="<?= count($levels) ?>">
                                <img src="<?= base_url('/assets/img/bx_loader.gif') ?>" class="wait-img hide"/>
                                <ul id="question_list"></ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#collapse3">
            <h4 class="panel-title"><i class="fa fa-caret-<?= $active_box == 'sharing' ? "down" : "right" ?>"></i><a><?= lang('q_sharing_box') ?></a></h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse<?= $active_box == 'sharing' ? " in" : "" ?>">
            <div class="panel-body">
                <table id="table" class="ql-list" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
                    <thead>
                        <tr>
                            <th width="27%" class="username-clr">
                                <span class="search_area">
                                  <input type="text" placeholder="Username" name="search_text"/>
                                  <input type="submit" value=""/>
                                </span>
                            </th>
                            <?php
                            foreach ($levels as $level) {
                                echo '<th width="10%">' . lang('q_' . $level['label'] . '_title') . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($granted_perms as $uid => $perms) :
                            if (!isset($friends_list[$uid])) {
                                continue;
                            }
                            foreach ($perms as $pid => $perm) :
                                $friend = $friends_list[$uid];
                                ?>
                                <tr>
                                    <td>
                                        <img class="profile-img" src="<?= $friend['avatars']['forum'] ?>" alt="forum image..."/>
                                        <span class="user-name"><?= $friend['username'] ?></span>
                                    </td>
                                    <?php
                                    $privacy = $perm['privacy'][$type];
                                    foreach ($levels as $level) {
                                        $lnum = $level['level'];
                                        if (isset($privacy[$lnum]) && $privacy[$lnum]) {
                                            ?>
                                            <td align="center">
                                                <div class="profile-level-privacy">
                                                    <a class="plp-low<?= in_array('low', $privacy[$lnum]) ? " active" : "" ?>" onclick="return false;" data-code="low"></a>
                                                    <a class="plp-medium<?= in_array('medium', $privacy[$lnum]) ? " active" : "" ?>" onclick="return false;" data-code="medium"></a>
                                                    <a class="plp-high<?= in_array('high', $privacy[$lnum]) ? " active" : "" ?>" onclick="return false;" data-code="high"></a>
                                                </div>
                                                <div class="edit-links" data-id="<?= $pid ?>" data-lnum="<?= $lnum ?>" data-qtype="<?= $type ?>">
                                                    <a class="edit"><?= lang('btn_edit') ?></a>
                                                    <a class="save hide"><?= lang('btn_save') ?></a>
                                                    <a class="cancel hide" data-code="<?= implode('|', $privacy[$lnum]) ?>"><?= lang('btn_cancel') ?></a>
                                                </div>
                                            </td>
                                            <?php
                                        } else {
                                            echo '<td></td>';
                                        }
                                    }
                                    ?>
                                </tr>
                                <?php
                            endforeach;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#collapse4">
            <h4 class="panel-title"><i class="fa fa-caret-<?= $active_box == 'requests' ? "down" : "right" ?>"></i><a><?= lang('q_requests_box') ?></a></h4>
        </div>
        <div id="collapse4" class="panel-collapse collapse<?= $active_box == 'requests' ? " in" : "" ?>">
            <div class="panel-body">
                <table id="table" class="ql-list" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
                    <thead>
                        <tr>
                            <th width="27%" class="username-clr">
                                <span class="search_area">
                                  <input type="text" placeholder="Username" name="search_text"/>
                                  <input type="submit" value=""/>
                                </span>
                            </th>
                            <?php
                            foreach ($levels as $level) {
                                echo '<th width="10%">' . lang('q_' . $level['label'] . '_title') . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnaire_requests as $r) { ?>
                            <tr>
                                <td>
                                    <img class="profile-img" src="<?= $r['ava'] ?>" alt="forum image..."/>
                                    <span class="user-name"><?= $r['username'] ?></span>
                                </td>
                                <?php
                                foreach ($levels as $level) {
                                    $req = isset($r['levels'][$level['level']]) ? $r['levels'][$level['level']] : array('id' => '', 'codes' => array());
                                    if ($req['id']) {
                                        ?>
                                        <td align="center" id="qreq-<?= $req['id'] ?>">
                                            <div class="profile-level-privacy">
                                                <a class="plp-low<?= in_array('low', $req['codes']) ? " active" : "" ?>" onclick="return false;"></a>
                                                <a class="plp-medium<?= in_array('medium', $req['codes']) ? " active" : "" ?>" onclick="return false;"></a>
                                                <a class="plp-high<?= in_array('high', $req['codes']) ? " active" : "" ?>" onclick="return false;"></a>
                                            </div>
                                            <div class="req-btns">
                                                <a class="accept" onclick="responseToRequest('question_privacy', 'accept', <?= $req['id'] ?>); return false;"><?= lang('btn_accept') ?></a>
                                                <a class="decline" onclick="responseToRequest('question_privacy', 'decline', <?= $req['id'] ?>); return false;"><?= lang('btn_decline') ?></a>
                                            </div>
                                        </td>
                                        <?php
                                    } else {
                                        echo '<td></td>';
                                    }
                                }
                                ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script type="text/javascript">
<?php
if ($active_box == "requests") {
    echo 'var active_box = "#collapse4"';
} elseif ($active_box == "sharing") {
    echo 'var active_box = "#collapse3"';
} elseif ($active_box == "privacy") {
    echo 'var active_box = "#collapse2"';
} else {
    echo 'var active_box = ""';
}
?>
</script>