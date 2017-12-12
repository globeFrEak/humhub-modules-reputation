<?php
/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\user\models\User;
use humhub\modules\space\modules\manage\widgets\DefaultMenu;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', '<strong>Space member</strong> reputation'); ?>
    </div>
    <?= DefaultMenu::widget(['space' => $space]); ?>
    <div class="panel-body">

        <?php echo Yii::t('ReputationModule.views_adminReputation_show', 'Last Update: '); ?>
        <?php echo '<strong> ' . $lastUpdatedBefore . '</strong>' ?>
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', ' minutes ago'); ?>
        <?php echo '<br><br>' ?>
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', 'In the area below, you see how much reputation each member inside this space has gained.'); ?>
        <br/><br/>
        <?php if (count($reputations) > 0) : ?>        
            <ul class="media-list">
                <li>
                    <div class="media">
                        <div class="pull-right">
                            <?php echo Yii::t('ReputationModule.views_adminReputation_show', "Score"); ?>
                            <i class="fa fa-info-circle tt" data-toggle="tooltip" data-placement="top"
                               title="<?php echo Yii::t('ReputationModule.views_adminReputation_show', 'Reputation score of this user'); ?>"></i>                     
                        </div>  
                        <div class="pull-left">
                            <?php echo Yii::t('ReputationModule.views_adminReputation_show', "User"); ?>                            
                        </div> 
                    </div>                    
                </li>
                <?php foreach ($reputations as $reputationUser) : ?>
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
                </div></div>
        <?php endif; ?>       

        <div class="pagination-container">
            <?php echo \humhub\widgets\LinkPager::widget(['pagination' => $pagination]); ?>
        </div>

        <hr>
        <?php echo Html::a(Yii::t('ReputationModule.views_profileReputation_show', 'Update'), array('//reputation/admin', 'sguid' => $space->guid, 'forceUpdate' => 1), array('class' => 'btn btn-primary')); ?>       

        <div class="pull-right">
            <?php echo Html::a(Yii::t('ReputationModule.views_adminReputation_show', 'Configuration'), array('//reputation/space/settings', 'sguid' => $space->guid), array('class' => 'btn btn-warning')); ?>          
        </div>      
    </div>
</div>
