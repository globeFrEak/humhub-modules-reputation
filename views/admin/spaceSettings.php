<?php
/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\modules\reputation\models\ReputationBase;
?>
<div class="panel panel-default">
    <div
        class="panel-heading"><?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Configuration of the reputation module for this space'); ?></div>
    <div class="panel-body">

        <?php
        $form = ActiveForm::begin([
                    'action' => ['space-settings-submit', 'sguid' => $space->guid],
                    'method' => 'post',
                    'id' => 'configure-form',
                    'enableAjaxValidation' => false,
        ]);
        ?>

        <?php echo $form->errorSummary($model); ?>
        <div class="panel-body">
            <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Please read the documentation before changing the settings: TODO URL.') ?>
        </div>

        <div class="form-group">           
            <?php
            $functions = array(
                ReputationBase::LOGARITHMIC => Yii::t('ReputationModule.base', 'Logarithmic'),
                ReputationBase::LINEAR => Yii::t('ReputationModule.base', 'Linear')
            );
            echo $form->
                    field($model, 'functions')->
                    label(Yii::t('ReputationModule.forms_adminController_settings', 'Function'))->
                    dropDownList($functions, array('class' => 'form-control', 'id' => 'dropdown_function', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Choose the function that should be used to show user reputation.')));
            ?>      

        </div>

        <?php if ($model->functions == '1'): ?>
            <div class="form-group" id="logarithm_base" style="display:none">
                <?php
                echo $form->
                        field($model, 'logarithmBase')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Logarithm base'))->
                        textInput(array('class' => 'form-control'));
                ?>
            </div>
        <?php else: ?>
            <div class="form-group" id="logarithm_base">        
                <?php
                echo $form->
                        field($model, 'logarithmBase')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Logarithm base'))->
                        textInput(array('class' => 'form-control'));
                ?>          
            </div>
        <?php endif ?>

        <div class="form-group">   
            <?php
            echo $form->
                    field($model, 'daily_limit')->
                    label(Yii::t('ReputationModule.forms_adminController_settings', 'Daily limit for Users'))->
                    textInput(array('class' => 'form-control'));
            ?>           
        </div>

        <div class="form-group">

            <?php
            $functions = array(
                1 => Yii::t('ReputationModule.base', 'Yes'),
                0 => Yii::t('ReputationModule.base', 'No')
            );
            echo $form->
                    field($model, 'decrease_weighting')->
                    label(Yii::t('ReputationModule.forms_adminController_settings', 'Decrease weighting per post'))->
                    dropDownList($functions, array('class' => 'form-control', 'id' => 'join_visibility_dropdown', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Should the weighting of reputation decrease with the number with increasing activity?')));
            ?>

        </div>

        <div class="form-group">

            <?php
            $functions = array(
                1 => Yii::t('ReputationModule.base', 'Yes'),
                0 => Yii::t('ReputationModule.base', 'No')
            );
            echo $form->
                    field($model, 'cron_job')->
                    label(Yii::t('ReputationModule.forms_adminController_settings', 'Update reputation data on hourly cron job'))->
                    dropDownList($functions, array('class' => 'form-control', 'id' => 'join_visibility_dropdown', 'hint' => Yii::t('ReputationModule.views_adminReputation_show', 'Should the hourly cron job update reputation data for this space?')));
            ?>

        </div>

        <p>
            <a data-toggle="collapse" id="space-weighting-settings" href="#collapse-weighting-settings" style="font-size: 11px;"><i
                    class="fa fa-caret-right"></i> <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Weightings') ?>
            </a>
        </p>
        <div id="collapse-weighting-settings" class="panel-collapse collapse">
            <div class="form-group">
                <?php
                echo $form->
                        field($model, 'create_content')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Creating posts or comments'))->
                        textInput(array('class' => 'form-control'));
                ?>              
            </div>
            <div class="form-group"> 
                <?php
                echo $form->
                        field($model, 'smb_likes_content')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Somebody liked the post'))->
                        textInput(array('class' => 'form-control'));
                ?>              
            </div>            
            <?php //if ($space->isModuleEnabled('favorite')): ?>
                <div class="form-group">    
                    <?php
                    echo $form->
                            field($model, 'smb_favorites_content')->
                            label(Yii::t('ReputationModule.forms_adminController_settings', 'Somebody marked the post as favorite'))->
                            textInput(array('class' => 'form-control'));
                    ?>                   
                </div>
            <?php //endif ?>
            <div class="form-group">    
                <?php
                echo $form->
                        field($model, 'smb_comments_content')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Somebody comments the post'))->
                        textInput(array('class' => 'form-control'));
                ?>           
            </div>
        </div>

        <p>
            <a data-toggle="collapse" id="space-advanced-settings" href="#collapse-advanced-settings" style="font-size: 11px;"><i
                    class="fa fa-caret-right"></i> <?php echo Yii::t('ReputationModule.views_adminReputation_setting', 'Advanced Settings') ?>
            </a>
        </p>
        <div id="collapse-advanced-settings" class="panel-collapse collapse">
            <div class="form-group">        
                <?php
                echo $form->
                        field($model, 'lambda_long')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Exponential decrease for Ranking Rising'))->
                        textInput(array('class' => 'form-control'));
                ?>              
            </div>
            <div class="form-group">
                <?php
                echo $form->
                        field($model, 'lambda_short')->
                        label(Yii::t('ReputationModule.forms_adminController_settings', 'Exponential decrease for Ranking Hot'))->
                        textInput(array('class' => 'form-control'));
                ?>               
            </div>
        </div>

        <hr>        
        <?= Html::submitButton(Yii::t('ReputationModule.base', 'Save'), array('class' => 'btn btn-primary')) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var droplist = $('#dropdown_function');
        droplist.change(function (e) {
            if (droplist.val() == '0') {
                $('#logarithm_base').show(400);
            } else {
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
