<div class="container">
    <div class="logo">
        <a href="<?= site_url('homepage'); ?>" title="<?= lang("home") ?>"><span><?= lang("home") ?></span></a>
    </div>
    <div class="navigation pull-right">
        <ul>
            <?php if($is_joined) { ?>
                <li><a href="/profile"><?= lang("profile") ?></a></li>
            <?php } ?>
            <li><a href="/questionnaire/<?= $q_type ?>"><?= lang("q_all_levels") ?></a></li>
            <?php
            $access = true;
            foreach ($levels as $level) {
                $onclick = !$access ? 'return false;' : '';
                $title = !$access ? lang('q_answer_previous_before') : '';
                echo '<li' . ($cur_lvl == $level['level'] ? ' class="selected"' : "") . '>';
                if ($level['type'] == 'past' && $level['level'] == 7) {
                    echo '<a href="' . site_url('/outcome') . '" onclick="' . $onclick . '" title="' . $title . '" class="cmt-tooltip">' . lang('q_outcome_title') . '</a>';
                } else {
                    echo '<a href="' . site_url(sprintf('questions/%s/l%s/q1', $level['type'], $level['level'])) . '" onclick="' . $onclick . '" title="' . $title . 
                            '" class="cmt-tooltip">' . lang('q_' . $level['label'] . '_title') . '</a>';
                }
                echo '</li>';
                if ($access && $level['quest_count'] > 0 && $level['answered_count'] < $level['quest_count']) {
                    $access = false;
                }
            }
            ?>
        </ul>
    </div>
</div>