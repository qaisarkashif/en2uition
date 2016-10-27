<?php
$photo_count = isset($photos) ? count($photos) : -1;
?>
<div class="main">
    <?php
    if ($this->session->flashdata('msg')) {
        $msg = $this->session->flashdata('msg');
        ?>
        <div style="padding: 15px;">
            <div class="alert alert-<?= $msg['status'] ?> alert-dismissible" role="alert" style="margin: 0;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong><?= ucfirst($msg['status']) ?>!</strong> <?= $msg['text'] ?>
            </div>
        </div>
        <?php
    }
    if ($albums_count > 0) :
        ?>
        <div class="col-xs-12 col-sm-12">
            <div class="album-privacy-slider">
                <div class="slider">
                    <?php
                    $csi = 0;//current slide index
                    foreach ($albums as $i => $album) {
                        if($album['id'] == $curaid) {
                            $csi = $i;
                        }
                        ?>
                        <div class="slide" data-id="<?= $album['id'] ?>">
                            <div class="album-number"><?= lang('album') ?> <span><?= ($i + 1); ?></span></div>
                            <div class="album-name"><?= $album['title']; ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="clear"></div>
            <div class="album-photo-carousel<?= $photo_count > 0 ? '' : ' hidden' ?>">
                <div id="waterwheelCarousel">
                    <?php
                    if ($photo_count > 0) {
                        $cpsi = 0;
                        foreach ($photos as $i => $photo) {
                            if($photo['id'] == $curpid) {
                                $cpsi = $i;
                            }
                            echo '<img src="' . $photo['medium'] . '" id="alb-photo-' . $photo['id'] . '" alt="" data-pr="' . $photo['privacy_code'] .
                                    '" data-realhref="' . $photo['orig'] . '" data-id="' . $photo['id'] . '" data-shared="' . $photo['shared'] . '" />';
                        }
                    }
                    ?>
                </div>
                <div class="ww-control">
                    <a class="btn-right"><img src="<?= base_url('/assets/img/btn-next.png') ?>" alt="next"></a>
                    <a class="btn-left"><img src="<?= base_url('/assets/img/btn-prev.png') ?>" alt="prev"></a>
                </div>
            </div>
            <div class="no-photo-available<?= $photo_count > 0 ? ' hidden' : '' ?>">
                <a class="btn btn-primary btn-first-album" onclick="return false;"><?= lang('error_no_photos_to_view') ?></a>
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-xs-6 col-sm-3 left-side fixed-block">&nbsp;</div>
        <div class="col-xs-12 col-sm-6 comments photo-page">
            <div class="bridge-privacy">
                <div class="bridge-heading">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <span class="photo-title"></span>
                </div>
                <div class="photo-like-box">
                    <ul>
                        <li><a class="total-dislike cmt-tooltip" title="voted -1">-0</a></li>
                        <li class="dislike" onClick="vote(this, 'photo', 'dislike', 0);"><a>&nbsp;</a></li>
                        <li class="like" onClick="vote(this, 'photo', 'like', 0);"><a>&nbsp;</a></li>
                        <li><a class="total-like cmt-tooltip" title="voted +1">+0</a></li>
                    </ul>
                </div>
            </div>
            <div class="comments-box">
                <div class="wait">
                    <img src="<?= base_url('/assets/img/waiting.gif') ?>" alt="loading..."/>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 right-side fixed-block">
            <div class="privacy_area" id="privacy">
                <div class="privacy_heading"><?= lang('privacy') ?></div>
                <div class="privacy_opts">
                    <a class="pr-low" href="/photo/privacy/update" data-pr="low"><?= lang('privacy_low') ?></a>
                    <a class="pr-medium" href="/photo/privacy/update" data-pr="medium"><?= lang('privacy_medium') ?></a>
                    <a class="pr-high" href="/photo/privacy/update" data-pr="high"><?= lang('privacy_high') ?></a>
                </div>
            </div>
            <div class="pagination_area" id="pagination">
                <div class="paging">
                    <p><?= lang('photo') ?>: <input class="variable_page_number" type="text" value="1"/> <?= lang('of') ?> &nbsp;&nbsp;<span><?= $photo_count ?></span></p>
                </div>
                <div class="pagenav">
                    <a class="btn-first full_step_backward"><i class="fa fa-step-backward"></i></a>
                    <a class="btn-left step_backward"><i class="fa fa-caret-left"></i></a>
                    <a class="btn-right step_forward"><i class="fa fa-caret-right"></i></a>
                    <a class="btn-last full_step_forward"><i class="fa fa-step-forward"></i></a>
                </div>
            </div>
            <div class="photo-menu">
                <ul>
                    <li class="more"><a><?= lang('btn_more') ?></a>
                        <ul>
                            <li id="lnk-share-photo" class="<?= $photo_count > 0 ? '' : 'hidden' ?>"><a href="/photo/share"></a></li>
                            <li><a href="/photo/privacy/edit"><?= lang('color_privacy') ?></a></li>
                            <li id="lnk-upload-photo"><a href="/photo/album-<?= $curaid ?>/edit"><?= lang('btn_upload_photo') ?></a></li>
                            <li id="lnk-delete-photo" class="<?= $photo_count > 0 ? '' : 'hidden' ?>"><a href="/photo/delete"><?= lang('btn_delete_photo') ?></a></li>
                            <li><a href="/photo/album"><?= lang('all_albums') ?></a></li>
                            <li><a href="/photo/album/new"><?= lang('btn_new_album') ?></a></li>
                            <li id="lnk-delete-album"><a href="/photo/album-<?= $curaid ?>/delete"><?= lang('btn_delete_album') ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="first-album pos-fixed">
            <a href="/photo/album/new" class="btn btn-primary btn-first-album"><?= lang('btn_create_first_album') ?></a>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    <?php
    if(isset($csi)) { echo 'var csi = "' . $csi . '";'; }
    if(isset($cpsi)) { echo 'var cpsi = "' . $cpsi . '";'; }
    ?>
</script>