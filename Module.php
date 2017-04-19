<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) 
 * */

namespace humhub\modules\reputation;

use \yii;
use yii\helpers\Url;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;

class Module extends ContentContainerModule {

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes() {
        return [
            Space::className(),
            User::className(),
        ];
    }

    /**
     * @inheritdoc
     */
//    public function getConfigUrl() {
//        return Url::to(['/reputation/admin/config']);
//    }
    
     /**
     * @inheritdoc
     */
     public function enable()
    {
       parent::enable();
    }
    
      /**
     * @inheritdoc
     */
     public function disable()
    {
       parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function enableContentContainer(ContentContainerActiveRecord $container) {
        parent::enableContentContainer($container);
        if ($container instanceof Space) {
            ReputationUser::updateUserReputation($container, true);
            ReputationContent::updateContentReputation($container, true);
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container) {
        if ($container instanceof Space) {
            return $container->createUrl('/reputation/space/settings');
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container) {
        $this->settings->contentContainer($container)->deleteAll();
        foreach (ReputationUser::findAll(['id' => $container->id]) as $reputationSpace) {
            $reputationSpace->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container) {
        return Yii::t('ReputationModule.base', 'Reputation');
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription(ContentContainerActiveRecord $container) {
        return Yii::t('ReputationModule.base', 'This Module Integrates A Reputation System Into HumHub. It Works With HumHub 1.2.x +.');
    }

}
