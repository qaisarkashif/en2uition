<?php
$lang_colors = lang('colors');
$tmp_colors = $tmp_shapes = array();
foreach ($colors as $color) {
    $tmp_colors[] = strtolower($lang_colors[$color]);
}
foreach ($shapes as $shape) {
    $tmp_shapes[] = strtolower(str_replace('_', '-', $shape['name']));
}
$rpl1_what = array(
    '#colors_count#',
    '#shapes_count#',
    '#colors_list#',
    '#shapes_list#'
);
$rpl1_to = array(
    count($tmp_colors),
    count($tmp_shapes),
    implode(', ', array_values($tmp_colors)),
    implode(', ', array_values($tmp_shapes))
);
?>
<div class="col-xs-12 col-sm-12">
    <div class="group-description">
        <h2><?= lang('gd_title') ?>:</h2>
        <ul>
            <?= str_replace($rpl1_what, $rpl1_to, lang('gd_top_content')) ?>
            <select name="color" class="gd-clr">
                <?php
                foreach ($colors as $color) {
                    echo '<option class="' . $color . '" value="' . $color . '"' . ($my_color == $color ? ' selected' : '') . '>' . strtoupper($lang_colors[$color]) . '</option>';
                }
                ?>
            </select>
            <p class="color-descr"><?= $color_descr ?></p>
            </li>
            <li><?= lang('gd_choose_shape') ?>
                <select name="shape" class="gd-shape">
                    <?php
                    foreach ($tmp_shapes as $shape) {
                        echo '<option value="' . $shape . '"' . ($my_shape == $shape ? ' selected' : '') . '>' . $shape . '</option>';
                    }
                    ?>
                </select>
                <p class="shape-descr"><?= $shape_descr ?></p>
            </li>
            <li>
                <span>
                    <?= lang('gd_choose_group') ?> &nbsp;
                    <select name="color" class="gd-clr go">
                        <?php
                        foreach ($colors as $color) {
                            echo '<option class="' . $color . '" value="' . $color . '"' . ($my_color == $color ? ' selected' : '') . '>' . strtoupper($lang_colors[$color]) . '</option>';
                        }
                        ?>
                    </select>
                    &nbsp;
                    <select name="shape" class="gd-shape go">
                        <?php
                        foreach ($tmp_shapes as $shape) {
                            echo '<option value="' . $shape . '"' . ($my_shape == $shape ? ' selected' : '') . '>' . $shape . '</option>';
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;<?= lang('and') ?>
                </span>
                <span><?= lang('gd_talk_in_discussion') ?> &nbsp;<a href="/neighborhood" class="go-neighborhood"><?= (lang('btn_go')) ?></a></span>
                <span><?= lang('gd_compare_with_group') ?> &nbsp;<a href="/compare" class="go-compare"><?= (lang('btn_go')) ?></a></span>
                <span style="margin-top:25px;"><a href="/homepage"><?= lang('btn_go_homepage') ?></a></span>
            </li>
        </ul>
    </div>
</div>
