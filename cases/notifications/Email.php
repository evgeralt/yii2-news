<?php

namespace app\cases\notifications;

use Yii;

class Email implements Transport
{
    public function getId(): string
    {
        return 'email';
    }

    public function notify(MessageDto $messageDto): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailNotification-html'],
                ['message' => $messageDto->getMessage()]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
            ->setTo($messageDto->getEmail())
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}