<?php
$descr = lang('rel_descr');
$links = lang('rel_status_links');
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" style="empty-cells:show; border-collapse:separate;">
    <tr><td colspan="2" height="40"></td></tr>
    <tr>
        <td width="50%" height="100%" align="center" rowspan="2">
            <a href="<?= site_url('/relationship') ?>" class="glink"><?= lang('rel_btn_change_status') ?></a>
        </td>
        <td id="green-partner" width="50%" height="47.3%" align="center" valign="middle" class="relb1 single bb hl">
            <div class="rel-det">
				<a id="t-green"><img src="../assets/img/single_man_happy.png" alt=""></a>
				<div class="rel-txt"><?= $descr['green']['det'] ?></div>
            </div>
            <div class="rel-clr-det green" style="display:none;">
                <span><?= $descr['green']['inf'] ?></span>
                <p><a href="<?= site_url('/questionnaire/past') ?>" class="goto-quiz" data-color="green"><?= $links['past'] ?></a></p>
            </div>
            <a id="csl-green" class="rel-cl">Cancel</a>
        </td>
    </tr>
    <tr>
        <td id="blue-partner" width="50%" height="52.7%" align="center" class="relb1 single hl">
            <div class="rel-det">
				<a id="t-blue"><img src="../assets/img/single_man_serious.png" alt=""></a>
				<div class="rel-txt"><?= $descr['blue']['det'] ?></div>
            </div>
            <div class="rel-clr-det blue" style="display:none;">
                <span><?= $descr['blue']['inf'] ?></span>
                <p><a href="<?= site_url('/questionnaire/past') ?>" class="goto-quiz" data-color="blue"><?= $links['past'] ?></a></p>
            </div>
            <a id="csl-blue" class="rel-cl">Cancel</a>
        </td>
    </tr>
    <tr><td colspan="2" height="34"></td></tr>
</table>