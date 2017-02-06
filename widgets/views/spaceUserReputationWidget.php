<?php

/**
 * @author Philipp Horna (v0.20+) 
 * */
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\reputation\models\ReputationUser;
?>

<div class="panel panel-default members" id="space-members-panel">
    <?php echo \humhub\widgets\PanelMenu::widget(['id' => 'space-members-panel']); ?>
    <div class="panel-heading"><?php echo Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', '<strong>Space User</strong> Reputation'); ?></div>
    <div>
        <?php if (count($users) > 0) : ?>         
            <ul class="media-list">
                <?php foreach ($users as $reputationUser) : ?>
                    <?php
                    $user = User::findOne(['id' => $reputationUser->user_id]);
                    if ($user == null)
                        continue;
                    ?>               
                    <!-- BEGIN: Reputation Results -->
                    <li>
                        <div class="media">                            
                            <div class="pull-right">
                                <strong>
                                    <?php
                                    if ($function == ReputationUser::LINEAR) {
                                        echo Html::encode($reputationUser->value);
                                    } else {
                                        echo Html::encode($reputationUser->value) . '%';
                                    }
                                    ?>
                                </strong>
                            </div>
                            <a href="<?php echo $user->getUrl(); ?>" class="pull-left">
                                <img class="media-object img-rounded user-image user-<?php echo $user->guid; ?>" alt="32x32"
                                     data-src="holder.js/32x32" style="width: 32px; height: 32px;"
                                     src="<?php echo $user->getProfileImage()->getUrl(); ?>"
                                     width="40" height="40"/>
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <strong><?php echo Html::a($user->getDisplayName(), $user->getUrl()); ?></strong>                                 
                                </h4>
                                <h5><?php echo Html::encode($user->profile->title); ?></h5>
                                <?php $tag_count = 0; ?>
                                <?php if ($user->hasTags()) : ?>
                                    <?php foreach ($user->getTags() as $tag): ?>
                                        <?php if ($tag_count <= 5) { ?>
                                            <?php echo Html::a(Html::encode($tag), Url::to(['/directory/directory/members', 'keyword' => $tag]), array('class' => 'label label-default')); ?>
                                            <?php
                                            $tag_count++;
                                        }
                                        ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>                  
                <?php endforeach; ?>
                <!-- END: Reputation Results -->
            </ul>  
        <?php else: ?>
            <div class="media">          
                <div class="media-body">         
                    <h4 class="media-heading"><?php echo Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', 'No Reputation found!'); ?>
                        <i class="fa fa-info-circle tt text-break" data-toggle="tooltip" data-placement="top" 
                           title="<?php echo Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', 'You can only see reputation the user shares.'); ?>"></i>
                    </h4>        
                    <div><?php echo Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', 'Change your settings to share your Reputation on this Space'); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="panel-body">
            <?php if (count($users) == $maxUsers) : ?>
                <br>
                <a href="<?php echo $contentContainer->createUrl('//reputation/space/stats'); ?>" class="btn btn-default btn-sm"><?php echo Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', 'Show all'); ?></a>
            <?php endif; ?>
            <div class="pull-right">
                <?php echo Html::a(Yii::t('ReputationModule.widgets_views_spaceUserReputationWidget', 'Settings'), array('//reputation/profile/config', 'uguid' => Yii::$app->user->guid), array('class' => 'btn btn-default btn-sm')); ?>        
            </div>                                   
        </div>
    </div>
</div>
