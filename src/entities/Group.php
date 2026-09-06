<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\entities;

use Besnovatyj\Meta\Meta;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Page\entities\queries\GroupQuery;
use Besnovatyj\TreeManager\Manager\entities\Node;
use yii\db\ActiveQuery;

/**
 * Группа (раздел) статических страниц — иерархическая структура (Nested Sets)
 *
 * @property int    $id
 * @property int    $lft
 * @property int    $rgt
 * @property int    $depth
 * @property int    $tree
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int    $status
 * @property int    $sort_order
 *
 * @property Meta   $meta
 *
 * @mixin MetaBehavior
 */
class Group extends Node
{
    /** Раздел скрыт: ни он сам, ни его страницы не доступны на фронте. */
    public const int STATUS_INACTIVE = 0;

    /** Раздел опубликован. */
    public const int STATUS_ACTIVE = 1;

    /** @var Meta */
    public Meta $meta;

    /**
     * Создать новую группу
     */
    public static function create(string $name, string $slug, string $description, Meta $meta): self
    {
        $group = new static();
        $group->name = $name;
        $group->slug = $slug;
        $group->description = $description;
        $group->meta = $meta;
        return $group;
    }

    /**
     * Обновить данные группы
     */
    public function edit(string $name, string $slug, string $description, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->meta = $meta;
    }

    /**
     * Переключить статус группы
     */
    public function changeStatus(): void
    {
        $this->status = $this->isActive() ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;
    }

    /**
     * Опубликован ли сам раздел (без учёта предков — их проверяет {@see GroupQuery::visible()}).
     */
    public function isActive(): bool
    {
        return (int)$this->status === self::STATUS_ACTIVE;
    }

    /**
     * Получить SEO-заголовок группы
     */
    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    /**
     * Получить страницы, принадлежащие этой группе
     */
    public function getPages(): ActiveQuery
    {
        return $this->hasMany(Page::class, ['group_id' => 'id']);
    }

    /**
     * Подсчёт опубликованных страниц в группе
     */
    public function countPublishedPages(): int
    {
        return (int)$this->getPages()->andWhere(['status' => Page::STATUS_PUBLISHED])->count();
    }

    public static function tableName(): string
    {
        return '{{%page_groups}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            ...parent::behaviors(),
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): GroupQuery
    {
        return new GroupQuery(static::class);
    }
}
