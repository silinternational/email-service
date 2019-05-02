<?php

use yii\db\Migration;

/**
 * Handles adding send_after to table `email`.
 */
class m190426_202115_add_send_after_column_to_email_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{email}}', 'send_after', 'int(11) null');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{email}}', 'send_after');
    }
}
