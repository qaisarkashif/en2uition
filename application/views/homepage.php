<div class="main">
    <div class="col-xs-6 col-sm-3 left-side fixed-block">
        <div class="side-block" id="user-info">
        	<div class="sblock-body">
                <div class="block-title" id="hmpg-username"><?= $user['username'] ? $user['username'] : "<span>&nbsp;</span>" ?></div>
                <div class="pro-img">
                    <a class="magnific-popup" href="<?= $avatar_original ?>">
                        <img src="<?= $avatar_preview ?>" alt="profile image here..."/>
                    </a>
                    <div class="change-avatar">
                        <form id="frm-profile-pct" method="post" enctype="multipart/form-data">
                            <span class="btn btn-info btn-block btn-file">
                                <span class="glyphicon glyphicon-picture" aria-hidden="true"></span> <?= lang('change_photo') ?>
                                <input type="file" name="profile-picture"/>
                            </span>
                        </form>
                    </div>
                </div>
        	</div>
        </div>
        <div class="side-block" id="dm-info">
        	<div class="sblock-body">
                <div class="block-title"><?= lang('dailymood_title') ?></div>
                <div class="block-content">
                	<div class="dm_s_i">
                        <div class="dmi">
                            <a id="dm-switch" data-v="<?= $dailymood_hidden == 0 ? "on" : "off"; ?>" data-texton="<?= lang('on') ?>" data-textoff="<?= lang('off') ?>">
                                <?= $dailymood_hidden == 0 ? lang("on") : lang("off") ?>
                            </a>
                        </div>
                        <div class="daily-mood dmi">
                            <input type="text" class="mood-bar" data-slider="true" value="<?= $dailymood; ?>" data-slider-range="1,185" data-slider-step="1">
                            <input type="hidden" id="dailymood" value="">
                        </div>
                    </div>
                    <ul class="links">
                        <li id="ug_dm_comp"><a href="/compare"><?= lang('btn_compare') ?></a></li>
                    </ul>
                </div>
        	</div>
        </div>
        <div class="side-block" id="g-info">
        	<div class="sblock-body">
                <div class="block-title cmt-tooltip" title="<?= lang('homepage_groups_tooltip') ?>"><?= lang('groups') ?></div>
                <div class="block-content">
                    <div class="trend cmt-tooltip" title="<?= lang('homepage_groups_tooltip') ?>">
                        <div class="tcolor" id="trend-info"><div class="trend-color"></div></div>
                        <div id="shape-type"><img class="shape-red" src="/assets/img/shapes/hexagon.png" alt=""></div>
                        <div class="tshape" id="tshape-info"><div class="trend-shape"></div></div>
                    </div>
                    <ul class="links">
                        <li id="gd-info"><a href="/group-description" class="grp-descr"><?= lang('group_description') ?></a></li>
                    </ul>
                </div>
        	</div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 comments homepage-comments">
        <div class="comments-box">
            <div class="wait hide">
                <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..."/>
            </div>
        </div>
    </div>
    <div class="col-xs-6 col-sm-3 right-side fixed-block">
        <div class="side-block" id="que-info">
        	<div class="sblock-body">
                <div class="block-title"><?= lang('questionnaire') ?></div>
                <div>
                    <div class="grey-bg">
                        <div id="qp-info"><a href="/questionnaire/past"><?= lang('questionnaire_past') ?></a></div>
                        <div id="qpr-info"><a href="/questionnaire/present"><?= lang('questionnaire_present') ?></a></div>
                    </div>
                    <form autocomplete="off" onsubmit="return false;">
                        <div class="questionnaire-requests">
                            <div class="req-past">
                                <div class="req_progress_bar progress_pinfo">
                                    <div class="req_bar_inner_bg"></div>
                                    <label class="req-comp"><?= lang('completed') ?></label>
                                    <input class="knob1" data-thickness=".17" data-step="0.01" data-width="78" data-height="78" value="<?= $progress_q1; ?>" data-fgColor="#d9e889" data-bgColor ="#fff" data-inputColor="#d9e889" data-fontWeight="normal" read-only="true">
                                </div>
                                <div class="req-past-counter qreq_info">
                                    <a href="/questionnaire/past/requests">
                                        <div class="rpast-total" style="visibility: <?= $questionnaire_requests['past'] > 0 ? 'visible' : 'hidden' ?>">
                                            <?= $questionnaire_requests['past'] ?>
                                        </div>
                                        <div class="req-label"><?= lang('requests') ?></div>
                                    </a>
                                </div>
                            </div>
                            <div class="req-seperate">&nbsp;</div>
                            <div class="req-present">
                                <div class="req_progress_bar progress_prinfo">
                                    <div class="req_bar_inner_bg"></div>
                                    <label class="req-comp"><?= lang('completed') ?></label>
                                    <input class="knob2" data-thickness=".17" data-step="0.01" data-width="78" data-height="78" value="<?= $progress_q2; ?>" data-fgColor="#d9e889" data-bgColor ="#fff" data-inputColor="#d9e889" data-fontWeight="normal" read-only="true">
                                </div>
                                <div class="req-present-counter qreq_info">
                                    <a href="/questionnaire/present/requests">
                                        <div class="rpresent-total" style="visibility: <?= $questionnaire_requests['present'] > 0 ? 'visible' : 'hidden' ?>">
                                            <?= $questionnaire_requests['present'] ?>
                                        </div>
                                        <div class="req-label"><?= lang('requests') ?></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="side-block" id="nei-info">
        	<div class="sblock-body">
                <div class="block-title"><?= lang('neighborhoods') ?></div>
                <div class="neighborhood">
                    <?php
                    $neigb_rels = array(
                        'red' => 'serious relationships',
                        'green' => 'single having fun',
                        'yellow' => 'casual relationships',
                        'blue' => 'single and bored'
                    );
                    foreach ($neigb_rels as $color => $rel) {
                        if(!empty($user['shape_id'])) {
                            $cmt_tooltip = str_replace(array("#rel#", "#topic_num#"), array($rel, $unviewed_topics[$color]), lang('neighborhood_tooltip'));
                        } else {
                            $cmt_tooltip = lang('neighborhood_tooltip_no_shape');
                        }
                        ?>
                        <a href="/neighborhood/<?= $color ?>" id="n<?= $color ?>_info" class="nh-<?= $color ?> cmt-tooltip" title="<?= $cmt_tooltip ?>">
                            <span class="counter"><?= $unviewed_topics[$color] ?></span>
                        </a>
                    <?php } ?>
                </div>
        	</div>
        </div>
        <div class="side-block" id="en2-info">
        	<div class="sblock-body">
                <div class="block-title"><?= lang('my_en2uition') ?></div>
                <div class="block-content">
                    <ul class="links">
                        <li id="en21_info"><a href="<?= sprintf("/my-group/%s/%s", $user['color'], $user['shapename']) ?>"><?= lang('my_group') ?></a></li>
                        <li id="en22_info"><a href="<?= $my_cur_relshp_url ?>"><?= lang('my_cur_relshp') ?></a></li>
                        <li id="en23_info"><a href="<?= $my_past_relshp_url ?>"><?= lang('my_past_relshp') ?></a></li>
                    </ul>
                </div>
        	</div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="col-xs-12 col-sm-12 hp-sliders">
        <!--div class="popular-topic">
            <div class="slider">
                <div class="slide"><a href="http://finance.yahoo.com/news/afford-retirement-paris-135402943.html" target="_blank"><img alt="How to afford retirement in Paris" title="How to afford retirement in Paris" src="/assets/img/popular-topics/paris_635x250_1429203931.jpg"></a></div>
                <div class="slide"><a href="http://news.yahoo.com/awesome-spacex-video-shows-rocket-landing-try-crash-223927085.html" target="_blank"><img alt="Amazing video shows SpaceX rocket landing crash" title="Amazing video shows SpaceX rocket landing crash" src="/assets/img/popular-topics/rocketlanding_635x250_1429141924.jpg"></a></div>
                <div class="slide"><a href="http://www.msn.com/en-us/travel/adventuretravel/15-wildest-travel-accommodations-in-the-world/ss-AAb01KH" target="_blank"><img alt="15 wildest travel accommodations in the-world" title="15 wildest travel accommodations in the-world" src="/assets/img/popular-topics/AAaZWJY.img.jpg"></a></div>
                <div class="slide"><a href="http://www.highsnobiety.com/tag/2014-world-cup/" target="_blank"><img alt="facebook year in review 2014 world cup 2014" title="facebook year in review 2014 world cup 2014" src="/assets/img/popular-topics/facebook-year-in-review-2014-world-cup-2014.jpg"></a></div>
                <div class="slide"><a href="http://www.msn.com/en-us/lifestyle/relationships/9-things-you-should-never-ask-of-your-husband/ss-AAaZOWT" target="_blank"><img alt="9 things you should never ask of your husband" title="9 things you should never ask of your husband" src="/assets/img/popular-topics/AAaZP1R.img.jpg"></a></div>
                <div class="slide"><a href="http://www.highsnobiety.com/2014/12/09/facebook-popular-topics-2014/" target="_blank"><img alt="facebook year in review 2014 world cup 2014" title="facebook year in review 2014 world cup 2014" src="/assets/img/popular-topics/facebook-year-in-review-2014-frozen.jpg"></a></div>
            </div>
            <div class="popular-topic-search">
                <form autocomplete="off">
                    <label><?= lang('popular_topics') ?></label>
                    <input type="text" name="keyword" placeholder="<?= lang('placeholders_keyword') ?>"/>
                </form>
            </div>
        </div>
        <div class="suggested-article">
            <div class="slider">
                <div class="slide"><a href="http://www.travelchannel.com/interests/travels-best/articles/travels-best-wonders-of-the-world-2015" target="_blank"><img alt="travels-best-wonders-of-the-world-2015" title="travels-best-wonders-of-the-world-2015" src="/assets/img/articles/carved-city-7-wonders-petra-jordan.jpg.rend.tccom.616.462.jpg"></a></div>
                <div class="slide"><a href="http://www.businessnewsdaily.com/7866-these-3-traits-make-businesses-successful.html" target="_blank"><img alt="3 Things Most Successful Businesses Do Right" title="3 Things Most Successful Businesses Do Right" src="/assets/img/articles/PuzzleCigdem.jpg"></a></div>
                <div class="slide"><a href="http://www.businessnewsdaily.com/7917-fastest-growing-security-threats.html" target="_blank"><img alt="Are You Prepared? This Year's Fastest Growing Security Threats" title="Are You Prepared? This Year's Fastest Growing Security Threats" src="/assets/img/articles/SecurityZajda.jpg"></a></div>
                <div class="slide"><a href="http://www.livescience.com/50409-longer-life-span-anti-inflammatory-genes.html" target="_blank"><img alt="A Longer Life May Lie in Number of Anti-Inflammatory Genes" title="A Longer Life May Lie in Number of Anti-Inflammatory Genes" src="/assets/img/articles/dna-light-150407.jpg"></a></div>
                <div class="slide"><a href="http://www.livescience.com/50298-most-interesting-science-news-articles-of-the-week.html" target="_blank"><img alt="Most Interesting Science News Articles of the Week" title="Most Interesting Science News Articles of the Week" src="/assets/img/articles/plasma-ball.jpg"></a></div>
                <div class="slide"><a href="http://www.livescience.com/50206-happiest-counrties-2014-list.html" target="_blank"><img alt="The Happiest Countries in The World" title="The Happiest Countries in The World" src="/assets/img/articles/happy-smiling-woman.jpg"></a></div>
            </div>
            <div class="suggested-article-search">
                <form autocomplete="off">
                    <label><?= lang('suggested_articles') ?></label>
                    <input type="text" name="keyword" placeholder="<?= lang('placeholders_keyword') ?>"/>
                </form>
            </div>
        </div-->
        <!--div class="profiles">
            <input type="hidden" name="my_id" value="<?= $my_id ?>"/>
            <div class="slider">
                <?php
                foreach ($users as $id => $data) {
                    ?>
                        <div class="slide">
                            <a href="<?= $id == $my_id ? '/profile' : '/visitor/uid-' . $id ?>">
                                <img alt="pro-img" src="<?=$data['foto']?>" title="<?= $data['username'] ?>" class="username-tooltip"/>
                            </a>
                        </div>
                    <?php
                }
                ?>
            </div>
            <div class="profiles-search">
                <form autocomplete="off" onsubmit="return false;">
                    <label><?= lang('profiles') ?></label>
                    <?php foreach ($selects as $k => $v) { ?>
                        <select name="<?= $k ?>" onchange="reloadProfiles();">
                            <option value=""><?= lang($k) ?></option>
                            <?php
                            foreach (${$v} as $item) {
                                echo '<option value="' . $item[$k] . '">' . $item[$k] . '</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                    <select name="color" onchange="reloadProfiles();">
                        <option value=""><?= lang('color') ?></option>
                        <?php
                        $tmp_colors = array();
                        foreach (lang('colors') as $k => $c) {
                            echo '<option value="' . $k . '">' . $c . '</option>';
                            $tmp_colors[] = $k;
                        }
                        ?>
                    </select>
                    <select name="shape" onchange="reloadProfiles();">
                        <option value=""><?= lang('shape') ?></option>
                        <option value="-1">?</option>
                        <?php
                        $tmp_shapes = array();
                        foreach ($shapes as $shape) {
                            echo '<option value="' . $shape['id'] . '">' . $shape['name'] . '</option>';
                            $tmp_shapes[] = strtolower(str_replace('_', '-', $shape['name']));
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div-->
        <div class="clear"></div>
    </div>
</div>

<div id="cropModal" class="modal fade top span8">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="clsbtn"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                <div class="modal-hdr-welcme"><h2><?= lang('cut_profile_photo_title') ?>:</h2></div>
            </div>
            <div class="modal-body">
                <form>
                    <?php
                    foreach (array('x1', 'x2', 'y1', 'y2', 'w', 'h') as $field) {
                        echo '<input type="hidden" name="' . $field . '" id="' . $field . '" value="" class="imgareaselect-inputs"/>';
                    }
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn-primary btn-sm" onclick="cropImage();"><?= lang('btn_save') ?></a>
                <a class="btn-sm btn-danger" onclick="removeAvatar();"><?= lang('btn_delete_photo') ?></a>
            </div>
        </div>
    </div>
</div>
<?php
$guide_lang = lang('user_guide');
if(!$guide_lang) {
    $guide_lang = array();
}
?>
<script type="text/javascript">
    /* Shapes color type selection */
    var colorid = ["<?= implode('", "', $tmp_colors) ?>"];
    var color = ["serious <br> relationship", "casual <br>relationship", "single <br>having fun", "single <br>and bored"];
    var shapeid = ["<?= implode('", "', $tmp_shapes) ?>"];
    var lang = {
        'btn_user_guide' : '<?= lang('users_guide') ?>',
        'btn_quit_guide' : '<?= lang('quit_guide') ?>',
        'guide' : JSON.parse('<?= json_encode($guide_lang) ?>')
    };
</script>