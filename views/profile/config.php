<?php
/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\modules\reputation\models\ReputationBase;
use humhub\modules\space\models\Space;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('ReputationModule.views_profileReputation_show', '<strong>Space</strong> reputation'); ?>
    </div>
    <div class="panel-body">

        <?php echo Yii::t('ReputationModule.views_profileReputation_show', 'In the area below, you see how much reputation this user gained inside each space.'); ?>
        <br/>
        <?php if (Yii::$app->user->id != $user->id) echo Yii::t('ReputationModule.views_profileReputation_show', 'You can only see reputation the user shares.'); ?>

        <br/><br/>
        
        <?php if (isset($reputations) && (Yii::$app->user->id === $user->id)): ?>
        <?php
        $form = ActiveForm::begin([
                    'action' => ['config', 'uguid' => $user->guid],
                    'method' => 'post',
                    'id' => 'configure-form',
                    'enableAjaxValidation' => false,
        ]);
        ?>    
        <?php endif ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo Yii::t('ReputationModule.views_profileReputation_show', "Space"); ?></th>
                    <th></th>
                    <th><?php echo Yii::t('ReputationModule.views_profileReputation_show', "Reputation"); ?></th>
                    <?php if (Yii::$app->user->id == $user->id): ?>
                        <th><?php echo Yii::t('ReputationModule.views_profileReputation_show', "Share"); ?>
                            <i class="fa fa-info-circle tt" data-toggle="tooltip" data-placement="top"
                               title="<?php echo Yii::t('ReputationModule.views_profileReputation_show', 'Visible for other users.'); ?>"></i>
                        </th>
                    <?php endif ?>
                    <th style="text-align: right"><?php echo Yii::t('ReputationModule.views_profileReputation_show', 'Updated') ?></th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($reputations as $reputation) : ?>
                    <?php
                    $space = Space::findOne(['id' => $reputation->space_id]);

                    if ($space == null || ($reputation->visibility == 0 && Yii::$app->user->id != $reputation->user_id))
                        continue;

                    // Hidden input to get users on this page                    
                    echo Html::hiddenInput("reputationUsers[" . $reputation->space_id . "]", $reputation->space_id);

                    // Hidden field to get users on this page
                    echo Html::hiddenInput('reputationUser_' . $reputation->space_id . "[placeholder]", 1);

                    $module = Yii::$app->getModule('reputation');
                    $setting_function = $module->settings->contentContainer($contentContainer)->get('functions', ReputationBase::DEFAULT_FUNCTION);
                    ?>

                    <tr>
                        <td width="28px" style="vertical-align: middle">                            
                            <?php
                            // Space Icon Widget
                            echo \humhub\modules\space\widgets\Image::widget([
                                'space' => $space,
                                'width' => 32,
                                'htmlOptions' => [
                                    'class' => 'current-space-image',
                                ],
                                'link' => 'true',
                            ]);
                            ?>
                        </td>
                        <td style="vertical-align: middle">
                            <strong><?php echo Html::a($space->getDisplayName(), $space->getUrl()); ?></strong>
                            <br/>
                        </td>

                        <td style="vertical-align:middle;text-indent: 5px">
                            <label>
                                <?php
                                if ($setting_function == 1) {
                                    echo Html::encode($reputation->value) . ' ';
                                } else {
                                    echo Html::encode($reputation->value) . '%';
                                }
                                ?>
                            </label>
                        </td>
                        <?php if (Yii::$app->user->id == $reputation->user_id): ?>
                            <td style="vertical-align: middle;">

                                <div class="checkbox">
                                    <label>
                                        <?php
                                        echo Html::checkBox(
                                                'reputationUser' . '_' . $reputation->space_id . "[visibility]", $reputation->visibility, array('class' => 'visibility',
                                            'id' => "chk_visibility_" . $reputation->space_id,
                                            'data-view' => 'slider')
                                        );
                                        ?>
                                    </label>
                                </div>
                            </td>
                        <?php endif ?>
                        <td style="text-align: right">
                            <?php echo $reputation->updated_at; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>                   
            </tbody>
        </table>

        <div class="pagination-container">
            <?php echo \humhub\widgets\LinkPager::widget(['pagination' => $pagination]); ?>
        </div>

        <?php if (isset($reputations) && (Yii::$app->user->id == $user->id)): ?>
            <hr>
            <?php echo Html::submitButton(Yii::t('ReputationModule.views_profileReputation_show', 'Save'), array('class' => 'btn btn-primary')); ?>            

            <!-- show flash message after saving -->
            <?php echo \humhub\widgets\DataSaved::widget(); ?>       
            <?php ActiveForm::end(); ?>
        <?php endif ?>
    </div>
</div>
