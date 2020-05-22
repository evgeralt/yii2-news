<?php

namespace app\behaviors;

use app\cases\notifications\MessageDto;
use app\components\Notifications;
use app\interfaces\MultipleUsersNotification;
use app\models\NotificationSettings;
use app\models\User;
use yii\base\Behavior;
use yii\base\Event;
use function array_fill_keys;
use function array_keys;

class NotificationsBehavior extends Behavior
{
    /** @var string[] */
    public $messages;

    /**
     * @inheritDoc
     */
    public function events()
    {
        return array_fill_keys(array_keys($this->messages), 'notify');
    }

    public function notify(Event $event): void
    {
        /** @var User[] $users */
        $users = $this->owner instanceof MultipleUsersNotification
            ? $this->owner->getUsersForNotify()
            : [User::identity()];
        foreach ($users as $user) {
            if (NotificationSettings::has($user->id, $event->name) || $event->name === NotificationSettings::USER_SIGNUP) {
                /** @var Notifications $notifications */
                $notifications = \Yii::$app->notifications;
                $notifications->notify(new MessageDto($user->email, $this->messages[$event->name]));
            }
            NotificationSettings::clearEventsCache();
        }
    }
}