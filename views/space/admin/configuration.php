<?php
/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', '<strong>Space member</strong> reputation'); ?>
    </div>
    <div class="panel-body">

        <?php echo Yii::t('ReputationModule.views_adminReputation_show', 'Last Update: '); ?>
        <?php echo '<strong> ' . $lastUpdatedBefore . '</strong>' ?>
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', ' minutes ago'); ?>
        <?php echo '<br><br>' ?>
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', 'In the area below, you see how much reputation each member inside this space has gained.'); ?>
        <br/><br/>
        <?php echo CHtml::form($this->createUrl('//reputation/adminReputation/show', array('sguid' => $space->guid)), 'post'); ?>

        <table class="table table-hover">
            <thead>
            <tr>
                <th><?php echo Yii::t('ReputationModule.views_adminReputation_show', "User"); ?></th>
                <th></th>
                <th style="text-align: center"><?php echo Yii::t('ReputationModule.views_adminReputation_show', "Score"); ?>
                    <i class="fa fa-info-circle tt" data-toggle="tooltip" data-placement="top"
                       title="<?php echo Yii::t('ReputationModule.views_adminReputation_show', 'Reputation score of this user'); ?>"></i>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($reputations as $reputation) : ?>
                <?php
                $user = $reputation->user;
                if ($user == null)
                    continue;
                ?>

                <tr>
                    <td width="36px" style="vertical-align:middle">
                        <a href="<?php echo $user->getUrl(); ?>">

                            <img class="media-object img-rounded"
                                 src="<?php echo $user->getProfileImage()->getUrl(); ?>" width="24"
                                 height="32" alt="32x32" data-src="holder.js/32x32"
                                 style="width: 32px; height: 32px;">
                        </a>

                    </td>
                    <td style="vertical-align:middle">
                        <strong><?php echo CHtml::link($user->displayName, $user->getProfileUrl()); ?></strong>
                        <br/>
                    </td>

                    <td style="vertical-align:middle; text-align:center">
                        <strong>
                            <?php if ($function == ReputationUser::LINEAR) {
                                echo CHtml::encode($reputation->value);
                            } else {
                                echo CHtml::encode($reputation->value) . '%';
                            } ?>
                        </strong>
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

        <hr>
        <?php echo CHtml::link(Yii::t('ReputationModule.views_adminReputation_show', 'Update'), array('//reputation/adminReputation/show', 'sguid' => $this->getSpace()->guid, 'forceUpdate' => true), array('class' => 'btn btn-primary')); ?>

        <div class="pull-right">
            <?php echo CHtml::link(Yii::t('ReputationModule.views_adminReputation_show', 'Configuration'), array('//reputation/adminReputation/configuration', 'sguid' => $this->getSpace()->guid), array('class' => 'btn btn-warning')); ?>
            <?php if ($function == ReputationUser::LOGARITHMIC): ?>
                <?php echo CHtml::link(Yii::t('ReputationModule.views_adminReputation_show', "Export as CSV"), $space->createUrl('//reputation/adminReputation/export'), array('class' => 'btn btn-danger', 'data-toggle' => 'modal', 'data-target' => '#globalModal')); ?>
            <?php else: ?>
                <?php echo CHtml::link(Yii::t('ReputationModule.views_adminReputation_show', "Export as CSV"), $space->createUrl('//reputation/adminReputation/export'), array('class' => 'btn btn-danger')); ?>
            <?php endif ?>
        </div>


        <?php echo Chtml::endForm(); ?>
    </div>
</div>
