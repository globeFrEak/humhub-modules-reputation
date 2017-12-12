<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) 
 */

namespace humhub\modules\reputation;

use Yii;
use yii\helpers\Console;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\reputation\models\ReputationBase;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;

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
        Console::startProgress($processed, $count_spaces, '[Module] calculate REPUTATION for Spaces...', false);
        foreach ($spaces as $space) {           
            if ($space->isModuleEnabled('reputation')) {
                $cronJobEnabled = ReputationBase::getSpaceSettings($space);
                if ($cronJobEnabled['cron_job'] = 1) {
                    ReputationUser::updateUserReputation($space, true);
                    ReputationContent::updateContentReputation($space, true);
                    Console::updateProgress(++$processed, $count_spaces);
                }                
            }
        }
        Console::endProgress(true);
        $controller->stdout('done - ' . $processed . ' Spaces checked.' . PHP_EOL, Console::FG_GREEN);

        $users = User::find()->all();
        $count_users = count($spaces);
        Console::startProgress($processed, $count_users, '[Module] calculate REPUTATION for Users...', false);
        foreach ($users as $user) {
            if ($user->isModuleEnabled('reputation')) {
                Console::updateProgress(++$processed, $count_users);
            }
        }
        Console::endProgress(true);
        $controller->stdout('done - ' . $processed . ' Users checked.' . PHP_EOL, Console::FG_GREEN);
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
        foreach (ReputationUser::findAll(['id' => $event->sender->contentcontainer_id]) as $reputationSpace) {
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
     * @param type $event
     */
    public static function onProfileMenuInit($event) {
        if ($event->sender->user !== null && $event->sender->user->isModuleEnabled('reputation')) {
            $event->sender->addItem(array(
                'label' => Yii::t('ReputationModule.base', 'User Reputation'),
                'icon' => '<i class="fa fa-book"></i>',
                'group' => 'profile',
                'url' => $event->sender->user->createUrl('/reputation/profile/config'),
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'reputation' && Yii::$app->controller->id == 'profile' && Yii::$app->controller->action->id == 'config'),
                'sortOrder' => 1000,
            ));
        }
    }

    /*
     * Show reputation menu in space admin menu
     * 
     * @param type $event
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
     * 
     * @param type $event
     */

    public static function onSpaceMenuInit($event) {
        if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('reputation') && $event->sender->space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('ReputationModule.base', 'Hot'),
                'url' => $event->sender->space->createUrl('/reputation/space'),
                'icon' => '<i class="fa fa-fire"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'reputation' && Yii::$app->controller->id == 'space' && Yii::$app->controller->action->id == 'index'),
                'group' => 'modules',
                'sortOrder' => 200,
            ));
        }
    }

    /**
     * On run of integrity check command, validate all module data
     *
     * @param $event
     */
    public static function onIntegrityCheck($event) {
        $integrityChecker = $event->sender;
        $integrityChecker->showTestHeadline("Reputation Module (Content) (" . ReputationContent::find()->count() . " entries)");
        $integrityChecker->showTestHeadline("Reputation Module (User) (" . ReputationUser::find()->count() . " entries)");
    }

    /**
     * Add Space Widget (TODO when Reputation on this space enabled)  
     *
     * @param $event
     */
    public static function onSpaceSidebar($event) {
        if ($event->sender->space->isModuleEnabled('reputation')) {
            $event->sender->addWidget(widgets\SpaceUserReputationWidget::className(), array('contentContainer' => $event->sender->space), array('sortOrder' => 10));
        }
    }

}
