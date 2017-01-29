<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;

class Module extends ContentContainerModule {

    public $controllerNamespace = 'humhub\modules\reputation\controllers';

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes() {
        return [
            User::className(),
            Space::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function enableContentContainer(ContentContainerActiveRecord $container) {

        $container->setSetting('enableWidget', 0, 'reputation');
        parent::enableContentContainer($container);
        if ($container instanceof Space) {
            ReputationUser::updateUserReputation($container, true);
            ReputationContent::updateContentReputation($container, true);
        }
    }

    /**
     * Returns the url to configure this module in a content container
     * 
     * @param ContentContainerActiveRecord $container
     * @return string the config url
     */
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container) {
        if ($container instanceof Space) {
            return $container->createUrl('/reputation/admin/space-settings');
        }
    }

}
