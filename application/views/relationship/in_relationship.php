<?php
$descr = lang('rel_descr');
$links = lang('rel_status_links');
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" style="empty-cells:show; border-collapse:separate;">
    <tr><td colspan="2" height="40"></td></tr>
    <tr>
        <td id="red-partner" width="50%" height="47.3%" align="center" valign="middle" class="rela1 couple bb hl">
            <div class="rel-det">
				<a id="t-red"><img src="../assets/img/couple1.png" alt=""></a>
				<div class="rel-txt"><?= $descr['red']['det'] ?></div>
            </div>
            <div class="rel-clr-det red" style="display:none;">
                <span><?= $descr['red']['inf'] ?></span>
                <p><a href="<?= site_url('/questionnaire/present') ?>" class="goto-quiz" data-color="red"><?= $links['present'] ?></a></p>
                <p><a href="<?= site_url('/questionnaire/past') ?>" class="goto-quiz" data-color="red"><?= $links['past'] ?></a></p>
            </div>
            <a id="csl-red" class="rel-cl">Cancel</a>
        </td>
        <td width="50%" height="100%" align="center" rowspan="2">
            <a href="<?= site_url('/relationship') ?>" class="glink"><?= lang('rel_btn_change_status') ?></a>
        </td>
    </tr>
    <tr>
        <td id="yellow-partner" width="50%" height="52.7%" align="center" valign="middle" class="rela1 couple hl">
            <div class="rel-det">
				<a id="t-yellow"><img src="../assets/img/couple2.png" alt=""></a>
				<div class="rel-txt"><?= $descr['yellow']['det'] ?></div>
            </div>
            <div class="rel-clr-det yellow" style="display:none;">
                <span><?= $descr['yellow']['inf'] ?></span>
                <p><a href="<?= site_url('/questionnaire/present') ?>" class="goto-quiz" data-color="yellow"><?= $links['present'] ?></a></p>
                <p><a href="<?= site_url('/questionnaire/past') ?>" class="goto-quiz" data-color="yellow"><?= $links['past'] ?></a></p>
            </div>
            <a id="csl-yellow" class="rel-cl">Cancel</a>
        </td>
    </tr>
    <tr><td colspan="2" height="34"></td></tr>
</table>