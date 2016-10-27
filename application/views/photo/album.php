<div class="main">
    <div class="col-xs-3 col-sm-3 albums-cover">
        <?php foreach ($albums as $album_id => $album) : ?>
            <div class="side-block">
                <div class="block-content">
                    <?php
                    if (count($album['photos']) > 0) {
                        $img = current($album['photos']);
                        echo '<img src="' . $img['thumb'] . '" alt="cover photo"/>';
                    } else {
                        echo '<div class="noimg"></div>';
                    }
                    ?>                    
                    <div class="under-content">
                        <div>
                            <a href="/photo/album-<?= $album_id ?>/edit" class="btn btn-info"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?= lang('add_photo') ?></a>
                        </div>
                    </div>
                </div>
                <div class="block-title" data-id="<?= $album_id ?>">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <span class="album-title"><?= $album['album_title'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="clearfix"></div>
    </div>
    <div class="col-xs-9 col-sm-9 albums-slider">
        <?php foreach ($albums as $album_id => $album) : ?>
            <div class="side-block">
                <div class="block-content">
                    <div class="album-img">
                        <div class="slider">
                            <?php foreach ($album['photos'] as $photo) : ?>
                                <div class="slide">
                                    <div><a href="<?=sprintf("/photo/page/preset/%s/%s", $album_id, $photo['id']) ?>"><img alt="img-<?= $photo['id'] ?>" src="<?= $photo['thumb'] ?>"></a></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>	
    </div>
    <div class="clear"></div>
</div>
