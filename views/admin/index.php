<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('ReputationModule.base', 'Reputation Modul Konfiguration'); ?></div>
    <div class="panel-body">


        <p><?php echo Yii::t('MostactiveusersModule.base', 'You may configure the number users to be shown.'); ?></p>
        <br/>

<?php $form = CActiveForm::begin(); ?>

        <div class="form-group">

        </div>

        <hr>
<?php echo Html::submitButton(Yii::t('MostactiveusersModule.base', 'Save'), array('class' => 'btn btn-primary')); ?>
        <a class="btn btn-default" href="<?php echo Url::to(['/admin/module']); ?>"><?php echo Yii::t('MostactiveusersModule.base', 'Back to modules'); ?></a>

<?php CActiveForm::end(); ?>

        <div id="#myContainer">
            <!-- Note, you won't have to define the name of your handler in this case -->
            <button class="sendButton" data-action-url="<?= Url::to(['/reputation/admin/recalculate']) ?>">Recalculate</button>
        </div>
    </div>
</div>