<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\services\manage;

use Besnovatyj\Helpers\FilesystemHelper;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\forms\backend\PageForm;
use Besnovatyj\Page\repositories\PageRepository;
use Throwable;
use Yii;
use yii\base\Exception;

/**
 * Сервис управления статическими страницами
 */
readonly class PageManageService
{
    public function __construct(
        private PageRepository $pages,
    )
    {
    }

    /**
     * Создать новую страницу
     *
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function create(PageForm $form): Page
    {
        $page = Page::create(
            groupId: $form->group_id,
            title: $form->title,
            slug: $form->slug,
            excerpt: $form->excerpt ?: null,
            content: $form->content ?: null,
            meta: new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords,
            ),
            sortOrder: $form->sort_order,
        );

        $this->pages->save($page);

        $this->createFolder($page->id);

        return $page;
    }

    /**
     * Обновить существующую страницу
     * @throws \yii\db\Exception
     */
    public function edit(int $id, PageForm $form): void
    {
        $page = $this->pages->get($id);
        $page->edit(
            $form->group_id,
            $form->title,
            $form->slug,
            $form->excerpt ?: null,
            $form->content ?: null,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords,
            ),
            $form->sort_order,
        );
        $this->pages->save($page);
    }

    /**
     * Опубликовать страницу
     * @throws \yii\db\Exception
     */
    public function publish(int $id): void
    {
        $page = $this->pages->get($id);
        $page->publish();
        $this->pages->save($page);
    }

    /**
     * Перевести страницу в черновик
     * @throws \yii\db\Exception
     */
    public function draft(int $id): void
    {
        $page = $this->pages->get($id);
        $page->draft();
        $this->pages->save($page);
    }

    /**
     * Архивировать страницу
     *
     * @throws \yii\db\Exception
     */
    public function archive(int $id): void
    {
        $page = $this->pages->get($id);
        $page->archive();
        $this->pages->save($page);
    }

    /**
     * Удалить страницу
     *
     * @throws Throwable
     */
    public function remove(int $id): void
    {
        $page = $this->pages->get($id);
        $this->pages->remove($page);
        $this->removeFolder($id);
    }

    /**
     * @throws Exception
     */
    private function createFolder(int $id): void
    {
        $path = $this->getFolderPath($id);
        FilesystemHelper::createDirectoryRecursively($path);
    }

    /**
     * @throws \Exception
     */
    private function removeFolder(int $id): void
    {
        $path = $this->getFolderPath($id);
        FilesystemHelper::deleteDirContents($path, true);
    }

    protected function getFolderPath(int $id): string
    {
        return Yii::getAlias('@static') . '/origin/Page/' . $id;
    }

}
