<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\migrations;

use common\components\migration\BaseMigration;
use Yii;
use yii\db\Exception;

class m260407_100100_create_page_foreign_key_constraints extends BaseMigration
{

    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        parent::safeUp();

        Yii::$app->getDb()->createCommand("SET foreign_key_checks = 0")->execute();

        // FK group_id → page_groups.id (SET NULL при удалении группы)
        $this->createFKs(
            m250226_130300_create_page_pages_table::TABLE_NAME,
            'group_id',
            m260407_100000_create_page_groups_table::TABLE_NAME,
            'id',
            'SET NULL',
            'CASCADE',
        );

        Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
    }

    public function safeDown(): void
    {
        // Отменяем действия по умолчанию,
        // так как \common\components\migration\BaseMigration::safeDown() вызывает static::TABLE_NAME,
        // которого в данной миграции не существует.
        // Так же, \common\components\migration\BaseMigration::safeDown() при удалении таблиц сам удалит у них все индексы и внешние ключи.

        // parent::safeDown();
    }

}
