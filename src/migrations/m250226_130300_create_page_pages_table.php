<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<n>' */
class m250226_130300_create_page_pages_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%page_pages}}';

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
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->null()->defaultValue(null)
                ->comment('ID группы (раздела) After id'), // необязательная ссылка на группу
            'excerpt' => $this->text()->null()->defaultValue(null)
                ->comment('Краткое описание'),
            'title' => $this->string(255)->notNull()
                ->comment('Заголовок страницы'),
            'slug' => $this->string(255)->notNull()
                ->comment('Slug страницы'),
            'content' => 'LONGTEXT NULL DEFAULT NULL COMMENT "Контент страницы"',
            'meta_json' => $this->text()->notNull()
                ->comment('JSON of meta-obj'),
            'is_markdown' => $this->smallInteger(1)->notNull()->defaultValue(0)
                ->comment('Флаг, используется ли markdown синтаксис'),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0)
                ->comment('0=черновик, 1=опубликована, 2=архив'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)
                ->comment('Порядок сортировки'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')
                ->comment('Дата создания'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')->append('ON UPDATE NOW()')
                ->comment('Дата обновления'),


        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Модуль статических страниц');

        $this->createIndexes(static::TABLE_NAME, 'slug', false, true);
        $this->createIndexes(static::TABLE_NAME, 'group_id', false, false);
        $this->createIndexes(static::TABLE_NAME, 'status', false, false);
        $this->createIndexes(static::TABLE_NAME, 'sort_order', false, false);

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
