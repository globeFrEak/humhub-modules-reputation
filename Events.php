<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation;

use Yii;
use yii\helpers\Console;
use humhub\modules\space\models\Space;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\content\models\ContentContainerSetting;

class Events extends \yii\base\Object {

    /**
     * Recalculate user and content reputation every hour
     * Only do this in spaces where reputation module is enabled
     *
     * @param \yii\base\Event $event
     */
    public static function onCronHourlyRun($event) {
        $controller = $event->sender;
        $spaces = Space::find()->all();
        $count_spaces = count($spaces);

        $processed = 0;
        Console::startProgress($processed, $count_spaces, '[Module] calculate REPUTATION for user and content...', false);
        foreach ($spaces as $space) {
            if ($space->isModuleEnabled('reputation')) {
                $cronJobEnabled = ContentContainerSetting::findOne(['module_id' => 'reputation', 'contentcontainer_id' => $space->wall_id, 'name' => 'cron_job', 'value' => '1']);
                if ($cronJobEnabled) {
                    ReputationUser::updateUserReputation($space, true);
                    ReputationContent::updateContentReputation($space, true);
                    Console::updateProgress(++$processed, $count_spaces);
                }
            }
        }
        Console::endProgress(true);
        $controller->stdout('done - ' . $processed . ' spaces checked.' . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * On user delete, also delete all reputation of this user
     *
     * @param type $event
     */
    public static function onUserDelete($event) {
        foreach (ReputationUser::findAll(['user_id' => $event->sender->id]) as $reputationUser) {
            $reputationUser->delete();
        }
    }

    /**
     * When a user leaves a space remove the user reputation for this space
     *
     * @param type $event
     */
    public static function onSpaceMembershipDelete($event) {
        foreach (ReputationUser::findAll(['user_id' => $event->sender->user_id, 'space_id' => $event->sender->space_id]) as $reputationUser) {
            $reputationUser->delete();
        }
    }

    /**
     * On space delete, also delete all reputation of this space
     *
     * @param type $event
     */
    public static function onSpaceDelete($event) {
        foreach (ReputationUser::findAll(['space_id' => $event->sender->id]) as $reputationSpace) {
            $reputationSpace->delete();
        }
    }

    /**
     * On content delete, also delete the content reputation
     *
     * @param type $event
     */
    public static function onContentDelete($event) {
        foreach (ReputationContent::findAll(['content_id' => $event->sender->id]) as $reputationContent) {
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
                'url' => $event->sender->space->createUrl('/reputation/space'),
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
        $integrityChecker->showTestHeadline("Validating Reputation Content (" . ReputationContent::find()->count() . " entries)");
        $integrityChecker->showTestHeadline("Validating Reputation User (" . ReputationUser::find()->count() . " entries)");
    }

}
