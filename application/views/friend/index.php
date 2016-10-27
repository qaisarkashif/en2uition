<div class="main">
    <div class="friend-list" id="friend-list">
        <?php 
        foreach ($friends_list as $fid => $friend) {
            $past = $present = '-';
            if(isset($stats[$fid])) {
                $past = $stats[$fid]['past'];
                $present = $stats[$fid]['present'];
            }
            ?>
            <div class="friend">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <div class="photo tt-info" onmouseover="show(tooltip<?= $fid; ?>)" onmouseout="hide(tooltip<?= $fid; ?>)">
                                <a href="/visitor/uid-<?= $fid ?>">
                                    <img src="<?= set_image($friend['avatars']['friend']) ?>" alt="profile image here..." width="83" height="83"/>
                                </a>
                                <div class="friend-info ftooltip" id="tooltip<?= $fid; ?>">
                                    <div class="friend-name"><?= $friend['username'] ?></div>
                                    <div class="lvl-rst">
                                        <span><?= lang('questionnaire_past') . ": " . $past ?></span>
                                        <span><?= lang('questionnaire_present') . ": " . $present ?></span>
                                    </div>
                                    <div class="friend-shape-mode">
                                        <div class="friend-mood">
                                            <div class="friend-daily-mood">
                                                <?php if ((int) $friend['dailymood_hidden'] == 0) { ?><hr style="margin-left:<?= $friend['dailymood'] ?>px;"><?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>    
        <?php } ?>
    </div>
</div>