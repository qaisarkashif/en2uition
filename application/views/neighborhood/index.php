<div class="neighborhood-list <?= $top_menu; ?>" data-color="<?= $top_menu; ?>">
    <?php
    $g1 = $my_color . " " . $my_shape_name;
    $lng = lang('two_forum_groups');
    $page = $prt_count > 0 ? 1 : 0;
    foreach ($shapes as $shape) :
        $shid = $shape['id'];
        $accordion = 'data-toggle="collapse" href="#collapse' . $shid . '" aria-expanded="false" aria-controls="collapse' . $shid . '"';
        $g2 = $top_menu . " " . $shape['name'];
        $two_group_tooltip = str_replace(array('#g1#', '#g2#'), array($g1, $g2), $lng);
        $pre_sh = !empty($pre_shape) && $pre_shape == strtolower(str_replace('_', '-', $shape['name']));
        if($pre_sh || (empty($pre_shape) && $cur_shapeid == $shid)) {
            $col_class = ' in';
        } else {
            $col_class = "";
        }
        ?>
        <div id="<?= $shape['name'] ?>" class="nbr hover-effect<?= $pre_sh ? ' pre-shape' : "" ?>">
            <table id="table" class="nbr-table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
                <thead>
                    <tr>
                        <th style="width:34px;" height="25" class="nopad cmt-tooltip" valign="top" <?= $accordion ?> title="<?= $two_group_tooltip ?>">
                            <img class="nei-img hvr-grow shape-<?= $top_menu; ?>" src="<?= base_url('/assets/img/shapes/shape/' . $shape['name'] . '.png') ?>" width="34" alt=""/>
                            <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..." class="wait-shape hide"/>
                        </th>
                        <th style="width:188px;" class="username-clr" <?= $accordion ?>><?= lang('username') ?></th>
                        <th style="width:115px;" <?= $accordion ?>><?= lang('date_posted') ?></th>
                        <th style="width:247px;">
                            <span class="pull-left" <?= $accordion ?>><?= lang('topic') ?></span>
                            <?php if (isset($unviewed_topics[$shid]) && $unviewed_topics[$shid] > 0) : ?>
                                <span class="pull-left unviewed-counter"><?= $unviewed_topics[$shid] ?></span>
                            <?php endif; ?>
                            <span class="pull-right">
                                <a href="<?= sprintf("/new_topic/%s/%s", $top_menu, $shape['name']) ?>" class="btn btn-green btn-new-topic"><?= lang('btn_new_topic') ?></a>
                            </span>
                        </th>
                        <th style="width:67px;" <?= $accordion ?>><?= lang('users') ?></th>
                        <th style="width:67px;" <?= $accordion ?>><?= lang('posts') ?></th>
                        <th style="width:67px;" <?= $accordion ?>><?= lang('shares') ?></th>
                        <th style="width:175px;" <?= $accordion ?>><?= lang('last_post') ?></th>
                    </tr>
                </thead>
            </table>    
            <div class="nbr-data mn-data collapse<?= $col_class ?>" id="collapse<?= $shid ?>" data-shapeid="<?= $shid ?>" data-page="<?= $page ?>">
                <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
                    <tbody class="<?= $top_menu . $shid ?>">
                        <?php
                        foreach ($participants as $p) :
                            ?>
                            <tr<?= in_array($p['id'], $unviewed_topics['ids']) ? ' class="unviewed"' : "" ?>>
                                <td style="width:34px; height:25px;" class="nopad text-center">
                                    <img src="<?= $p['ava'] ?>" alt="usr-img" width="34"/>
                                </td>
                                <td style="width:188px;" class="user-name">
                                    <a href="/neighborhood/username/<?= $p['encoded_username'] ?>"><?= $p['username'] ?></a>
                                </td>
                                <td style="width:115px;"><?= $p['created_datetime'] ?></td>
                                <td style="width:247px;">
                                    <a href="<?= sprintf("/forum/topic-%s/%s/%s", $p['id'], $top_menu, $shape['name']) ?>">“<?= $p['title'] ?>”</a>
                                </td>
                                <td style="width:67px;" class="nopad text-center"><?= $p['users_count'] ?></td>
                                <td style="width:67px;" class="nopad text-center"><?= $p['posts_count'] ?></td>
                                <td style="width:67px;" class="nopad text-center"><?= $p['shares_count'] ?></td>
                                <td style="width:175px;">
                                    <a href="<?= sprintf("/neighborhood/date/%s/%s/%s", $p['last_post1'], $top_menu, $shape['name']) ?>"><?= $p['last_post2'] ?></a>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            </table>
        </div>
        <?php
    endforeach;
    ?>
</div>