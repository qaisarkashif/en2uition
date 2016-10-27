<div class="container" id="features">
    <div class="wow fadeInDown">
        <div id="accordion" class="panel-group col-md-10 col-md-offset-1">
            <?php
            $sections = lang('features_sections');
            if ($sections) :
                foreach ($sections as $key => $section) :
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-target="#<?= $key ?>">
                            <h4 class="panel-title"><i class="fa fa-caret-right"></i><a><?= $section['heading'] ?></a></h4>
                        </div>
                        <div id="<?= $key ?>" class="panel-collapse collapse">
                            <div class="panel-body"><?= $section['content'] ?></div>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>
