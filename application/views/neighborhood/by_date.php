<div class="neighborhood-list">
    <?php 
    $key1 = $my_color . $my_shape_id; 
    $key2 = $color2 . $shape2_id;
    ?>
    <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
        <thead>
            <tr>
                <th width="3%" class="nopad" id="list-shape-<?= $my_color ?>">
                    <img class="nei-img shape-<?= $my_color ?>" src="<?= base_url('/assets/img/shapes/shape/' . $my_shape . '.png') ?>" width="35" alt=""/>
                    <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..." class="wait-shape hide"/>
                </th>
                <th width = "20%" class = "username-clr"><?= lang('username') ?></th>
                <th width = "12%"><?= lang('date_posted') ?></th>
                <th width = "26%"><?= lang('topic') ?></th>
                <th width = "7%"><?= lang('users') ?></th>
                <th width = "7%"><?= lang('posts') ?></th>
                <th width = "7%"><?= lang('shares') ?></th>
                <th width = "18%"><?= lang('last_post') ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="nbr-data mn-data <?= $key2 != $key1 ? 2 : '' ?>">
        <table id = "table" width = "100%" cellpadding = "0" cellspacing = "0" border = "1" bordercolor = "#e3e3e3">
            <thead></thead>
            <tbody class="<?= $key1 ?>">
                <?php
                if (isset($rows[$key1])) :
                    foreach ($rows[$key1] as $row) :
                        ?>
                        <tr>
                            <td width="3%" class="nopad" align="center"><img src="<?= $row['ava'] ?>" alt="usr-img" width="34"/></td>
                            <td width = "20%" class="user-name">
                                <a href="/neighborhood/username/<?= urlencode($row['username']) ?>"><?= $row['username'] ?></a>
                            </td>
                            <td width = "12%"><?= $row['created_datetime'] ?></td>
                            <td width = "26%"><a href="<?= sprintf("/forum/topic-%s/%s/%s", $row['id'], $color2, $shape2) ?>">“<?= $row['title'] ?>”</a></td>
                            <td width = "7%" align="center" class="nopad"><?= $row['users_count'] ?></td>
                            <td width = "7%" align="center" class="nopad"><?= $row['posts_count'] ?></td>
                            <td width = "7%" align="center" class="nopad"><?= $row['shares_count'] ?></td>
                            <td width = "18%"><a href="<?= sprintf("/neighborhood/date/%s/%s/%s", $row['last_post1'], $color2, $shape2) ?>"><?= $row['last_post2'] ?></a></td>
                        </tr>
                        <?php
                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($key2 != $key1) {
        ?>
        <table id = "table" width = "100%" cellpadding = "0" cellspacing = "0" border = "1" bordercolor = "#e3e3e3">
            <thead>
                <tr>
                    <th width = "3%" class = "nopad" id = "list-shape-<?= $color2 ?>">
                        <img class="nei-img shape-<?= $color2 ?>" src="<?= base_url('/assets/img/shapes/shape/' . $shape2 . '.png') ?>" width = "35" alt = ""/>
                        <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..." class="wait-shape hide"/>
                    </th>
                    <th width = "20%" class = "username-clr"><?= lang('username') ?></th>
                    <th width = "12%"><?= lang('date_posted') ?></th>
                    <th width = "26%"><?= lang('topic') ?></th>
                    <th width = "7%"><?= lang('users') ?></th>
                    <th width = "7%"><?= lang('posts') ?></th>
                    <th width = "7%"><?= lang('shares') ?></th>
                    <th width = "18%"><?= lang('last_post') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="nbr-data usr-hgt2">
            <table id = "table" width = "100%" cellpadding = "0" cellspacing = "0" border = "1" bordercolor = "#e3e3e3">
                <thead></thead>
                <tbody class="<?= $key2 ?>">
                    <?php
                    if (isset($rows[$key2])) :
                        foreach ($rows[$key2] as $row) :
                            ?>
                            <tr>
                                <td width="3%" class="nopad" align="center"><img src="<?= $row['ava'] ?>" alt="usr-img" width="34"/></td>
                                <td width = "20%" class="user-name">
                                    <a href="/neighborhood/username/<?= $row['encoded_username'] ?>"><?= $row['username'] ?></a>
                                </td>
                                <td width = "12%"><?= $row['created_datetime'] ?></td>
                                <td width = "26%"><a href="<?= sprintf("/forum/topic-%s/%s/%s", $row['id'], $color2, $shape2) ?>">“<?= $row['title'] ?>”</a></td>
                                <td width = "7%" align="center" class="nopad"><?= $row['users_count'] ?></td>
                                <td width = "7%" align="center" class="nopad"><?= $row['posts_count'] ?></td>
                                <td width = "7%" align="center" class="nopad"><?= $row['shares_count'] ?></td>
                                <td width = "18%"><a href="<?= sprintf("/neighborhood/date/%s/%s/%s", $row['last_post1'], $color2, $shape2) ?>"><?= $row['last_post2'] ?></a></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>