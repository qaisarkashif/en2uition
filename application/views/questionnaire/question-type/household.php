<?php
$lbl = $question->label;

$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl));

    for ($l = 1; $l <= 2; $l++) { 
        $answer = $l == 2 ? $question->partner : $question->me;
        if(preg_match("/hh=([123]{1})/i", $answer, $matches)) {
            $hh = $matches[1];
        } else {
            $hh = 0;
        }
        ?>
        <form autocomplete="off" onsubmit="return false;">
            <div class="range_slider_bg l2treeq<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
                <div class="hh_choice">
                    <ul>
                        <li data-id="1"><?=lang("{$lbl}s{$l}_one");?></li>
                        <li data-id="2"><?=lang("{$lbl}s{$l}_two");?></li>
                        <li data-id="3"><?=lang("{$lbl}s{$l}_multiple");?></li>
                    </ul>
                    <input type="hidden" value="<?= $hh ?>" class="q_hh"/>
                </div>
                <div id="hh-container<?= $l ?>">
                    <p class="hh-text hidenone"><?=lang("{$lbl}s{$l}_hht");?></p>

                    <?php 
                    foreach(array('a', 'c', 'b') as $s) : 
                        if(preg_match("/h{$s}=(one|two);([0123]{0,1})/i", $answer, $matches)) {
                            ${"h{$s}_t"} = $matches[1];
                            ${"h{$s}_v"} = $matches[2];
                        } else {
                            ${"h{$s}_t"} = '';
                            ${"h{$s}_v"} = 0;
                        }
                        ?>
                        <div class="hh<?= $s ?> hidenone subcontainer" data-v="<?= $s ?>">
                            <div class="no1"><a><?=lang("{$lbl}s{$l}_h{$s}");?></a></div>
                            <div class="line1"></div>
                            <?php if($s == 'c') : ?>
                                <div class="line2"></div>
                                <div class="line3"></div>
                            <?php endif; ?>
                            <div class="no2 hh<?= $s ?>_one" data-v="one"><a><?=lang("{$lbl}s{$l}_one");?></a></div>
                            <div class="line2 hh<?= $s ?>_one_box hidenone"></div>
                            <div class="ha_one_box hh<?= $s ?>_one_box hidenone" data-v="one">
                                <ul class="hh_list">
                                    <li data-id="1"><?=lang("{$lbl}s{$l}_one_opt1");?></li>
                                    <li data-id="2"><?=lang("{$lbl}s{$l}_one_opt2");?></li>
                                    <li data-id="3"><?=lang("{$lbl}s{$l}_one_opt3");?></li>
                                </ul>
                            </div>
                            <div class="<?= $s == 'c' ? 'line5' : 'line3' ?>"></div>
                            <div class="no2 hh<?= $s ?>_two" data-v="two"><a><?=lang("{$lbl}s{$l}_two");?></a></div>
                            <div class="line2 hh<?= $s ?>_two_box hidenone"></div>
                            <div class="ha_two_box hh<?= $s ?>_two_box hidenone" data-v="two">
                                <ul class="hh_list">
                                    <li data-id="1"><?=lang("{$lbl}s{$l}_two_opt1");?></li>
                                    <li data-id="2"><?=lang("{$lbl}s{$l}_two_opt2");?></li>
                                </ul>
                            </div>
                            <input type="hidden" class="<?= "q_h{$s}_t" ?>" value="<?= ${"h{$s}_t"} ?>"/>
                            <input type="hidden" class="<?= "q_h{$s}_v" ?>" value="<?= ${"h{$s}_v"} ?>"/>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </form>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl)); ?>    

<script type="text/javascript">
    function setPartnerAnswersDefault() {
        flipTop();
        $("#question_box_2").setDefault();
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
    
    $(function() {
        $.fn.setDefault = function() {
            var $this = this.find("[id^='hh-container']");
            $this.find('.hha, .hhb, .hhc, .hh-text').addClass('hidenone');
            var hh = this.find('.q_hh').val();
            if(hh > 0) {
                var choise = this.find('.hh_choice li[data-id="'+hh+'"]');
                choise.removeClass('active');
                toggleActiveClass(choise);
                this.find('.hh-text').removeClass('hidenone');
                
                if(hh > 0)
                    $this.find('.hha').removeClass('hidenone');
                if(hh > 1)
                    $this.find('.hhb').removeClass('hidenone');
                if(hh > 2)
                    $this.find('.hhc').removeClass('hidenone');
                
                $.each(new Array('a', 'b', 'c'), function(i, v) {
                    $this.find(".hh"+v).updateBoxState(v);
                });
            }
        };
        
        $.fn.updateBoxState = function() {
            var s = this.attr('data-v');
            var k1 = this.find('.q_h'+s+'_t').val(),
                k2 = this.find('.q_h'+s+'_v').val();
            if(k1 !== '') {
                this.find(".no1, .no2.hh"+s+"_"+k1).addClass('active');
                this.find(".hh"+s+"_"+k1+"_box").removeClass('hidenone');
            } else {
                this.find(".no1, .no2").removeClass('active');
                this.find(".hh"+s+"_one_box, .hh"+s+"_two_box").addClass('hidenone');
            }
            if(k2 > 0) {
                this.find(".ha_"+k1+"_box .hh_list [data-id='"+k2+"']").addClass('active');
            } else {
                this.find(".hh_list .active").removeClass('active');
            }
        };
        
        $.fn.saveSelection = function(whose_answer, link) {
            var hh = this.find('.q_hh').val(),
                answer = new Array('hh='+hh),
                status = 1;
            if(hh > 0) {
                this.find(".subcontainer:not(.hidenone)").each(function() {
                    if($(this).find('.no1').hasClass('active')) {
                        var s = $(this).attr('data-v'),
                            t = $(this).find('.q_h'+s+'_t').val(),
                            v = $(this).find('.q_h'+s+'_v').val();
                        if(t == '' || v == 0) { 
                            status = 0; 
                        }
                        answer.push('h'+s+'='+t+';'+v);
                    } else {
                        status = 0;
                    }
                });
            } else {
                status = 0;
            }
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer.join('#'),
                'status' : status
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
        
        $(".hh_choice li").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
            var choise_box = $(this).closest('div'),
                choise = choise_box.find('.active'),
                choise_val = 0;
            if(choise.length) {
                choise_val = choise.attr('data-id');
            }
            choise_box.find('.q_hh').val(choise_val);
            choise_box.closest('[id^="question_box_"]').setDefault();
        });
        
        $(".hh_list li").click(function(e) {
            e.preventDefault();
            toggleActiveClass(this);
            var box = $(this).closest(".subcontainer");
            var active = $(this).closest('.hh_list').find('.active');
            box.find('.q_h'+box.attr('data-v')+'_v').val(active.length ? active.attr('data-id') : 0);
        });
        
        $(".no2 a").click(function(e) {
            e.preventDefault();
            var box = $(this).closest('.no2'),
                box2 = box.closest(".subcontainer");
            var k1 ='', k2=0;
            if(box.hasClass('active')) {
                box.removeClass('active');
            } else {
                box2.find('.no2.active a').click();
                box.addClass('active');
                k1 = box.attr('data-v');
            }
            box2.find('.q_h'+box2.attr('data-v')+'_t').val(k1);
            box2.find('.q_h'+box2.attr('data-v')+'_v').val(k2);
            box2.updateBoxState();
        });
        
        $("#question_box_1").setDefault();
    });
</script>