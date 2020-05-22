<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use function in_array;

/**
 * This is the model class for table "notification_settings".
 *
 * @property int         $id
 * @property int         $user_id
 * @property string|null $event
 *
 * @property User        $user
 */
class NotificationSettings extends \yii\db\ActiveRecord
{
    public const USER_SIGNUP = 'user.signup';
    public const USER_PASSWORD_CHANGES = 'user.passwordChanges';
    public const NEWS_CREATE = 'news.create';
    public const NEWS_DELETE = 'news.delete';
    public const ALLOWED_SETTINGS = [
        NotificationSettings::USER_SIGNUP,
        NotificationSettings::USER_PASSWORD_CHANGES,
        NotificationSettings::NEWS_CREATE,
        NotificationSettings::NEWS_DELETE,
    ];
    private static $events;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['event'], 'string', 'max' => 50],
            [['user_id'], 'unique', 'targetAttribute' => ['user_id', 'event']],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id',
                ],
                'value' => function () {
                    return Yii::$app->user->identity->getId();
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'event' => 'Event',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function has(string $event): bool
    {
        $result = false;
        if (Yii::$app->user->isGuest) {
            return $result;
        }
        if (self::$events === null) {
            self::$events = self::getEvents(Yii::$app->user->getId());
        }

        return in_array($event, self::$events, true);
    }

    public static function setEvents(int $userId, array $events): void
    {
        self::$events = $events;
        self::deleteAll(['user_id' => $userId]);
        $data = [];
        foreach ($events as $event) {
            $data[] = [$userId, $event];
        }
        if ($data) {
            Yii::$app->db
                ->createCommand()
                ->batchInsert(self::tableName(), ['user_id', 'event'], $data)
                ->execute();
        }
    }

    public static function getEvents(int $userId): array
    {
        if (self::$events === null) {
            self::$events = self::find()
                ->select('event')
                ->where(['user_id' => $userId])
                ->column();
        }

        return self::$events;
    }
}
