<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\repositories;

use Besnovatyj\Page\entities\Page;
use RuntimeException;
use Throwable;
use yii\db\Exception;

/**
 * Репозиторий для CRUD операций со страницами
 */
class PageRepository
{
    /**
     * Получить страницу по ID
     * @throws NotFoundException
     */
    public function get(int $id): Page
    {
        if (!$page = Page::findOne($id)) {
            throw new NotFoundException('Страница не найдена.');
        }
        return $page;
    }

    /**
     * Получить страницу по slug
     * @throws NotFoundException
     */
    public function getBySlug(string $slug): Page
    {
        if (!$page = Page::find()->andWhere(['slug' => $slug])->one()) {
            throw new NotFoundException('Страница не найдена.');
        }
        return $page;
    }

    /**
     * Сохранить страницу
     * @throws RuntimeException|Exception
     */
    public function save(Page $page): void
    {
        if (!$page->save()) {
            throw new RuntimeException('Ошибка сохранения страницы: ' . json_encode($page->errors));
        }
    }

    /**
     * Удалить страницу
     * @throws Throwable
     */
    public function remove(Page $page): void
    {
        if (!$page->delete()) {
            throw new RuntimeException('Ошибка удаления страницы.');
        }
    }

    /**
     * Проверить существование страниц в заданной группе
     */
    /**
     * Карта «slug => заголовок» опубликованных страниц — для выбора цели URL-алиаса и пункта меню
     * в админке.
     *
     * Фильтр здесь по публикации самой страницы, без учёта видимости раздела: администратор вправе
     * назначить адрес странице скрытого раздела заранее — раздел откроют позже. Фронтовый аналог
     * {@see \Besnovatyj\Page\readModels\PageReadRepository::slugTitleMap()} строже: он показывает
     * только реально доступное посетителю.
     *
     * @return array<string,string>
     */
    public function slugTitleMap(): array
    {
        return Page::find()->published()
            ->select(['title', 'slug'])
            ->orderBy(['title' => SORT_ASC])
            ->indexBy('slug')
            ->column();
    }

    public function existsByGroup(int $groupId): bool
    {
        return Page::find()->andWhere(['group_id' => $groupId])->exists();
    }
}
