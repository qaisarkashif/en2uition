<?php if (isset($top_menu) && $top_menu == "homepage") { ?>
	<div id="search-pro-con" class="hp-sliders">	
        <div class="profiles page tab-content" style="margin-bottom:-3px; z-index:999; display:none;">
            <input type="hidden" name="my_id" value="<?= $my_id ?>"/>
            <div class="slider">
                <?php
                foreach ($users as $data) {
                    ?>
                        <div class="slide">
                            <a href="<?= $data['uid'] == $my_id ? '/profile' : '/visitor/uid-' . $data['uid'] ?>">
                                <img alt="pro-img" src="<?=$data['foto']?>" title="<?= $data['username'] ?>" class="username-tooltip"/>
                            </a>
                        </div>
                    <?php
                }
                ?>
            </div>
            <div class="profiles-search<?= count($users) < PROSLD_LIMIT ? ' stop-request' : '' ?>">
                <form autocomplete="off" onsubmit="return false;">
                    <label><?= lang('profiles') ?></label>
                    <?php foreach ($selects as $k => $v) { ?>
                        <select name="<?= $k ?>" onchange="reloadProfiles(true);">
                            <option value=""><?= lang($k) ?></option>
                            <?php
                            foreach (${$v} as $item) {
                                echo '<option value="' . $item[$k] . '">' . $item[$k] . '</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                    <select name="color" onchange="reloadProfiles(true);">
                        <option value=""><?= lang('color') ?></option>
                        <?php
                        $tmp_colors = array();
                        foreach (lang('colors') as $k => $c) {
                            echo '<option value="' . $k . '">' . $c . '</option>';
                            $tmp_colors[] = $k;
                        }
                        ?>
                    </select>
                    <select name="shape" onchange="reloadProfiles(true);">
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
        </div>
</div>
<?php } ?>
<div id="footer" class="front-footer">
    <div class="container">
  		<?php if (isset($top_menu) && $top_menu == "homepage") { ?>
    		<a class="show-profile">Search profiles</a>
        <?php } ?>    
        <?php 
        if (isset($top_menu) && $top_menu != "profile" && $top_menu != "homepage" && $top_menu != "home") {
            $links = lang('frontpage_footer_links');
            ?>
            <ul class="tabs nav-justified">
                <li data-tab="tab-1">
                    <a><?= $links['contact_us'] ?></a>
                </li>
                <li data-tab="tab-2">
                    <a><?= $links['testimonials'] ?></a>
                </li>
                <li data-tab="tab-3">
                    <a><?= $links['privacy'] ?></a>
                </li>
                <li data-tab="tab-4">
                    <a><?= $links['terms'] ?></a>
                </li>
                <li>
                    <span class="sitelang2">
                        <?= lang('language') ?>: 
                        <a href="/language/english"><?= lang('lang_english') ?></a>
                        <!--a href="/language/dutch"><?= lang('lang_dutch') ?></a-->
                    </span>
                </li>
            </ul>
        <?php } else { ?>
            <div class="cr"><?= lang('copyright') ?> 
                <span class="sitelang"><?= lang('language') ?>: 
                    <a href="/language/english"><?= lang('lang_english') ?></a>
                    <!--a href="/language/dutch"><?= lang('lang_dutch') ?></a-->
                </span>
            </div>
            <?php } ?>
    </div>
</div><!--/#footer-->