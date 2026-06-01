<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\migrations;

use common\components\migration\BaseMigration;
use yii\base\NotSupportedException;

/**
 * Создание таблицы групп (разделов) статических страниц — Nested Sets дерево
 */
class m260407_100000_create_page_groups_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%page_groups}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id'          => $this->primaryKey(),
            'lft'         => $this->integer()->notNull()->defaultValue(0)
                ->comment('Левый ключ Nested Sets'),
            'rgt'         => $this->integer()->notNull()->defaultValue(0)
                ->comment('Правый ключ Nested Sets'),
            'depth'       => $this->integer()->notNull()->defaultValue(0)
                ->comment('Глубина узла'),
            'tree'        => $this->integer()->notNull()->defaultValue(0)
                ->comment('Идентификатор дерева'),
            'name'        => $this->string(255)->notNull()
                ->comment('Название группы'),
            'slug'        => $this->string(255)->notNull()
                ->comment('Slug группы'),
            'description' => $this->text()->null()->defaultValue(null)
                ->comment('Описание группы'),
            'status'      => $this->smallInteger(1)->notNull()->defaultValue(1)
                ->comment('Статус: 0 — неактивна, 1 — активна'),
            'sort_order'  => $this->integer()->notNull()->defaultValue(0)
                ->comment('Порядок сортировки корневых узлов'),
            'meta_json'   => $this->text()->notNull()
                ->comment('JSON SEO-метаданных'),
        ], $this->tableOptions);

        $this->addCommentOnTable(static::TABLE_NAME, 'Группы (разделы) статических страниц');

        $this->createIndexes(static::TABLE_NAME, 'slug', false, true);
        $this->createIndexes(static::TABLE_NAME, 'lft', false, false);
        $this->createIndexes(static::TABLE_NAME, 'rgt', false, false);
        $this->createIndexes(static::TABLE_NAME, 'tree', false, false);
        $this->createIndexes(static::TABLE_NAME, 'depth', false, false);
        $this->createIndexes(static::TABLE_NAME, 'status', false, false);

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
