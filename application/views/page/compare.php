<div class="col-xs-12 col-sm-12">
    <div class="dm-compare">
        <h2><?= lang('compare_dailymood') ?></h2>
        <table width="770" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="90"></td>
                <td width="680">
                    <form onsubmit="return false;" autocomplete="off">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <thead>
                            <th>
                                <select name="friend" onchange="reloadHistoData();">
                                    <option value=""><?= lang('all') ?></option>
                                    <option value="all"><?= lang('friends') ?></option>
                                </select>
                            </th>
                            <?php foreach ($selects as $name => $options) : ?>
                                <th>
                                    <select name="<?= $name ?>" onchange="reloadHistoData();">
                                        <option value=""><?= lang($name) ?></option>
                                        <?php
                                        foreach (${$options} as $item) {
                                            echo '<option value="' . $item[$name] . '">' . $item[$name] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </th>
                            <?php endforeach; ?>
                            <th>
                                <select name="color" onchange="reloadHistoData();">
                                    <option value=""><?= lang('color') ?></option>
                                    <?php
                                    $lng_colors = lang('colors');
                                    foreach ($colors as $color) {
                                        echo '<option value="' . $color . '"' . ($pre_color == $color ? ' selected' : '') . '>' . $lng_colors[$color] . '</option>';
                                    }
                                    ?>
                                </select>
                            </th>
                            <th>
                                <select name="shape" onchange="reloadHistoData();">
                                    <option value=""><?= lang('shape') ?></option>
                                    <option value="-1">?</option>
                                    <?php
                                    foreach ($shapes as $shape) {
                                        echo '<option value="' . $shape['id'] . '"' . ($pre_shape == strtolower(str_replace('_', '-', $shape['name'])) ? ' selected' : '') . '>' . $shape['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </th>
                            </thead>
                        </table>
                    </form>
                </td>
                <td width="90"></td>
            </tr>
            <tr><td colspan="3" height="65"></td></tr>
            <tr class="hc-left">
                <td valign="bottom">
                    <table width="90" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="left" class="ll"><?= lang('population') ?> (<?= strtolower(lang('total')) ?> = <span id="population"></span>)</td>
                            <td valign="bottom">
                                <ul>
                                    <li><span class="lax w" data-num="16">16</span></li>
                                    <li></li>
                                    <li><span class="lax w" data-num="14">14</span></li>
                                    <li></li>
                                    <li><span class="lax w" data-num="12">12</span></li>
                                    <li></li>
                                    <li><span class="lax w" data-num="10">10</span></li>
                                    <li></li>
                                    <li><span class="lax" data-num="8">8</span></li>
                                    <li></li>
                                    <li><span class="lax" data-num="6">6</span></li>
                                    <li></li>
                                    <li><span class="lax" data-num="4">4</span></li>
                                    <li></li>
                                    <li><span class="lax" data-num="2">2</span></li>
                                    <li class="last"></li>
                                </ul>
                            </td>
                            <td width="20"></td>
                        </tr>
                    </table>
                </td>
                <td height="390" valign="bottom">
                    <table width="680" cellpadding="0" cellspacing="0" height="390">
                        <tr class="dm-pro-bar" data-max="0" data-all-count="0">
                            <?php
                            for ($i = 1; $i <= 16; $i++) {
                                echo '<td valign="bottom"><div class="col" data-id="0"></div></td>';
                            }
                            ?>
                        </tr> 
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <table width="100%">
                        <tr><td colspan="16" height="20"></td></tr>  
                        <tr class="dm-bottom">
                            <?php
                            for ($i = 1; $i <= 16; $i++) {
                                echo '<td width="" height="10"></td>';
                            }
                            ?>
                        </tr>
                        <tr><td colspan="16" align="center" height="75" class="com-dm"><?= lang('dailymood_title') ?></td></tr>    
                    </table>
                </td>
                <td></td>
            </tr>    
        </table>
    </div>
</div>