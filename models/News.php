<?php

namespace app\models;

use app\behaviors\NotificationsBehavior;
use app\interfaces\MultipleUsersNotification;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property int         $id
 * @property int         $author_id
 * @property string|null $text
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User        $author
 */
class News extends \yii\db\ActiveRecord implements MultipleUsersNotification
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id'], 'required'],
            [['author_id'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'author_id',
                ],
                'value' => function () {
                    return Yii::$app->user->identity->getId();
                },
            ],
            [
                'class' => NotificationsBehavior::class,
                'messages' => [
                    NotificationSettings::NEWS_CREATE => 'News create',
                    NotificationSettings::NEWS_DELETE => 'News delete',
                ],
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
            'author_id' => 'Author ID',
            'text' => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function getUsersForNotify(): array
    {
        return User::find()
            ->select('email')
            ->where(['not', ['id' => $this->author_id]])
            ->column();
    }
}
