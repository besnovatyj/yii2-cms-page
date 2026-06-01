<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\repositories;

use Besnovatyj\Page\entities\Group;

/**
 * Репозиторий для управления группами страниц
 */
class GroupRepository
{
    /**
     * Получить группу по ID
     *
     * @throws NotFoundException
     */
    public function get(int $id): Group
    {
        if (!$group = Group::findOne($id)) {
            throw new NotFoundException('Группа страниц не найдена.');
        }
        return $group;
    }

    /**
     * Проверить, есть ли страницы в группе или её потомках
     */
    public function hasPages(int $groupId): bool
    {
        return \Besnovatyj\Page\entities\Page::find()
            ->andWhere(['group_id' => $groupId])
            ->exists();
    }
}
