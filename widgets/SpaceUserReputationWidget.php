<?php

namespace humhub\modules\reputation\widgets;

use humhub\components\Widget;
use \Yii;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationBase;

class SpaceUserReputationWidget extends Widget {

    /**
     * @var int maximum members to display
     */
    public $maxUsers = 5;
    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function run() {
        $module = Yii::$app->getModule('reputation');
        $function = $module->settings->space()->get('functions', ReputationBase::DEFAULT_FUNCTION);
        $query = ReputationUser::find()->where('space_id = :spaceId AND visibility = 1', [':spaceId' => $this->contentContainer->id])->orderBy("value DESC");
        $query->limit($this->maxUsers);
        return $this->render('spaceUserReputationWidget', ['contentContainer' => $this->contentContainer, 'maxUsers' => $this->maxUsers, 'users' => $query->all(), 'function' => $function]);
    }

}

?>