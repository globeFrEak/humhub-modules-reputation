<?php

/**
 * Description of humhub\modules\reputation\controllers\AdminController
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\controllers;

use Yii;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\reputation\models\ReputationBase;
use humhub\modules\reputation\models\SpaceSettings;
use humhub\modules\content\components\ContentContainerController;

/**
 * All user reputation actions a admin can use and see
 *
 * @author Anton Kurnitzky
 */
class AdminController extends ContentContainerController {

    /** access level of the user currently logged in. 0 -> no write access / 1 -> create links and edit own links / 2 -> full write access. * */
    public $accessLevel = 0;

    /**
     * Automatically loads the underlying contentContainer (User/Space) by using
     * the uguid/sguid request parameter
     *
     * @return boolean
     */
    public function init() {
        $retVal = parent::init();
        $this->accessLevel = $this->getAccessLevel();
        return $retVal;
    }

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Get the acces level to the linklist of the currently logged in user.
     * @return number 0 -> no write access / 1 -> create links and edit own links / 2 -> full write access
     */
    private function getAccessLevel() {
        if ($this->contentContainer instanceof User) {
            return $this->contentContainer->id == Yii::$app->user->id ? 2 : 0;
        } else if ($this->contentContainer instanceof Space) {
            return $this->contentContainer->isAdmin(Yii::$app->user->id) ? 2 : 1;
        }
    }

    /*
     * Allow only space admins to see configuration
     */

    public function beforeAction($action) {
        if (!$this->getSpace()->isAdmin()) {
            throw new HttpException(403, 'Access denied - Space Administrator only!');
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
        if (isset($_GET['forceUpdate'])) {
            $forceUpdate = true;
        }

        $space = $this->contentContainer;

        ReputationUser::updateUserReputation($space, $forceUpdate);

        $params = [':spaceId' => $space->id];
        $query = ReputationUser::find();
        $query->where('space_id=:spaceId', $params);
        $query->orderBy('reputation_user.space_id ASC');
        $reputations = $query->all();

        $itemCount = count($reputations);

        $pagination = new \yii\data\Pagination(['totalCount' => $itemCount]);

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

    /**
     * Initialize configuration view
     * Allows the user to set a bunch of parameters for reputation settings inside this space
     *
     * @throws CException
     */
    public function actionSpaceSettings() {

        $space = $this->contentContainer;
        $module = Yii::$app->getModule('reputation');
        $form = new SpaceSettings();

        $form->functions = $module->settings->space()->get('functions', ReputationBase::DEFAULT_FUNCTION);
        $form->logarithmBase = $module->settings->space()->get('logarithm_base', ReputationBase::DEFAULT_LOGARITHM_BASE);
        $form->create_content = $module->settings->space()->get('create_content', ReputationBase::DEFAULT_CREATE_CONTENT);
        $form->smb_likes_content = $module->settings->space()->get('smb_likes_content', ReputationBase::DEFAULT_SMB_LIKES_CONTENT);
        $form->smb_favorites_content = $module->settings->space()->get('smb_favorites_content', ReputationBase::DEFAULT_SMB_FAVORITES_CONTENT);
        $form->smb_comments_content = $module->settings->space()->get('smb_comments_content', ReputationBase::DEFAULT_SMB_COMMENTS_CONTENT);
        $form->daily_limit = $module->settings->space()->get('daily_limit', ReputationBase::DEFAULT_DAILY_LIMIT);
        $form->decrease_weighting = $module->settings->space()->get('decrease_weighting', ReputationBase::DEFAULT_DECREASE_WEIGHTING);
        $form->cron_job = $module->settings->space()->get('cron_job', ReputationBase::DEFAULT_CRON_JOB);
        $form->lambda_short = $module->settings->space()->get('lambda_short', ReputationBase::DEFAULT_LAMBDA_SHORT);
        $form->lambda_long = $module->settings->space()->get('lambda_long', ReputationBase::DEFAULT_LAMBDA_LONG);

        return $this->render('spaceSettings', array('model' => $form, 'space' => $space));
    }

    public function actionSpaceSettingsSubmit() {

        $space = $this->contentContainer;
        $module = Yii::$app->getModule('reputation');
        $form = new SpaceSettings();
        $form->load(Yii::$app->request->post());

        if ($form->validate()) {
            $form->functions = $module->settings->space()->set('functions', $form->functions);
            $form->logarithmBase = $module->settings->space()->set('logarithm_base', $form->logarithmBase);
            $form->create_content = $module->settings->space()->set('create_content', $form->create_content);
            $form->smb_likes_content = $module->settings->space()->set('smb_likes_content', $form->smb_likes_content);
            $form->smb_favorites_content = $module->settings->space()->set('smb_favorites_content', $form->smb_favorites_content);
            $form->smb_comments_content = $module->settings->space()->set('smb_comments_content', $form->smb_comments_content);
            $form->daily_limit = $module->settings->space()->set('daily_limit', $form->daily_limit);
            $form->decrease_weighting = $module->settings->space()->set('decrease_weighting', $form->decrease_weighting);
            $form->cron_job = $module->settings->space()->set('cron_job', $form->cron_job);
            $form->lambda_short = $module->settings->space()->set('lambda_short', $form->lambda_short);
            $form->lambda_long = $module->settings->space()->set('lambda_long', $form->lambda_long);

            ReputationContent::updateContentReputation($space, true);
            ReputationUser::updateUserReputation($space, true);

            $this->redirect(['/reputation/admin/space-settings', 'sguid' => $space->guid]);
        } else {
            $form->functions = $module->settings->space()->get('functions', ReputationBase::DEFAULT_FUNCTION);
            $form->logarithmBase = $module->settings->space()->get('logarithm_base', ReputationBase::DEFAULT_LOGARITHM_BASE);
            $form->create_content = $module->settings->space()->get('create_content', ReputationBase::DEFAULT_CREATE_CONTENT);
            $form->smb_likes_content = $module->settings->space()->get('smb_likes_content', ReputationBase::DEFAULT_SMB_LIKES_CONTENT);
            $form->smb_favorites_content = $module->settings->space()->get('smb_favorites_content', ReputationBase::DEFAULT_SMB_FAVORITES_CONTENT);
            $form->smb_comments_content = $module->settings->space()->get('smb_comments_content', ReputationBase::DEFAULT_SMB_COMMENTS_CONTENT);
            $form->daily_limit = $module->settings->space()->get('daily_limit', ReputationBase::DEFAULT_DAILY_LIMIT);
            $form->decrease_weighting = $module->settings->space()->get('decrease_weighting', ReputationBase::DEFAULT_DECREASE_WEIGHTING);
            $form->cron_job = $module->settings->space()->get('cron_job', ReputationBase::DEFAULT_CRON_JOB);
            $form->lambda_short = $module->settings->space()->get('lambda_short', ReputationBase::DEFAULT_LAMBDA_SHORT);
            $form->lambda_long = $module->settings->space()->get('lambda_long', ReputationBase::DEFAULT_LAMBDA_LONG);
        }
        return $this->render('spaceSettings', array('model' => $form, 'space' => $space));
    }

}
