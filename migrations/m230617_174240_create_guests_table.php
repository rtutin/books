<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%guests}}`.
 */
class m230617_174240_create_guests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%guests}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(),
            'author_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%guests}}');
    }
}
