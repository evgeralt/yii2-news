<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Cabinet form
 */
class CabinetForm extends Model
{
    public $password;
    public $password_repeat;
    public $notification_settings;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'trim'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat'],
            ['notification_settings', 'each', 'rule' => ['in', 'range' => NotificationSettings::ALLOWED_SETTINGS]],
        ];
    }

    public static function findCurrent(): self
    {
        $model = new self();
        $model->notification_settings = NotificationSettings::getEvents(Yii::$app->user->getId());

        return $model;
    }

    public function saveSettings(): bool
    {
        $result = false;
        if (!$this->validate()) {
            return $result;
        }
        $user = User::identity();
        if ($this->password) {
            $user->setPassword($this->password);
        }
        NotificationSettings::setEvents($user->id, $this->notification_settings ?: []);
        $user->save();

        return $result;
    }
}