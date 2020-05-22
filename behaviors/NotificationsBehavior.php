<?php

namespace app\behaviors;

use app\cases\notifications\MessageDto;
use app\components\Notifications;
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
        $user = User::identity();
        if (NotificationSettings::has($event->name)) {
            /** @var Notifications $notifications */
            $notifications = \Yii::$app->notifications;
            $notifications->notify(new MessageDto($user, $this->messages[$event->name]));
        }
    }
}