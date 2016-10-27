<?php
	// You
	$y_passion = 80;
	$y_intimacy = 50;
	$y_commitment = 35;
	
	// Your partner
	$yp_passion = 100;
	$yp_intimacy = 100;
	$yp_commitment = 100;
?>
<script>
	// You
	var CT1 = <?=$y_passion;?>;
	var CR1 = <?=$y_intimacy;?>;
	var CB1 = <?=$y_commitment;?>;
	// Your partner
	var CT2 = <?=$yp_passion;?>;
	var CR2 = <?=$yp_intimacy;?>;
	var CB2 = <?=$yp_commitment;?>;

	var qa_romantic = "<?=lang("qa_romantic");?>";
	var qa_fatuous = "<?=lang("qa_fatuous");?>";
	var qa_companionate = "<?=lang("qa_companionate");?>";
	var qa_consummate = "<?=lang("qa_consummate");?>";
</script>
<?php
$lvl_str = lang('q_' . $level['label'] . '_title');
$this->load->view('questionnaire/head', $header_data);
?>
<div id="main">
    <div class="header">
        <?php $this->load->view('questionnaire/header', $header_data); ?>
    </div>
    <div class="container main-container">
        <div id="analyze">
            <h2>
                <?php
				if ($level['type'] == "past") echo(lang('qa_ana_past_rel')); else echo(lang('qa_ana_current_rel'));
                if($cur_lvl == 1) {
                    echo lang('qa_analyze_head_fst');
                } else {
                    $h2 = str_replace(array("#LVLs#", "#CURLVL#"), array(implode(', ', $lstr), $cur_lvl), lang('qa_analyze_head'));
                    $h2 = str_replace('7', lang('q_outcome_title'), $h2);
                    echo $h2;
                }
                ?>
            </h2>
            <table width="100%" cellpadding="0" cellspacing="0" align="center">
                <caption>
                    <h4>
                        <?php
                        if($cur_lvl == 1) {
                            echo str_replace("#UCOUNT#", $next_stat, lang('qa_analysis_based_fst'));
                        } else {
                            $h4 = str_replace(array("#UCOUNT#", "#LVLs#", "#CURLVL#"), array($next_stat, implode(', ', $lstr), $cur_lvl), lang('qa_analysis_based'));
                            $h4 = str_replace('7', lang('q_outcome_title'), $h4);
                            echo $h4;
                        }
                        ?>
                    </h4>
                </caption>
                <tr>
                    <td height="45" align="center">
                        <?php if($q_type != 'past') : ?>
                            <a id="prediction" class="btngreen abtn"><?= lang('qa_get_prediction') ?></a>
                        <?php endif; ?>
                        <a id="feedback" class="btngreen abtn"><?= lang('qa_get_feedback') ?></a>
                        <?php
                        if($q_type == 'past' && $cur_lvl == 7) {
                            echo '<a href="' . site_url('/outcome') . '" class="btngreen abtn">' . lang('qa_btn_edit') . '</a>';
                        } else {
                            echo '<a href="' . site_url(sprintf('questions/%s/l%s/q%s', $q_type, $cur_lvl, 1)) . '" class="btngreen abtn">' . lang('qa_btn_edit') . '</a>';
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <?php
            if($q_type != 'past') :
                $select = '<select name="duration" id="standard-dropdown">';
                $options = lang('qa_duration_select_options');
                if($options) {
                    foreach($options as $txt => $val) {
                        $select .= '<option value="'.$val.'">'.$txt.'</option>';
                    }
                }
                $select .= "</select>";
                ?>
            <div id="prediction-con">
                <div>
                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                        <tr>
                            <td align="center" height="50">
                                <p style="font-size:18px;"><?= str_replace("#SELECT#", $select, lang('qa_prediction_top_text')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="outcome analyze">
                    <?php
                    for ($i = 1; $i <= 24; $i++) {
                        ?>
                        <div id="oc-q<?= $i; ?>" class="accordion-container">
                            <div href="#" class="accordion-toggle" data-id="<?= $i; ?>"><?= lang("ocq" . $i . "_title"); ?></div>
                            <div class="accordion-content">
                                <div class="acc-pro-con">
                                    <div class="std">
                                        <div class="std-text"><?= lang('qa_std_txt_you_'.$i) ?></div>
                                        <div class="std-pro"><div class="bar"><div class="pro-bar"><span style="width:100%;"></span></div> <span>100%</span></div></div>
                                    </div>
                                    <div class="std">
                                        <div class="std-text"><?= lang('qa_std_txt_partner_'.$i) ?></div>
                                        <div class="std-pro"><div class="bar"><div class="pro-bar"><span style="width:37%;"></span></div> <span>37%</span></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php if($next_lvl !== false) : ?>
                    <div class="proceed-nxt-lvl">
                        <p>
                            <?= str_replace("#NEXTLVL#", ($next_lvl == 7 && $q_type == 'past' ? lang('q_outcome_title') : lang("q_lvl_{$next_lvl}_title")), lang('qa_proceed_nextlvl')) ?>
                            <a href="<?= site_url($next_lvl == 7 && $q_type == 'past' ? '/outcome' : sprintf('questions/%s/l%s/q%s', $q_type, $next_lvl, 1)) ?>" class="btngreen">
                                <?= lang('qa_btn_proceed') ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div id="feedback-con">
                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                    <tr><td colspan="2" align="center" height="50"><p><?= str_replace("#LVL#", $lvl_str, lang('qa_based_on_feedback_text')) ?></p></td></tr>
                    <tr>
                    	<td width="50%" align="center"><a class="btngreen btn-you"><?=lang('qa_you');?></a></td>
                    	<td width="50%" align="center"><a class="btngreen"><?=lang('qa_your_partner');?></a></td>
                    </tr>
                    <tr>
                    	<td align="center" style="position:relative;">
                        	<canvas id="fb-y" width="400" height="400"></canvas>
                            <div class="venncirctop outer">
                                <a class="venntxttop" href="#"><?=lang("qa_passion");?></a>
                            </div>
                            <div class="venncircrt outer">
                                <a class="venntxtrt" href="#"><?=lang("qa_intimacy");?></a>
                            </div>
                            <div class="venncircbtm outer">
                                <a class="venntxtbtm" href="#"><?=lang("qa_commitment");?></a>
                            </div>
                        </td>
                    	<td align="center" style="position:relative;">
                        	<canvas id="fb-yp" width="400" height="400"></canvas>
                            <div class="venncirctop outer">
                                <a class="venntxttop" href="#"><?=lang("qa_passion");?></a>
                            </div>
                            <div class="venncircrt outer">
                                <a class="venntxtrt" href="#"><?=lang("qa_intimacy");?></a>
                            </div>
                            <div class="venncircbtm outer">
                                <a class="venntxtbtm" href="#"><?=lang("qa_commitment");?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center" height="50">
                            <a class="btngreen abtn1" onClick="$('#fb1').toggle();"><?=lang("qa_btn_fb_1");?></a>
                            <div id="fb1" class="fb-content" style=" padding:0px;  display:none;">
                            	<table width="100%" cellpadding="0" cellspacing="0" align="center">
                                	<tr>
                                    	<td width="50%" style="border-right:1px solid #b5b5b5;">
			                            	<table width="100%" cellpadding="0" cellspacing="0" align="center">
                                            	<tr><td colspan="3" align="center" height="25"><u><?=lang("qa_you");?></u></td></tr>
                                                <tr>
                                                	<td width="30%" align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td width="45%"><?=lang("qa_passion");?></td>
                                                    <td width="25%"><?=$y_passion;?>%</td>
                                                </tr>
                                                <tr>
                                                	<td align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td><?=lang("qa_intimacy");?></td>
                                                    <td><?=$y_intimacy;?>%</td>
                                                </tr>
                                                <tr>
                                                	<td align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td><?=lang("qa_commitment");?></td>
                                                    <td><?=$y_commitment;?>%</td>
                                                </tr>
                                                <tr><td colspan="3">&nbsp;</td></tr>
                                            </table>
                                        </td>
                                        <td width="50%">
			                            	<table width="100%" cellpadding="0" cellspacing="0" align="center">
                                            	<tr><td colspan="3" align="center" height="25"><u><?=lang("qa_your_partner");?></u></td></tr>
                                                <tr>
                                                	<td width="30%" align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td width="45%"><?=lang("qa_passion");?></td>
                                                    <td width="25%"><?=$yp_passion?>%</td>
                                                </tr>
                                                <tr>
                                                	<td align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td><?=lang("qa_intimacy");?></td>
                                                    <td><?=$yp_intimacy?>%</td>
                                                </tr>
                                                <tr>
                                                	<td align="right" class="fb-hfield"><?=lang("qa_major");?>:</td>
                                                    <td><?=lang("qa_commitment");?></td>
                                                    <td><?=$yp_commitment?>%</td>
                                                </tr>
                                                <tr><td colspan="3">&nbsp;</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="border-top:1px solid #b5b5b5;"><td colspan="2" height="30">
                                    	<p style="padding:0 20px;"><?=lang("qa_rel_type");?></p>
                                    </td></tr>
                                    <tr>
                                        <td colspan="2" align="center">
                                            <div class="user-shape">
                                                <img src="<?= base_url('/assets/img/shapes/' . (!empty($my_group['shapename']) ? $my_group['shapename'] : 'undefined') . '.png') ?>" class="shape-<?= $my_group['color'] ?>" height="63">
                                                <span><?= $my_group['shapename'] ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center" height="50">
                            <a class="btngreen abtn1" onClick="$('#fb2').toggle();"><?=lang("qa_btn_fb_2");?></a>
                            <div id="fb2" class="fb-content" style="display:none;"><?=lang("qa_fb_txt1");?></div>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center" height="50">
                            <a class="btngreen abtn1" onClick="$('#fb3').toggle();"><?=lang("qa_btn_fb_3");?></a>
                            <div id="fb3" class="fb-content" style="display:none;"><?=lang("qa_fb_txt2");?></div>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center" height="50">
                            <a class="btngreen abtn1" onClick="$('#fb4').toggle();"><?=lang("qa_btn_fb_4");?></a>
                            <div id="fb4" class="fb-content" style="display:none;"><?=lang("qa_fb_txt3");?></div>
                        </td>
                    </tr>
				</table>
                <!--table width="100%" cellpadding="0" cellspacing="0" align="center" style="display:none;">
                    <tr><td align="center" height="50"><p><?= str_replace("#LVL#", $lvl_str, lang('qa_based_on_text')) ?></p></td></tr>
                    <tr>
                        <td align="center">
                            <div class="user-shape">
                                <img src="<?= base_url('/assets/img/shapes/' . (!empty($my_group['shapename']) ? $my_group['shapename'] : 'undefined') . '.png') ?>" class="shape-<?= $my_group['color'] ?>" height="63">
                                <span><?= $my_group['shapename'] ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $tmp = $lstr;
                    $tmp[] = $cur_lvl;
                    foreach ($tmp as $i) : ?>
                        <tr>
                            <td align="center">
                                <div class="fw-rel">
                                    <div class="fw-t"><?= str_replace("#LVL#", lang($i == 7 ? 'q_outcome_title' : "q_lvl_{$i}_title"), lang('qa_find_what')) ?></div>
                                    <div class="fw-b">
                                        <a class="btngreen l-info find-out" data-id="<?= $i ?>" data-collapsed="0" data-txt1="<?= lang('qa_btn_find_out_'.$i) ?>" data-txt2="<?= lang('qa_btn_hide_'.$i) ?>"><?= lang('qa_btn_find_out_'.$i) ?></a>
                                    </div>
                                </div>
                                <div id="fnd-rel-<?= $i ?>" class="fnd-rel-type"><?= lang("qa_find_rel_text_$i")
                                    ?></div>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    if (!$header_data['is_joined']) :
                        ?>
                        <tr>
                            <td align="center">
                                <div class="fw-rel">
                                    <div class="fw-t"><?= lang('qa_join_to_network') ?></div>
                                    <div class="fw-b"><a class="btngreen l-info" href="/join"><?= lang('btn_join') ?></a></div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    endif;
                    if($next_lvl !== false) :
                    ?>
                    <tr>
                        <td align="center">
                            <div class="fw-rel">
                                <div class="fw-t">
                                    <?= (!$header_data['is_joined'] ? lang('or') . " " : "") . str_replace("#NEXTLVL#", ($next_lvl == 7 && $q_type == 'past' ? lang('q_outcome_title') : lang("q_lvl_{$next_lvl}_title")), lang('qa_proceed_nextlvl2')) ?>
                                </div>
                                <div class="fw-b">
                                    <a href="<?= site_url($next_lvl == 7 && $q_type == 'past' ? '/outcome' : sprintf('questions/%s/l%s/q%s', $q_type, $next_lvl, 1)) ?>" class="btngreen l-info">
                                        <?= lang('qa_btn_proceed') ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table-->
            </div>
        </div>
    </div>
    <?php $this->load->view('include/footer'); ?>
</div>
<?php $this->load->view('questionnaire/foot'); ?>
<script language="javascript">
</script>