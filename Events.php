<?php
/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation;

use Yii;
use humhub\modules\space\behaviors\SpaceSetting;
use humhub\modules\space\models\Space;
use humhub\modules\reputation\models\ReputationBase;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\space\behaviors\SpaceModelModules;
use humhub\modules\space\modules\manage\widgets\DefaultMenu;

class Events extends \yii\base\Object {

    /**
     * Recalculate user and content reputation every hour
     * Only do this in spaces where reputation module is enabled
     *
     * @param $event
     * @throws CException
     */
    public static function onCronHourlyRun($event) {       

        foreach (Space::findAll() as $space) {
            if (SpaceModelModules::isModuleEnabled('reputation')) {
                $cronJobEnabled = SpaceSetting::getSetting('cron_job', 'reputation', ReputationBase::DEFAULT_CRON_JOB);
                if ($cronJobEnabled) {
                    print "- Recalculating reputation for space: $space->id  $space->name\n";
                    ReputationUser::updateUserReputation($space, true);
                    ReputationContent::updateContentReputation($space, true);
                }
            }
        }
    }

    /**
     * On user delete, also delete all reputation of this user
     *
     * @param type $event
     */
    public static function onUserDelete($event) {
        foreach (ReputationUser::findAllByAttributes(array('user_id' => $event->sender->id)) as $reputationUser) {
            $reputationUser->delete();
        }
    }

    /**
     * When a user leaves a space remove the user reputation for this space
     *
     * @param type $event
     */
    public static function onSpaceMembershipDelete($event) {
        foreach (ReputationUser::findAllByAttributes(array('user_id' => $event->sender->user_id, 'space_id' => $event->sender->space_id)) as $reputationUser) {
            $reputationUser->delete();
        }
    }

    /**
     * On space delete, also delete all reputation of this space
     *
     * @param type $event
     */
    public static function onSpaceDelete($event) {
        foreach (ReputationUser::findAllByAttributes(array('space_id' => $event->sender->id)) as $reputationSpace) {
            $reputationSpace->delete();
        }
    }

    /**
     * On content delete, also delete the content reputation
     *
     * @param type $event
     */
    public static function onContentDelete($event) {
        foreach (ReputationContent::findAllByAttributes(array('object_id' => $event->sender->id)) as $reputationContent) {
            $reputationContent->delete();
        }
    }

    /**
     * Show reputation menu in user profile
     *
     * @param $event
     * 
     * GEHT soweit
     * 
     */
    public static function onProfileMenuInit($event) {
        if ($event->sender->user !== null && $event->sender->user->isModuleEnabled('reputation')) {
            $event->sender->addItem(array(
                'label' => Yii::t('ReputationModule.base', 'User Reputation'),
                'group' => 'profile',
                'url' => $event->sender->user->createUrl('/reputation/profile'),
                'isActive' => Yii::$app->controller->module && Yii::$app->controller->module->id == 'reputation',
                'sortOrder' => 1000,
            ));
        }
    }

    /*
     * Show reputation menu in space admin menu
     */

    public static function onSpaceAdminMenuWidgetInit($event) {
        if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('reputation') && $event->sender->space->isAdmin()) {
            $event->sender->addItem(['label' => Yii::t('ReputationModule.base', 'User Reputation'),              
                'url' => $event->sender->space->createUrl('/reputation/admin'),                    
                'sortOrder' => 300,
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id === 'reputation'),
                ]);
        }
    }

    /*
     * Show reputation menu in space menu
     */

    public static function onSpaceMenuInit($event) {
        if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('reputation') && $event->sender->space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('ReputationModule.base', 'Hot'),
                'url' => $event->sender->space->createUrl('/reputation/wall'),
                'icon' => '<i class="fa fa-fire"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'reputation'),
                'group' => 'modules',
            ));
        }
    }  

    /**
     * On run of integrity check command, validate all module data
     *
     * @param type $event
     */
    public static function onIntegrityCheck($event) {
        $integrityChecker = $event->sender;
        $integrityChecker->showTestHeadline("Validating Reputation Content (" . ReputationContent::count() . " entries)");
        $integrityChecker->showTestHeadline("Validating Reputation User (" . ReputationUser::count() . " entries)");
    }

}
