<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

use humhub\commands\CronController;
use humhub\modules\content\models\Content;
use humhub\commands\IntegrityController;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\modules\space\widgets\Menu;
use humhub\modules\space\modules\manage\widgets\DefaultMenu;
use humhub\modules\space\widgets\Sidebar;

return [
    'id' => 'reputation',
    'class' => 'humhub\modules\reputation\Module',
    'namespace' => 'humhub\modules\reputation',
    'events' => [
        ['class' => CronController::className(), 'event' => CronController::EVENT_ON_HOURLY_RUN, 'callback' => ['humhub\modules\reputation\Events', 'onCronHourlyRun']],
        ['class' => Content::className(), 'event' => Content::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\reputation\Events', 'onContentDelete']],
        ['class' => IntegrityController::className(), 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\reputation\Events', 'onIntegrityCheck']],
        ['class' => User::className(), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\reputation\Events', 'onUserDelete']],
        ['class' => Membership::className(), 'event' => Membership::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\reputation\Events', 'onSpaceMembershipDelete']],
        ['class' => Space::className(), 'event' => Space::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\reputation\Events', 'onSpaceDelete']],
        ['class' => Sidebar::className(), 'event' => Sidebar::EVENT_RUN, 'callback' => ['humhub\modules\reputation\Events', 'onSpaceSidebar']],
        ['class' => ProfileMenu::className(), 'event' => ProfileMenu::EVENT_INIT, 'callback' => ['humhub\modules\reputation\Events', 'onProfileMenuInit']],
        ['class' => DefaultMenu::className(), 'event' => DefaultMenu::EVENT_INIT, 'callback' => ['humhub\modules\reputation\Events', 'onSpaceAdminMenuWidgetInit']],
        ['class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\reputation\Events', 'onSpaceMenuInit']],   
    ],
];
?>