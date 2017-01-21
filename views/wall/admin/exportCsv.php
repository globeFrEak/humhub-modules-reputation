<?php

/*
 * @author Anton Kurnitzky
 */

?>
<div class="modal-dialog modal-dialog-small animated fadeIn">
    <div class="modal-content">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'export-form',
            'enableAjaxValidation' => true,
        ));
        ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t('ReputationModule.views_adminReputation_exportCsv', '<strong>Export</strong> as CSV'); ?></h4>
        </div>
        <div class="modal-body">
            <?php echo Yii::t('ReputationModule.views_adminReputation_exportCsv', 'This space uses a logarithmic function for user reputation. Enter a value the reputation should be multiplied with.'); ?>
            <br/><br/>

            <div class="form-group">
                <?php echo $form->labelEx($model, 'weighting'); ?>
                <?php echo $form->textField($model, 'weighting', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'weighting'); ?>
            </div>
        </div>

        <div class="modal-footer">

            <?php echo CHtml::submitButton(Yii::t('ReputationModule.views_adminReputation_exportCsv', 'Generate CSV'), array('class' => 'btn btn-primary')); ?>
            <button type="button" class="btn btn-primary"
                    data-dismiss="modal"><?php echo Yii::t('ReputationModule.views_adminReputation_exportCsv', 'Close'); ?></button>
            <?php $this->endWidget(); ?>
        </div>

    </div>