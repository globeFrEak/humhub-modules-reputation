<?php

/**
 * Description of humhub\modules\reputation\controllers\ProfileController
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\controllers;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Membership;
use humhub\modules\reputation\models\ReputationUser;

class ProfileController extends \humhub\modules\content\components\ContentContainerController {

    /**
     * Action that renders the list view.
     * @see views/profile/config.php
     */
    public function actionConfig() {

        if (isset($_POST['reputationUsers'])) {
            $user = User::findIdentityByAccessToken(Yii::$app->getRequest()->get('uguid'));
            $userSpaces = Membership::GetUserSpaces($user->id);
            foreach ($userSpaces as $space) {
                $getPost = Yii::$app->getRequest()->post('reputationUser_' . $space->id);
                if (isset($getPost)) {
                    $userSettings = $getPost;
                    $params = [':spaceId' => $space->id, ':userId' => $user->id];
                    $query = ReputationUser::find();
                    $query->where('space_id=:spaceId AND user_id=:userId', $params);
                    $result = $query->one();
                    if ($result != null) {
                        $result->visibility = (isset($userSettings['visibility']) && $userSettings['visibility'] == 1) ? 1 : 0;
                        $result->save();
                    }
                }
            }
        }

        $user = User::findIdentityByAccessToken(Yii::$app->getRequest()->get('uguid'));                
        $params = [':userId' => $user->id];
        $query = ReputationUser::find();
        $query->where('user_id=:userId', $params);
        $query->leftJoin('space', 'space.id = reputation_user.space_id');
        $query->orderBy('reputation_user.space_id ASC');
        $result = $query->all();

        $itemCount = count($result);

        $pagination = new \yii\data\Pagination(['totalCount' => $itemCount]);
        return $this->render('config', array(
                    'contentContainer' => $this->contentContainer,
                    'user' => $user,
                    'reputations' => $result,                   
                    'pagination' => $pagination,
        ));
    }

}
