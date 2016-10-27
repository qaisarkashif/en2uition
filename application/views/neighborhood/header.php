<div class="header">
    <div class="container">
        <div class="pull-left navigation">
            <ul class="nh-nav">
                <li class="logo">
                    <a href="/homepage"><span><?= lang('home') ?></span></a>
                </li>
                <li class="pro-shape">
                    <img src="<?= base_url('/assets/img/shapes/shape/' . $user['shapename'] . '.png') ?>" width="35" class="shape-<?= $user['color'] ?>"/>
                </li>
                <li class="nh-shape<?= isset($shape) ? " disabled " : "" ?>">
                    <img src="<?= base_url('/assets/img/shapes/shape/' . (isset($shape) ? $shape : "undefined") . '.png') ?>" width="35" class="shape-<?= $top_menu ?>" alt=""/>
                </li>
                <li class="green<?php if ($top_menu == 'green') echo ' active'; ?>">
                    <a href="/neighborhood/green"><img src="<?= base_url('/assets/img/neighborhood/nh-green.png') ?>" alt=""/></a>
                </li>
                <li class="red<?php if ($top_menu == 'red') echo ' active'; ?>">
                    <a href="/neighborhood/red"><img src="<?= base_url('/assets/img/neighborhood/nh-red.png') ?>" alt=""/></a>
                </li>
                <li class="blue<?php if ($top_menu == 'blue') echo ' active'; ?>">
                    <a href="/neighborhood/blue"><img src="<?= base_url('/assets/img/neighborhood/nh-blue.png') ?>" alt=""/></a>
                </li>
                <li class="yellow<?php if ($top_menu == 'yellow') echo ' active'; ?>">
                    <a href="/neighborhood/yellow"><img src="<?= base_url('/assets/img/neighborhood/nh-yellow.png') ?>" alt=""/></a>
                </li>
                <?php if($active_page == 'new_topic' || $active_page == 'topic') : ?>
                    <li class="selected-topic no-hover">
                        <span><?= lang('topic') ?>:&nbsp;</span>
                        <a href="/neighborhood/<?= $top_menu ?>" id="topic-title">
                            <?= isset($topic_title) ? (strlen($topic_title) > 30 ? substr($topic_title, 0, 30) . '...' : $topic_title) : ""; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="pull-right navigation no-bdr">
            <ul>
                <?php if (isset($search_by) && $search_by == "username") { ?>
                    <li class="username-pro-img">
                        <a href="<?= $is_visitor ? "/visitor/uid-{$uid}" : "/profile" ?>" class="pic"><img src="<?= $ava ?>" alt="ava" width="33" height="33"/></a>
                    </li>
                    <?php
                }
                if ($active_page == 'neighborhood') {
                    ?>
                    <li>
                        <form name="search" method="post" class="header-forum-search" onsubmit="return false;" autocomplete="off">
                            <?php if (!isset($search_by)) { ?>
                                <input type="text" name="topic" placeholder="<?= lang('placeholders_search_topics') ?>" value="<?= isset($input_topic_val) ? $input_topic_val : "" ?>"/>
                                <?php foreach (array('country', 'state', 'city') as $i) { ?>
                                    <select id="<?=$i?>-filter" name="<?=$i?>">
                                        <option value=""><?= lang($i) ?></option>
                                        <?php
                                        foreach (${$i} as $k => $j) {
                                            echo '<option value="' . $j[$i] . '"' . ($j[$i] == ${'cur_'.$i} ? " selected" : "") . '>' . $j[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                <?php } ?>
                                <a class="btn btn-sm btn-search btn-default"><?= lang('btn_search') ?></a>
                            <?php } else if ($search_by == "username") { ?>
                                <input type="text" name="username" class="username" placeholder="<?= lang('placeholders_username') ?>" value="<?= isset($search_text) ? $search_text : "" ?>" autocomplete="off"/>
                                <input type="button" name="btnFind" value="<?= lang('my_topics') ?>" class="btn btn-green my_topic" onclick="window.location = '/neighborhood/username/<?= urlencode($user['username']) ?>';"/>
                            <?php } else { ?>
                                <input type="text" name="topic" class="by-date" placeholder="<?= lang('placeholders_topic_title') ?>" value="<?= isset($input_topic_val) ? $input_topic_val : "" ?>"/>
                                <select class="post-date" data-color="<?= $top_menu ?>" data-shape="<?= $shape ?>">
                                    <option value=""><?= lang('date_posted') ?></option>
                                    <?php
                                    foreach ($dates as $date) {
                                        $val = str_replace('-', '.', $date['d']);
                                        echo '<option value="' . $val . '" ' .($search_text == $val ? 'selected' : ''). '>' . $date['d'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="button" name="btnFind" value="<?= lang('my_topics') ?>" class="btn btn-green my_topic" onclick="window.location='/neighborhood/date/<?= "{$search_text}/{$top_menu}/{$shape}/my_topics" ?>'; "/>
                            <?php } ?>
                        </form>
                    </li>
                    <?php
                } elseif ($active_page == "new_topic" || $active_page == "topic") {
                    ?>
                    <li>
                        <div class="search_area">
                            <input type="text" placeholder="<?= lang('placeholders_search') ?>" />
                            <input type="submit" value="" />
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>