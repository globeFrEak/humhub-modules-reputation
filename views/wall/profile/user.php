<?php

/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('ReputationModule.views_profileReputation_show', '<strong>Space</strong> reputation'); ?>
    </div>
    <div class="panel-body">

        <?php echo Yii::t('ReputationModule.views_profileReputation_show', 'In the area below, you see how much reputation this user gained inside each space.'); ?>
        <br/>
        <?php if (Yii::app()->user->id != $user->id) echo Yii::t('ReputationModule.views_profileReputation_show', 'You can only see reputation the user shares.'); ?>

        <br/><br/>

        <?php echo CHtml::form($this->createUrl('//reputation/profileReputation/show', array('uguid' => $user->guid)), 'post'); ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th><?php echo Yii::t('ReputationModule.views_profileReputation_show', "Space"); ?></th>
                <th></th>
                <th><?php echo Yii::t('ReputationModule.views_profileReputation_show', "Reputation"); ?></th>
                <?php if (Yii::app()->user->id == $user->id): ?>
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
                $space = $reputation->space;
                if ($space == null || ($reputation->visibility == 0 && Yii::app()->user->id != $reputation->user_id))
                    continue;
                ?>

                <?php
                // Hidden field to get users on this page
                echo CHtml::hiddenField("reputationUsers[" . $reputation->space_id . "]", $reputation->space_id);

                // Hidden field to get users on this page
                echo CHtml::hiddenField('reputationUser_' . $reputation->space_id . "[placeholder]", 1);
                ?>

                <?php
                $function = SpaceSetting::Get($space->id, 'functions', 'reputation', ReputationUser::DEFAULT_FUNCTION);
                ?>

                <tr>
                    <td width="28px" style="vertical-align: middle">
                        <a href="<?php echo $user->getUrl(); ?>">

                            <img class="media-object img-rounded"
                                 src="<?php echo $space->getProfileImage()->getUrl(); ?>" width="24"
                                 height="24" alt="24x24" data-src="holder.js/24x24"
                                 style="width: 24px; height: 24px;">
                        </a>

                    </td>
                    <td style="vertical-align: middle">
                        <strong><?php echo CHtml::link($space->getDisplayName(), $space->getProfileImage()->getUrl()); ?></strong>
                        <br/>
                    </td>

                    <td style="vertical-align:middle;text-indent: 5px">
                        <label>
                            <?php if ($function == 1) {
                                echo CHtml::encode($reputation->value) . ' ';
                            } else {
                                echo CHtml::encode($reputation->value) . '%';
                            } ?>
                        </label>
                    </td>
                    <?php if (Yii::app()->user->id == $reputation->user_id): ?>
                        <td style="vertical-align: middle;">

                            <div class="checkbox">
                                <label>
                                    <?php echo CHtml::checkBox(
                                        'reputationUser' . '_' . $reputation->space_id . "[visibility]",
                                        $reputation->visibility,
                                        array('class' => 'visibility',
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
            <?php
            $this->widget('CLinkPager', array(
                'pages' => $pages,
                'nextPageLabel' => '<i class="fa fa-step-forward"></i>',
                'prevPageLabel' => '<i class="fa fa-step-backward"></i>',
                'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
                'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
                'header' => '',
                'htmlOptions' => array('class' => 'pagination'),
            ));
            ?>
        </div>

        <?php if (isset($reputation) && (Yii::app()->user->id == $reputation->user_id)): ?>
            <hr>
            <?php echo CHtml::submitButton(Yii::t('ReputationModule.views_profileReputation_show', 'Save'), array('class' => 'btn btn-primary')); ?>

            <!-- show flash message after saving -->
            <?php $this->widget('application.widgets.DataSavedWidget'); ?>
        <?php endif ?>
        <?php echo Chtml::endForm(); ?>
    </div>
</div>
