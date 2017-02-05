<?php

/**
 * Description of humhub\modules\reputation\controllers\AdminController
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationBase;
use humhub\modules\content\components\ContentContainerController;

/**
 * All user reputation actions a admin can use and see
 *
 * @author Anton Kurnitzky
 */
class AdminController extends ContentContainerController {
//
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

    /*
     * Allow only space admins to see configuration
     */
    public function beforeAction($action) {
        if (!$this->contentContainer->permissionManager->can(new \humhub\modules\content\permissions\ManageContent())) {
            throw new HttpException(400, 'Access denied!');
        }
        return parent::beforeAction($action);
    }

    /*
     * Initialize user reputation overview
     *
     * force update can be triggered by appending &forceUpdate at the end of the url
     * otherwise cache is used
     */

    public function actionIndex() {        
        $forceUpdate = false;
        if (Yii::$app->request->get('forceUpdate') === 1) {
            $forceUpdate = true;               
        }
        $space = $this->contentContainer;
        ReputationUser::updateUserReputation($space, $forceUpdate);
        $params = [':spaceId' => $space->id];
        $query = ReputationUser::find();
        $query->where('space_id=:spaceId', $params);
        $query->orderBy('reputation_user.value DESC');        

        $countQuery = clone $query;
        $itemCount = $countQuery->count();        
        $pagination = new \yii\data\Pagination(['totalCount' => $itemCount, 'pageSize' => 10]);
        $query->offset($pagination->offset)->limit($pagination->limit);
        
        $reputations = $query->all();

        $module = Yii::$app->getModule('reputation');
        $function = $module->settings->space()->get('functions', ReputationBase::DEFAULT_FUNCTION);

        $lastUpdatedBefore = $this->GetLastUpdateTimeInMinutes($reputations);        

        return $this->render('index', array(
                    'function' => $function,
                    'space' => $space,
                    'reputations' => $reputations,
                    'pagination' => $pagination,
                    'lastUpdatedBefore' => $lastUpdatedBefore,
        ));
    }

    /**
     * Get time in minutes since last update occurred
     *
     * @param $criteria
     * @return string: The time elapsed since the last update
     */
    private function GetLastUpdateTimeInMinutes($criteria) {
        $now = new \DateTime();
        $lastUpdateTime = new \DateTime($criteria[0]->updated_at);
        $lastUpdatedBefore = $lastUpdateTime->diff($now)->format('%i');

        return $lastUpdatedBefore;
    }

}
