<?php $nt = isset($new_topic) && !!$new_topic; ?>
<div class="container">
    <div class="col-xs-12 col-sm-12<?= $nt ? " new-topic" : "" ?>" id="forum-topic">
        <form name="topic" method="post" autocomplete="off">

            <?php if ($nt || (!$nt && count($comments) > 0)) : ?>
                <div id="new-topic-forum"<?= $nt ? ' class="hide"' : "" ?>>
                    <div class="forum-topic-user">
                        <?php
                        if (!$nt) :
                            foreach ($comments as $cid => $comment) :
                                ?>
                                <div id="cmt-<?= $cid ?>" class="user-topics">
                                    <div class="comment-user-box">
                                        <table cellspacing="2" cellpadding="2" width="175">
                                            <tbody>
                                                <tr>
                                                    <td width="70">
                                                        <img class="user-img" alt="" src="<?= set_image($comment['ava']) ?>">
                                                    </td>
                                                    <td class="cmt-user-info"><?= $comment['author'] ?><br><span class="comment-time"><?= $comment['date'] . '<br/>' . $comment['time'] ?></span></td>
                                                </tr>
                                                <tr id="right-lb">
                                                    <td colspan="2">
                                                        <div class="like-box" data-id="<?=$cid?>">
                                                            <ul>
                                                                <li><a onclick="return false;" title="<?= $comment['dislike_tooltip_title'] ?>" class="total-dislike cmt-tooltip">-<?= (int)$comment['dislike_count'] ?></a></li>
                                                                <li onclick="<?php if ($comment['deleted']!=1) { ?>vote(this, 'topic_comment', 'dislike', <?=$cid?>)<?php } else { echo 'return false'; }?>;" class="dislike">
                                                                    <a onclick="return false;"<?= $comment['my_vote'] == 'dislike' ? ' class="r"' : "" ?>></a>
                                                                </li>
                                                                <li onclick="<?php if ($comment['deleted']!=1) { ?>vote(this, 'topic_comment', 'like', <?=$cid?>)<?php } else { echo 'return false'; }?>;" class="like">
                                                                    <a onclick="return false;"<?= $comment['my_vote'] == 'like' ? ' class="g"' : "" ?>></a>
                                                                </li>
                                                                <li><a onclick="return false;" title="<?= $comment['like_tooltip_title'] ?>" class="total-like cmt-tooltip">+<?= (int)$comment['like_count'] ?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="cmt-txt-area" data-topic="<?= $topic_id ?>" data-cid="<?= $cid ?>">
                                        <?php if(!empty($comment['short_text'])) : ?>
                                            <div class="cmt-short-txt">
                                                <span class="txt"><?= $comment['deleted'] == 1 ? '<span class="deleted-comment-text">' . lang('error_comment_deleted_by_user') . '</span>' : $comment['short_text'] ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="cmt-full-txt<?= !empty($comment['short_text']) ? ' hide' : '' ?>">
                                            <span class="txt"><?= $comment['deleted'] == 1 ? '<span class="deleted-comment-text">' . lang('error_comment_deleted_by_user') . '</span>' : $comment['full_text'] ?></span>
                                        </div>
                                        <div class="cmt-btns">
                                            <?php if(!empty($comment['short_text']) && $comment['deleted'] != 1) : ?>
                                                <a class="btn-more"><?= strtolower(lang('btn_more')) ?></a>
                                                <a class="btn-less hide"><?= strtolower(lang('btn_less')) ?></a>
                                            <?php endif; ?>
                                                <a class="btn-expand"><?= strtolower(lang('btn_expand')) ?></a>
                                                <a class="btn-collapse hide"><?= strtolower(lang('btn_collapse')) ?></a>
                                                <a class="btn-thread<?= $comment['deleted'] != 1 ? '' : ' hide' ?>" data-sid="<?= $cid ?>" data-type="<?= !empty($comment['my_share']) ? 'unshare' : 'share' ?>">
                                                    <span class="st"><?= (int) $comment['shares_count'] ?></span>&nbsp;
                                                    <span class="cs"><?= strtolower(lang('shares')) ?></span>
                                                </a>
                                                <a class="btn-reply<?= $comment['deleted'] != 1 ? '' : ' hide' ?>"><?= strtolower(lang('btn_reply')) ?></a>
                                            <?php 
                                                if ($user['user_id'] == $comment['author_id']) {
                                                    if ($comment['deleted'] != 1) {
                                                        echo '<a class="btn-delete" data-id="' . $cid . '">' . strtolower(lang('btn_delete')) . '</a>';
                                                    } else {
                                                        echo '<a class="btn-undelete" data-id="' . $cid . '">' . strtolower(lang('btn_undelete')) . '</a>';
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <div class="forum-reply-user">
                        <?php
                        if (!$nt) :
                            foreach ($comments as $cid => $comment) :
                                ?>
                                <div id="reply-<?= $cid ?>" class="topic-user-reply">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%" data-id="<?= $cid ?>" class="topic-slider-bb" id="topic-slider-<?= $cid ?>" style="display: table;">
                                        <tbody>
                                            <tr>
                                                <td valign="top" class="topic">
                                                    <div class="slider">
                                                        <?php
                                                        if (count($comment['replies']) > 0) {
                                                            foreach ($comment['replies'] as $id => $rp) {
                                                                ?>
                                                                <div class="slide" style="float: left; list-style: none; position: relative; margin-right: 4px; width: 70px;" data-id="<?= $id ?>">
                                                                    <div>
                                                                        <a onclick="showReplies(this, <?= $id ?>, <?= $topic_id ?>);
                                                                                return false;">
                                                                            <img alt="" src="<?= set_image($rp['ava']); ?>"/>
                                                                            <span><?= $rp['rc'] ?></span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        } else {
                                                            for ($s = 1; $s <= 7; $s++) {
                                                                echo '<div class="slide"><div></div></div>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                <div class="clear"></div>
            <?php endif; ?>

            <table id="forum-topic-user" width="100%" cellpadding="0" cellspacing="0" border="0"<?= $nt ? "" : ' class="hide"' ?>>
                <tr>
                    <td width="50%" valign="top">
                        <table width="100%" border="0" style="background:#f6f6f6; border: 2px solid #e3e3e3;">
                            <?php if($nt) : ?>
                                <tr id="topic-title-input">
                                    <td style="padding:3px 10px;border-bottom:2px solid #e3e3e3;">
                                        <?= lang('topic') ?>: <input type="text" class="topic_title" name="topic_title" placeholder="<?= lang('placeholders_topic_title') ?>" maxlength="200"/>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>
                                    <div class="comm-reply thread-2 thread-4">
                                        <div class="comment-user-box">
                                            <table width="175" cellpadding="2" cellspacing="2">
                                                <tr>
                                                    <td width="70"><img src="<?= $ava ?>" alt="" class="user-img" /></td>
                                                    <td class="cmt-user-info"><?= $user['username'] ?><br/><span class="comment-time"><?= lang('now') ?></span></td>
                                                </tr>
                                                <tr id="right-lb">
                                                    <td colspan="2">
                                                        <div class="like-box">
                                                            <ul>
                                                                <li><a class="total-dislike cmt-tooltip" title="<?= lang('voted') ?> -1:" onclick="return false;">-0</a></li>
                                                                <li class="dislike" onClick="return false;"><a onclick="return false;"></a></li>
                                                                <li class="like" onClick="return false;"><a onclick="return false;"></a></li>
                                                                <li><a class="total-like cmt-tooltip" title="<?= lang('voted') ?> +1:" onclick="return false;">+0</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <textarea name="topic" class="reply"></textarea>
                                        <div class="comm-btn">
                                            <button class="publish-new-topic" onclick="<?= $nt ? "addNewTopic('{$color}', '{$shape}');" : "commentTopic(0);" ?>"><?= lang('btn_publish') ?></button>
                                            <a href="<?= isset($referrer) ? $referrer : "" ?>" class="cancel" onclick="<?= !isset($referrer) ? "$('#forum-topic-user').addClass('hide'); return false;" : "" ?>"><?= lang('btn_cancel') ?></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" valign="top">
                        <table width="100%" cellpadding="0" cellspacing="0" border="2" style="margin-top:28px; margin-left:-2px; border: 2px solid #e3e3e3;">
                            <tr>
                                <td id="topic-slider" class="topic" valign="top" style="border-left:none; background:#f6f6f6; width: 100%;">
                                    <div class="slider">
                                        <?php for ($s = 1; $s <= 7; $s++) { ?>
                                            <div class="slide">
                                                <div></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <input type="hidden" id="my-id" value="<?= $user['user_id'] ?>"/>
        </form>
    </div>
</div>

<div id="cmt-txt-area-html" class="hide">
    <div class="cmt-txt-area">
        <div class="cmt-short-txt"><span class="txt"></span></div>
        <div class="cmt-full-txt hide"><span class="txt"></span></div>
        <div class="cmt-btns">
            <a class="btn-more"><?= strtolower(lang('btn_more')) ?></a>
            <a class="btn-less hide"><?= strtolower(lang('btn_less')) ?></a>
            <a class="btn-expand"><?= strtolower(lang('btn_expand')) ?></a>
            <a class="btn-collapse hide"><?= strtolower(lang('btn_collapse')) ?></a>
            <a class="btn-thread"><span class="st">0</span> <span class="cs"><?= strtolower(lang('shares')) ?></span></a>
            <a class="btn-reply"><?= strtolower(lang('btn_reply')) ?></a>
            <a class="btn-delete"><?= strtolower(lang('btn_delete')) ?></a>
        </div>
    </div>
</div>