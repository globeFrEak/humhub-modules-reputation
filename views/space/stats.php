<?php
/*
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+)
 */

use yii\helpers\Html;
use humhub\modules\user\models\User;
use humhub\modules\reputation\models\ReputationUser;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', '<strong>Space member</strong> reputation'); ?>
    </div>
    <div class="panel-body">       
        <?php echo Yii::t('ReputationModule.views_adminReputation_show', 'In the area below, you see how much reputation each member inside this space has gained.'); ?>
        <br/><br/> 
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
                    $user = User::findOne(['id' => $reputation->user_id]);                    
                   if ($user == null)continue;
                    ?>
                    <tr>                        
                        <td width="32px" style="vertical-align: middle">                            
                            <a href="<?php echo $user->getUrl(); ?>" class="pull-left">
                                <img class="media-object img-rounded user-image user-<?php echo $user->guid; ?>" alt="32x32"
                                     data-src="holder.js/32x32" style="width: 32px; height: 32px;"
                                     src="<?php echo $user->getProfileImage()->getUrl(); ?>"
                                     width="40" height="40"/>
                            </a>
                        </td>
                        <td style="vertical-align:middle">
                            <strong><?php echo Html::a($user->getDisplayName(), $user->getUrl()); ?></strong>
                            <br/>
                        </td>

                        <td style="vertical-align:middle; text-align:center">
                            <strong>
                                <?php
                                if ($function == ReputationUser::LINEAR) {
                                    echo Html::encode($reputation->value);
                                } else {
                                    echo Html::encode($reputation->value) . '%';
                                }
                                ?>
                            </strong>
                        </td>

                    </tr>
                  <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination-container">
            <?php echo \humhub\widgets\LinkPager::widget(['pagination' => $pagination]); ?>
        </div>
    </div>
</div>