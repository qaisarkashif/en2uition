<?php
$lbl = $question->label;

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl));

for ($l = 1; $l <= 2; $l++) { 
    $answer = $l == 2 ? $question->partner : $question->me;
    if(preg_match("/yn=(Y|N)/i", $answer, $matches)) {
        $yn = $matches[1];
    } else {
        $yn = 'N';
    }
    ?>
    <div class="range_slider_bg l2treeq<?=$l==2 ? ' hidenone' : ""?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
        <div class="mixed-descent">
            <div class="md-txt"><?= lang("{$lbl}s{$l}_md"); ?></div>
            <div id="md-yn<?= $l ?>" class="boolean_question choose_yes_no">
                <a class="boolean_question_yes<?= $yn == "Y" ? " active" : "" ?>" data-v="Y"><?= lang("q_btn_yes"); ?></a>
                <a class="boolean_question_no<?= $yn == "N" ? " active" : "" ?>" data-v="N"><?= lang("q_btn_no"); ?></a>
            </div>
        </div>
        
        <?php 
        for ($h = 1; $h <= 2; $h++) {
            foreach (array("main", "sub", "subsub") as $var) {
                if (preg_match("/" . $var . $h . "=([0-9]+)/i", $answer, $matches)) {
                    ${$var . $h} = $matches[1];
                } else {
                    ${$var . $h} = 0;
                }
            }
        ?>
            <div id="ethnicity<?= $l . "_" . $h ?>" class="<?=$h==2 ? 'hidenone' : ""?>">
                <div class="ethnicity">
                    <ul id="top<?= $l . "_" . $h ?>">
                        <?php
                        foreach ($ethnicity as $eid => $eth) {
                            if(count($eth['info']) == 0) { continue; }
                            ?>
                            <li class="sub">
                                <?php 
                                if($eth['info']['main_id'] == 0) {
                                    echo '<a data-id="' . $eid . '" class="ec-main'. (${"main{$h}"} == $eid ? ' active' : '') . '">' . $eth['info']["name"] .'</a>';
                                }
                                if (count($eth["childs"]) > 0) {
                                    $_class = ${"main{$h}"} == $eid ? '' : ' hidenone';
                                    ?>
                                    <div class="main_eth<?= $_class ?>"></div>
                                    <ul class="ul-sub<?= $_class ?>">
                                        <?php
                                        foreach ($eth["childs"] as $cid) {
                                            if (count($ethnicity[$cid]['info']) == 0) {
                                                continue;
                                            }
                                            $child = $ethnicity[$cid]['info'];
                                            $_class = "";
                                            if (${"sub{$h}"} == $cid) {
                                                $_class .= " active";
                                            }
                                            if ($child["example"] != "") {
                                                $_class .= " nicetip";
                                            }
                                            ?>
                                            <li class="subsub <?= $child["tt_width_cls"]; ?>">
                                                <div class="sub_eth"></div>
                                                <a data-id="<?= $cid ?>" class="ec-sub<?= $_class; ?>" title="<?= $child["example"] ?>">
                                                    <span title=""><?= $child["name"]; ?></span>
                                                </a>
                                                <?php 
                                                if (count($ethnicity[$cid]["childs"]) > 0) { 
                                                    $_class = ${"sub{$h}"} == $cid ? '' : 'hidenone';
                                                    ?>
                                                    <div class="subsub_eth <?= $_class ?>"></div>
                                                    <ul class="<?= $_class ?>">
                                                        <?php 
                                                        foreach ($ethnicity[$cid]["childs"] as $scid) { 
                                                            if (count($ethnicity[$scid]['info']) == 0) {
                                                                continue;
                                                            }
                                                            $subchild = $ethnicity[$scid]['info'];
                                                            ?>
                                                            <li class="<?= $subchild["tt_width_cls"]; ?>"><div class="sub_eth"></div>
                                                                <a data-id="<?= $scid ?>" class="ec-subsub<?= ${"subsub{$h}"} == $scid ? " active" : "" ?>"><?= $subchild["name"] ?></a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            
                            </li>    
                        <?php } ?>
                    </ul> 
                </div>
                <div class="clear"></div>
            </div>
        <?php } ?>
        <div class="btn-other-half" style="visibility: <?= $yn == "Y" ? 'visible' : 'hidden'; ?>"><a><?= lang("{$lbl}s{$l}_other_half"); ?></a></div>
    </div>
<?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>    

<script type="text/javascript">    
    var opts1 = {
        'active_elem'  : "a.ec-main.active",
        'sub_class'    : ".sub",
        "find_elems"   : ".main_eth, .ul-sub"
    };
    var opts2 = {
        'active_elem'  : "a.ec-sub.active",
        'sub_class'    : ".subsub",
        "find_elems"   : ".subsub_eth, ul"
    };
    
    function setPartnerAnswersDefault() {
        flipTop();
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
    
    function collapseAll(obj, opts) {
        var box = $(obj).closest('.ethnicity');
        box.find(opts.active_elem).each(function() {
            $(this)
                .removeClass('active')
                .closest(opts.sub_class)
                .find(opts.find_elems)
                .addClass('hidenone');
        });
        box.find("a.ec-subsub.active").removeClass('active');
    }
    
    $(function() {
        $.fn.saveSelection = function(whose_answer, link) {
            var mixed = this.find(".choose_yes_no a.active").attr('data-v'),
                answer = new Array("yn="+mixed),
                status = 1;
            
            this.find(".ethnicity" + (mixed == 'Y' ? "" : ":first")).each(function(i) {
                var main = $(this).find(".ec-main.active");
                if(main.length) {
                    answer.push('main' + (i+1) + '=' + main.attr('data-id'));
                    var sub_active = main.closest('.sub').find(".ec-sub.active"),
                        sub = main.closest('.sub').find(".ec-sub");
                    if(sub_active.length) {
                        answer.push('sub' + (i+1) + '=' + sub_active.attr('data-id'));
                        var subsub_active = sub_active.closest('.subsub').find(".ec-subsub.active"),
                            subsub = sub_active.closest('.subsub').find(".ec-subsub");
                        if(subsub_active.length) {
                            answer.push('subsub' + (i+1) + '=' + subsub_active.attr('data-id'));
                        } else if(subsub.length) {
                            status = 0;
                        }
                    } else if(sub.length) {
                        status = 0;
                    }
                } else {
                    status = 0;
                }
            });
            var data = {
                'id': this.attr('data-qid'),
                'whose_answer': whose_answer,
                'answer': answer.join(";"),
                'status' : answer.length <= 1 ? 0 : status
            };
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        $("#me, #my-partner").flip({'trigger': 'manual'});
        
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection("me", this);
        });

        $(".q2").click(function(e) {
            e.preventDefault();
            $("#question_box_2").saveSelection("partner", this);
        });
        
        $("a.ec-main").click(function(e) {
            e.preventDefault();
            var a = !$(this).hasClass('active');
            collapseAll(this, opts1);
            collapseAll(this, opts2);
            var box = $(this).closest('.sub');
            if(a) {
                $(this).addClass('active');
                box.find('.main_eth, .ul-sub').removeClass('hidenone');
            }
        });
        
        $("a.ec-sub").click(function(e) {
            e.preventDefault();
            var a = !$(this).hasClass('active');
            collapseAll(this, opts2);
            var box = $(this).closest('.subsub');
            if(a) {
                $(this).addClass('active');
                box.find('.subsub_eth, ul').removeClass('hidenone');
            }
        });
        
        $("a.ec-subsub").click(function(e) {
            e.preventDefault();
            var a = !$(this).hasClass('active');
            $(this)
                .closest('ul')
                .find("a.ec-subsub.active")
                .removeClass('active');
            if(a) {
                $(this).toggleClass("active");
            }
        });
        
        $(".choose_yes_no a").click(function() {
            var visibility = $(this).attr('data-v') == 'Y' ? 'visible' : "hidden";
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            var box = $(this).closest("[id^='question_box_']");
            box.find(".btn-other-half").css('visibility', visibility);
            if($(this).attr('data-v') == 'N') {
                box.find('[id^="ethnicity"]:first').removeClass('hidenone');
                box.find('[id^="ethnicity"]:last').addClass('hidenone');
            }
        });
        
        $(".btn-other-half a").click(function(e) {
            e.preventDefault();
            var box = $(this).closest("[id^='question_box_']");
            box.find('[id^="ethnicity"]:first').addClass('hidenone');
            box.find('[id^="ethnicity"]:last').removeClass('hidenone');
            $(this).closest('.btn-other-half').css('visibility', 'hidden');
        });
    });
</script>