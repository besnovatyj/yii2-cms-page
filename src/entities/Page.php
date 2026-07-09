<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\entities;

use Besnovatyj\Meta\Meta;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Page\entities\queries\PageQuery;
use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Статическая страница
 *
 * @property int         $id
 * @property int|null    $group_id
 * @property string      $title
 * @property string      $slug
 * @property string|null $excerpt
 * @property string|null $content
 * @property int         $status
 * @property int         $sort_order
 * @property string      $created_at
 * @property string      $updated_at
 *
 * @property Meta        $meta
 * @property Group|null  $group
 *
 * @mixin MetaBehavior
 */
class Page extends ActiveRecord
{
    public const int STATUS_DRAFT = 0;
    public const int STATUS_PUBLISHED = 1;
    public const int STATUS_ARCHIVED = 2;

    /** @var Meta */
    public Meta $meta;

    /**
     * Создать новую страницу
     */
    public static function create(
        ?int $groupId,
        string $title,
        string $slug,
        ?string $excerpt,
        ?string $content,
        Meta $meta,
        int $status = self::STATUS_DRAFT,
        int $sortOrder = 0,
    ): self {
        $page = new static();
        $page->group_id = $groupId;
        $page->title = $title;
        $page->slug = $slug;
        $page->excerpt = $excerpt;
        $page->content = $content;
        $page->meta = $meta;
        $page->sort_order = $sortOrder;
        $page->status = $status;
        return $page;
    }

    /**
     * Обновить данные страницы
     */
    public function edit(
        ?int $groupId,
        string $title,
        string $slug,
        ?string $excerpt,
        ?string $content,
        Meta $meta,
        int $status,
        int $sortOrder = 0,
    ): void {
        $this->group_id = $groupId;
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->content = $content;
        $this->meta = $meta;
        $this->status = $status;
        $this->sort_order = $sortOrder;
    }

    // <editor-fold desc="Статусы">

    /**
     * Опубликовать страницу
     *
     * @throws DomainException
     */
    public function publish(): void
    {
        if ($this->isPublished()) {
            throw new DomainException('Страница уже опубликована.');
        }
        $this->status = self::STATUS_PUBLISHED;
    }

    /**
     * Перевести в черновик
     *
     * @throws DomainException
     */
    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Страница уже является черновиком.');
        }
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * Архивировать страницу
     *
     * @throws DomainException
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new DomainException('Страница уже архивирована.');
        }
        $this->status = self::STATUS_ARCHIVED;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    // </editor-fold>

    /**
     * Получить SEO-заголовок страницы
     */
    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->title;
    }

    /**
     * Получить описание для превью (excerpt или начало content)
     */
    public function getPreviewText(int $length = 200): string
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }
        if (!empty($this->content)) {
            return mb_substr(strip_tags($this->content), 0, $length);
        }
        return '';
    }

    // <editor-fold desc="Relations">

    /**
     * Получить группу страницы
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    // </editor-fold>

    public static function tableName(): string
    {
        return '{{%page_pages}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
            ...parent::behaviors(),
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): PageQuery
    {
        return new PageQuery(static::class);
    }
}
