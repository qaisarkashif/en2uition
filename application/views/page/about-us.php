<div class="container" id="about-us">
    <div class="center content-heading">
        <h3><?= lang('about_us_heading') ?></h3>
    </div>
    <div class="members">
        <?php
        $members = lang('about_us_members');
        if ($members) :
            foreach ($members as $member) :
                ?>
                <div class="member">
                    <div class="pull-left">
                        <img src="<?= $member['img'] ?>" alt="member img"/>
                    </div>
                    <div class="pull-left member-info">
                        <h4 class="member-name"><?= $member['name'] ?></h4>
                        <p class="member-position muted"><?= $member['position'] ?></p>
                    </div>
                    <div class="pull-left member-text">
                        <p><?= $member['text'] ?></p>
                    </div>
                </div>
                <?php
            endforeach;
        endif;
        ?>
    </div>
</div>
