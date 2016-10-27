<?php $is_my_friend = isset($my_friend) && $my_friend; ?>
<div class="main">
    <input type="hidden" id="profile-id" value="<?= $user['id'] ?>"/>
    <div class="row top-block">
        <div class="col-xs-4 col-sm-4 profile-img-slider">
            <div class="side-block">
                <div class="block-title"><?= lang('public_photos') ?></div>
                <div class="block-content">
                    <div class="profile-img shared-photos-slider">
                        <div class="slider">
                            <?php foreach($shared_photos as $sfoto) : ?>
                                <div class="slide">
                                    <div>
                                        <a class="magnific-popup" href="<?= set_image($sfoto['original']) ?>">
                                            <img alt="shared photo" src="<?= set_image($sfoto['medium']) ?>"/>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 main-profile-img">
            <div class="side-block">
                <div class="block-title visitor-name"><?= $user['username'] ? $user['username'] : "<span>&nbsp;</span>" ?></div>
                <div class="block-content">
                    <div id="profile-image-container">
                        <a class="magnific-popup" href="<?= set_image($avatar_original) ?>">
                            <img alt="profile image here..." src="<?= set_image($avatar_preview) ?>"/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 profile-friend-slider">
            <div class="side-block">
                <div class="block-title"><?= lang('friends') ?></div>
                <div class="block-content">
                    <div class="friends">
                        <div class="slider">
                            <?php foreach ($friends['list'] as $fid => $friend) : ?>
                                <div class="slide">
                                    <div>
                                        <a href="/visitor/uid-<?=$fid?>">
                                            <img alt="" src="<?=set_image($friend['avatars']['profile'])?>" title="<?= $friend['username'] ?>" class="username-tooltip"/>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="row">
        <div class="col-xs-6 col-sm-3 left-side fixed-block">
            <div class="side-block pull-left user-group">
                <div class="block-title"><?= lang('group') ?></div>
                <div class="block-content">
                    <div class="profile-shape">
                        <img src="<?= base_url('/assets/img/shapes/shape/' . (!empty($user['shapename']) ? $user['shapename'] : 'undefined') . '.png') ?>" class="shape-<?=$user['color']?>" alt=""/>
                    </div>
                </div>
            </div>
            <div class="side-block pull-right view">
                <div class="block-title"><?= lang('view') ?></div>
                <div class="block-content">
                    <?php if($is_my_friend) { ?>
                        <a href="<?= site_url('/visitor/photo/uid-' . $user['user_id']) ?>" class="btn-dblue"><?= lang('photos') ?></a>
                        <a href="<?= site_url('/questionnaire/answers/shared') ?>" class="btn-dblue"><?= lang('answers') ?></a>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="side-block">
                <div class="block-title"><?= lang('dailymood_title') ?></div>
                <div class="block-content pro-mood">
                    <div class="daily-mood"><?php if ($user['dailymood_hidden'] == 0) { ?><hr style="margin-left:<?=$user['dailymood'];?>px;"><?php } ?></div>
                </div>
            </div>
            <div class="side-block">
                <div class="block-title"><?= lang('personal') ?></div>
                <div class="block-content">
                    <ul class="pro-info">
                        <li><?= lang('living_in') ?>: <span><?=implode(', ', array_filter(array($user['country'], $user['state'], $user['city'])))?></span></li>
                        <li><?= lang('birthday') ?>: <span><?=!empty($user['birthday']) ? date("m/d/Y", strtotime($user['birthday'])) : ""?></span></li>
                        <li><?= lang('education') ?>: <span><?=$user['edu_title']?></span></li>
                        <li><?= lang('gender') ?>: <span><?=$user['gender_title']?></span></li>
                        <li><?= lang('sexual_orientation') ?>: <span><?=$user['sex_ori_title']?></span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 comments comments-block">
            <div class="side-block profile-comments<?= !$is_my_friend ? ' no-friend' : "" ?>">
                <div class="block-title"><?= lang('discussing') . " ..." ?></div>
                <div class="block-content">
                    <?php if($is_my_friend) { ?>
                        <div class="comments-box">
                            <div class="wait hide">
                                <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..."/>
                            </div>
                        </div>
                    <?php } else { ?>
                        <h3 style="text-align: center;"><?= str_replace("#username#", $user['username'], lang('only_friends_can_view_content')) ?></h3>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3  right-side fixed-block">
            <div class="side-block">
                <div class="block-title questionnaire-slider visitor">
                    <div class="slider">
                        <div class="slide" data-v="past">
                            <div class="que-name"><?= lang('questionnaire_past') . " " . strtolower(lang('questionnaire')) ?></div>
                        </div>
                        <div class="slide" data-v="present">
                            <div class="que-name"><?= lang('questionnaire_present') . " " . strtolower(lang('questionnaire')) ?></div>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <?php
                    $hide = false;
                    foreach ($questionnaire_progress as $type => $levels) :
                        ?>
                        <ul class="level-info visitor-info<?= $hide ? ' hide' : '' ?>" id="<?= $type ?>-info">
                            <?php
                            foreach ($levels as $lnum => $lvl) :
                                ?>
                                <li>
                                    <div class="level-ans-que"><?= $lvl['title'] . '<br>' . $lvl['progress'] ?></div>
                                    <div class="level-privacy">
                                        <?php if($is_my_friend) : ?>
                                            <div class="profile-level-privacy">
                                                <a class="plp-low" data-code="low"><?= lang('privacy_low') ?></a>
                                                <a class="plp-medium" data-code="medium"><?= lang('privacy_medium') ?></a>
                                                <a class="plp-high" data-code="high"><?= lang('privacy_high') ?></a>
                                            </div>
                                            <a class="request" data-uid="<?= $user['user_id'] ?>" data-qtype="<?= $type ?>" data-lnum="<?= $lnum ?>"><?= lang('request') ?></a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <?php
                            endforeach;
                            ?>
                        </ul>
                        <?php
                        $hide = true;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    var visitor_id = '<?= isset($user['user_id']) ? $user['user_id'] : '' ?>';
</script>