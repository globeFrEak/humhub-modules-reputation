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
use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\admin\components\Controller;
use humhub\modules\space\models\Space;

/**
 * All user reputation actions a admin can use and see
 *
 * @author Anton Kurnitzky
 */
class AdminController extends Controller {
    /*
     * Initialize user reputation overview
     *
     * force update can be triggered by appending &forceUpdate at the end of the url
     * otherwise cache is used
     */

    public function actionIndex() {

        $query = ReputationUser::find()->orderBy('reputation_user.value DESC');

        $reputations = $query->all();

        return $this->render('index', array(
                    'reputations' => $reputations,
        ));
    }

    public function actionRecalculate() {
        $spaces = Space::find()->all();
        foreach ($spaces as $space) {
            if ($space->isModuleEnabled('reputation')) {
                ReputationUser::updateUserReputation($space, true);
                ReputationContent::updateContentReputation($space, true);
            }
        }
       return $this->redirect(['/reputation/admin']);
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
