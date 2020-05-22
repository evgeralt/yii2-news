<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification_settings}}`.
 */
class m200522_061715_create_notification_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification_settings}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'event' => $this->string(50),
        ]);

        $this->createIndex('ui_user_id_event', '{{%notification_settings}}', ['user_id', 'event'], true);
        $this->addForeignKey('fk_notification_settings_user_user_id', '{{%notification_settings}}', 'user_id', '{{%user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notification_settings}}');
    }
}
