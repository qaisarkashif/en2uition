<?php
$photo_count = isset($photos) ? count($photos) : -1;
?>
<div class="main">
    <?php
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
                            if($photo['id'] == $curpid) { $cpsi = $i; }
                            echo '<img src="' . $photo['medium'] . '" data-realhref="' . $photo['orig'] . '" id="alb-photo-' . $photo['id'] . '" alt="" data-id="' . $photo['id'] . '" />';
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
            <div class="bridge-privacy visitor-photo">
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
        <div class="col-xs-6 col-sm-3 right-side fixed-block visitor-photo">
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
        </div>
    <?php else : ?>
            <div class="no-photo-available">
                <a class="btn btn-primary btn-first-album" onclick="return false;"><?= lang('error_no_photos_to_view') ?></a>
            </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    <?php
    if(isset($csi)) { echo 'var csi = "' . $csi . '";'; }//current slide index
    if(isset($cpsi)) { echo 'var cpsi = "' . $cpsi . '";'; }//current photo slide index
    ?>
    var visitor_id = '<?= isset($visitor_id) ? $visitor_id : '' ?>';
</script>