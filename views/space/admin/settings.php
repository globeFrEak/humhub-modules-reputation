<?php

/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

?>
<div class="panel panel-default">
    <div
        class="panel-heading"><?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Configuration of the reputation module for this space'); ?></div>
    <div class="panel-body">

        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'configure-form',
            'enableAjaxValidation' => true,
        ));
        ?>

        <?php echo $form->errorSummary($model); ?>
        <div class="panel-body">
            <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Please read the documentation before changing the settings: TODO URL.') ?>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'functions'); ?>
            <?php
            $functions = array(
                ReputationBase::LOGARITHMIC => Yii::t('ReputationModule.base', 'Logarithmic'),
                ReputationBase::LINEAR => Yii::t('ReputationModule.base', 'Linear')
            );
            ?>
            <?php echo $form->dropDownList($model, 'functions', $functions, array('class' => 'form-control', 'id' => 'dropdown_function', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Choose the function that should be used to show user reputation.'))); ?>
            <?php echo $form->error($model, 'functions'); ?>
        </div>

        <?php if ($model->functions == '1'): ?>
            <div class="form-group" id="logarithm_base" style="display:none">
                <?php echo $form->labelEx($model, 'logarithm_base'); ?>
                <?php echo $form->textField($model, 'logarithm_base', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'logarithm_base'); ?>
            </div>
        <?php else: ?>
            <div class="form-group" id="logarithm_base">
                <?php echo $form->labelEx($model, 'logarithm_base'); ?>
                <?php echo $form->textField($model, 'logarithm_base', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'logarithm_base'); ?>
            </div>
        <?php endif ?>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'daily_limit'); ?>
            <?php echo $form->textField($model, 'daily_limit', array('class' => 'form-control')); ?>
            <?php echo $form->error($model, 'daily_limit'); ?>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'decrease_weighting'); ?>
            <?php
            $functions = array(
                1 => Yii::t('ReputationModule.base', 'Yes'),
                0 => Yii::t('ReputationModule.base', 'No')
            );
            ?>
            <?php echo $form->dropDownList($model, 'decrease_weighting', $functions, array('class' => 'form-control', 'id' => 'join_visibility_dropdown', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Should the weighting of reputation decrease with the number with increasing activity?'))); ?>
            <?php echo $form->error($model, 'decrease_weighting'); ?>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'cron_job'); ?>
            <?php
            $functions = array(
                1 => Yii::t('ReputationModule.base', 'Yes'),
                0 => Yii::t('ReputationModule.base', 'No')
            );
            ?>
            <?php echo $form->dropDownList($model, 'cron_job', $functions, array('class' => 'form-control', 'id' => 'join_visibility_dropdown', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Should the hourly cron job update reputation data for this space?'))); ?>
            <?php echo $form->error($model, 'cron_job'); ?>
        </div>

        <p>
            <a data-toggle="collapse" id="space-weighting-settings" href="#collapse-weighting-settings" style="font-size: 11px;"><i
                    class="fa fa-caret-right"></i> <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Weightings') ?>
            </a>
        </p>
        <div id="collapse-weighting-settings" class="panel-collapse collapse">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'create_content'); ?>
                <?php echo $form->textField($model, 'create_content', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'create_content'); ?>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model, 'smb_likes_content'); ?>
                <?php echo $form->textField($model, 'smb_likes_content', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'smb_likes_content'); ?>
            </div>

            <?php if ($space->isModuleEnabled('favorite')): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'smb_favorites_content'); ?>
                    <?php echo $form->textField($model, 'smb_favorites_content', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'smb_favorites_content'); ?>
                </div>
            <?php endif ?>


            <div class="form-group">
                <?php echo $form->labelEx($model, 'smb_comments_content'); ?>
                <?php echo $form->textField($model, 'smb_comments_content', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'smb_comments_content'); ?>
            </div>
        </div>

        <p>
            <a data-toggle="collapse" id="space-advanced-settings" href="#collapse-advanced-settings" style="font-size: 11px;"><i
                    class="fa fa-caret-right"></i> <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Advanced Settings') ?>
            </a>
        </p>
        <div id="collapse-advanced-settings" class="panel-collapse collapse">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'lambda_long'); ?>
                <?php echo $form->textField($model, 'lambda_long', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'lambda_long'); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'lambda_short'); ?>
                <?php echo $form->textField($model, 'lambda_short', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'lambda_short'); ?>
            </div>
        </div>

        <hr>
        <?php echo CHtml::submitButton(Yii::t('ReputationModule.base', 'Save'), array('class' => 'btn btn-primary')); ?>

        <?php $this->endWidget(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var droplist = $('#dropdown_function');
        droplist.change(function (e) {
            if (droplist.val() == '0') {
                $('#logarithm_base').show(400);
            }
            else {
                $('#logarithm_base').hide(400);
            }
        })
    });

    $('#space-advanced-settings').on('show.bs.collapse', function () {
        // change link arrow
        $('#space-advanced-link i').removeClass('fa-caret-right');
        $('#space-advanced-link i').addClass('fa-caret-down');
    })

    $('#space-advanced-settings').on('hide.bs.collapse', function () {
        // change link arrow
        $('#space-advanced-link i').removeClass('fa-caret-down');
        $('#space-advanced-link i').addClass('fa-caret-right');
    })

    $('#space-weighting-settings').on('show.bs.collapse', function () {
        // change link arrow
        $('#space-weighting-link i').removeClass('fa-caret-right');
        $('#space-weighting-link i').addClass('fa-caret-down');
    })

    $('#space-weighting-settings').on('hide.bs.collapse', function () {
        // change link arrow
        $('#space-weighting-link i').removeClass('fa-caret-down');
        $('#space-weighting-link i').addClass('fa-caret-right');
    })
</script>
