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

//    /** access level of the user currently logged in. 0 -> no write access / 1 -> create links and edit own links / 2 -> full write access. * */
//    public $accessLevel = 0;
//
//    /**
//     * Automatically loads the underlying contentContainer (User/Space) by using
//     * the uguid/sguid request parameter
//     *
//     * @return boolean
//     */
//    public function init() {
//        $retVal = parent::init();
//        $this->accessLevel = $this->getAccessLevel();
//        return $retVal;
//    }
//
//    /**
//     * @return array action filters
//     */
//    public function filters() {
//        return array(
//            'accessControl', // perform access control for CRUD operations
//        );
//    }
//
//    /**
//     * Get the acces level to the linklist of the currently logged in user.
//     * @return number 0 -> no write access / 1 -> create links and edit own links / 2 -> full write access
//     */
//    private function getAccessLevel() {
//        if ($this->contentContainer instanceof User) {
//            return $this->contentContainer->id == Yii::$app->user->id ? 2 : 0;
//        } else if ($this->contentContainer instanceof Space) {
//            return $this->contentContainer->isAdmin(Yii::$app->user->id) ? 2 : 1;
//        }
//    }
//
//    /**
//     * Specifies the access control rules.
//     * This method is used by the 'accessControl' filter.
//     * @return array access control rules
//     */
//    public function accessRules() {
//        return array(
//            array('allow', // allow authenticated user to perform 'create' and 'update' actions
//                'users' => array('@'),
//            ),
//            array('deny', // deny all users
//                'users' => array('*'),
//            ),
//        );
//    }

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
        // TODO was ist das?!
        //Yii::$app->user->setFlash('data-saved', Yii::t('SpaceModule.controllers_AdminController', 'Saved'));

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
