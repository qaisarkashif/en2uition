<?php
$lbl = $question->label;
$t = $question_mode == 'single' ? 1 : 2;
$this->load->view('questionnaire/question-type/header', array('lbl' => $lbl, 'question_mode' => $question_mode));

    for ($l = 1; $l <= $t; $l++) { ?>
        <div class="plain_rows_7 qpie<?= $l == 2 ? ' hidenone' : "" ?>" id="question_box_<?= $l ?>" data-qid="<?= $question->id ?>">
            <div class="pieval">
                <?php if($question_mode == 'single') { ?>
                    <input type="text" id="piev1_2" value="" class="piev" readonly />
                <?php } ?>
                <input type="text" id="piev<?= $l ?>" value="" class="piev" readonly />
            </div>
            <div class="pieContainer">
                <img src="<?=base_url("assets/img/piechart/piechart.png");?>" width="310" height="200" usemap="#planetmap<?=$l?>" />
                <img id="selected<?=$l?>" src="" width="310" height="200"  usemap="#planetmap<?=$l?>" style="display: none" />
                <map name="planetmap<?=$l?>">
                    <area alt="1" title="10%" onclick="fillPieChart(10, this);" shape="poly" coords="197,22,212,25,225,28,236,33,247,38,262,47,156,87" />
                    <area alt="2" title="20%" onclick="fillPieChart(20, this);" shape="poly" coords="263,48,274,59,282,70,286,79,286,89,157,88" />
                    <area alt="3" title="30%" onclick="fillPieChart(30, this);" shape="poly" coords="287,91,282,107,274,118,266,127,260,131,159,90" />
                    <area alt="4" title="40%" onclick="fillPieChart(40, this);" shape="poly" coords="258,131,241,142,227,148,213,151,201,154,196,155,156,90" />
                    <area alt="5" title="50%" onclick="fillPieChart(50, this);" shape="poly" coords="194,156,174,158,154,159,137,158,121,157,112,156,154,90" />
                    <area alt="6" title="60%" onclick="fillPieChart(60, this);" shape="poly" coords="110,155,92,151,74,145,62,140,51,133,46,129,152,89" />
                    <area alt="7" title="70%" onclick="fillPieChart(70, this);" shape="poly" coords="45,128,35,121,28,112,22,99,21,89,145,90" />
                    <area alt="8" title="80%" onclick="fillPieChart(80, this);" shape="poly" coords="20,88,24,74,31,61,40,51,47,47,149,87" />
                    <area alt="9" title="90%" onclick="fillPieChart(90, this);" shape="poly" coords="49,45,62,37,73,33,86,28,100,24,112,21,152,86" />
                    <area alt="10" title="100%" onclick="fillPieChart(100, this);" shape="poly" coords="114,21,127,19,141,18,157,18,175,18,189,20,197,21,154,86" />
                    <area onclick="clearPieChart(this);" shape="poly" coords="20,88,1,88,1,1,309,2,309,88,288,88,284,73,278,61,264,47,245,36,224,27,199,20,169,16,139,16,111,19,85,26,59,36,40,48,23,69" />
                    <area onclick="clearPieChart(this);" shape="poly" coords="1,87,21,88,1,198,309,198,309,87,288,87,288,99,287,114,282,126,273,138,261,147,246,156,229,163,208,170,208,170,187,174,164,176,138,176,109,173,81,167,56,155,41,145,27,129,20,110,1,89,0,125,1,193,16,108" />            
                </map>
            </div>
            <input type="hidden" id="q_<?= $l ?>" value="<?= $question->{$l == 1 ? "me" : "partner"} ?>"/>
            <?php if($question_mode == 'single') { ?>
                <input type="hidden" id="q_2" value="<?= $question->partner ?>"/>
            <?php } ?>
        </div>
    <?php } ?>

</div>

<?php $this->load->view('questionnaire/question-type/footer', array('lbl' => $lbl, 'question_mode' => $question_mode)); ?>    

<script type="text/javascript">
    var question_mode = '<?= $question_mode ?>';
    
    function setPartnerAnswersDefault() {
        flipTop();
        var field = document.getElementById('q_2');
        fillPieChart(parseInt(field.value), field);
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
    
    function fillPieChart(percent, obj) {
        percent = parseInt(percent);
        var box = $(obj).closest("[id^='question_box_']"),
            $img = box.find("img[id^='selected']");
    
        if(!isNaN(percent)) {
            var src = '<?= base_url('assets/img/piechart/') ?>/'+percent+'.png';
            if($img.attr('src') != src && percent > 0) {
                $img.attr('src', src).fadeIn();
            } else {
                percent = 0;
                $img.hide().attr('src', '');
            }
        } else {
            $img.hide().attr('src', '');
        }
        if(question_mode == 'single') {
            $("#piev1").val(isNaN(percent) ? '-' : percent + '%');
            $("#piev1_2").val(isNaN(percent) ? '-' : (100 - percent) + '%');
        } else {
            box.find("input.piev").val(isNaN(percent) ? '-' : percent + '%');
        }
    }
    
    function clearPieChart(obj) {
        fillPieChart('-', obj);
    }
    
    $(function() {        
        $.fn.saveSelection = function(whose_answer, link) {
            if(question_mode == 'single') {
                var me = parseInt($("#piev1_2").val()),
                    partner = parseInt($("#piev1").val());
                if(!isNaN(me) && me >= 0 && !isNaN(partner) && partner >= 0) {
                    var answer = me + ';' + partner,
                        status = 1;
                } else {
                    var answer = '', status = 0;
                }
            } else {
                var answ = parseInt(this.find("input.piev").val()),
                    answer = isNaN(answ) ? '' : answ,
                    status = !isNaN(answ) && answer >= 0 ? 1 : 0;
            }
            var data = {
                'id' : this.attr('data-qid'),
                'whose_answer' : whose_answer,
                'answer' : answer,
                'status' : status
            };
            if(question_mode == 'single') {
                data.split_answer = true;
            }
            saveAnswer(data, <?= $question->level_id ?>, link);
        };
        
        if(question_mode == 'double') {
            $("#me, #my-partner").flip({'trigger': 'manual'});
        }
        
        $(".qpie").click(function(e) {
            e.stopPropagation();
            if($(e.target).hasClass('qpie')) {
                clearPieChart($(this).find('[id^="selected"]'));
            }
        });
        $(".q1").click(function(e) {
            e.preventDefault();
            $("#question_box_1").saveSelection("me", this);
        });
        $(".q2").click(function(e) {
            e.preventDefault();
            if(question_mode == 'single') { 
                $("#question_box_1").saveSelection("both", this);
            } else {
                $("#question_box_2").saveSelection("partner", this);
            }
        });
        var field = document.getElementById(question_mode == 'single' ? 'q_2' : 'q_1');
        fillPieChart(field.value, field);
    });
</script>