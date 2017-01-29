<?php

/**
 * Description of humhub\modules\reputation\controllers\WallController
 * * The SpaceController for content reputation
 * * Show different sorting options to get a better overview over popular posts
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\controllers;

use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\content\components\ContentContainerController;

class SpaceController extends ContentContainerController {

    /**
     * @inheritdoc
     */
    public function actions() {
        return array(
            'stream' => array(
                'class' => \humhub\modules\reputation\components\StreamAction::className(),
                'sort' => \humhub\modules\reputation\components\StreamAction::MODE_HOT,
                'contentContainer' => $this->contentContainer
            ),
        );
    }

    /**
     * Shows the reputation_content space
     */
    public function actionIndex() {
        $forceUpdate = false;
        if (isset($_GET['forceUpdate'])) {
            $forceUpdate = true;
        }

        $space = $this->contentContainer;
        $canCreatePosts = $space->permissionManager->can(new \humhub\modules\post\permissions\CreatePost());
        $isMember = $space->isMember();

        ReputationContent::updateContentReputation($space, $forceUpdate);
        return $this->render('index', [
                    'space' => $space,
                    'canCreatePosts' => $canCreatePosts,
                    'isMember' => $isMember
        ]);     
    }

}
