<div class="neighborhood-list">
    <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">
        <thead>
            <tr>
                <th width="9%"><?= lang('date_posted') ?></th>
                <th width="45%"><?= lang('topic') ?></th>
                <th width="5%"><?= lang('users') ?></th>
                <th width="5%"><?= lang('posts') ?></th>
                <th width="5%" class="nopad" style="text-align:center;"><?= lang('shares') ?></th>
                <th width="15%"><?= lang('last_post') ?></th>
            </tr>
        </thead>
    </table>
    <div class="nbr-data usr-hgt">
        <table id="table" width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#e3e3e3">	
            <tbody>
                <?php 
                foreach ($topics as $topic) {
                    ?>
                    <tr data-color="<?= $topic['row_color'] ?>" data-shape="<?= $topic['row_shape'] ?>" data-id="<?= $topic['id'] ?>">
                        <td width="9%"><?= $topic['date_posted'] ?></td>
                        <td width="45%" class="topic-text">
                            <a href="<?= sprintf("/forum/topic-%s/%s/%s", $topic['id'], $topic['row_color'], $topic['row_shape']) ?>"><?= $topic['title'] ?></a>
                        </td>
                        <td width="5%" align="center" class="nopad"><?= $topic['users_count'] ?></td>
                        <td width="5%" align="center" class="nopad"><?= $topic['posts_count'] ?></td>
                        <td width="5%" align="center" class="nopad"><?= $topic['shares_count'] ?></td>
                        <td width="15%">
                            <a href="<?= sprintf("/neighborhood/date/%s/%s/%s", $topic['last_post_url'], $topic['row_color'], $topic['row_shape']) ?>"><?= $topic['last_post_text'] ?></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
	</div>    
    <div class="wait hide">
        <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..."/>
    </div>
</div>