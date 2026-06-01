<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\controllers\frontend;

use Besnovatyj\Page\readModels\GroupReadRepository;
use Besnovatyj\Page\readModels\PageReadRepository;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Фронтэнд-контроллер статических страниц
 */
class PageController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly PageReadRepository $pages,
        private readonly GroupReadRepository $groups,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Показать опубликованную страницу по slug
     *
     * @throws NotFoundHttpException
     */
    public function actionView(string $slug): string
    {
        $page = $this->pages->findBySlug($slug);

        if ($page === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        // Хлебные крошки: группа (если есть) → страница
        $breadcrumbs = [];
        if ($page->group !== null) {
            $breadcrumbs = $this->groups->getBreadcrumbs(
                $page->group,
                static fn ($group) => Url::to(['/Page/page/group', 'slug' => $group->slug]),
            );
        }
        $breadcrumbs[] = ['label' => $page->title];

        return $this->render('view', [
            'page'        => $page,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Список страниц группы по slug группы
     *
     * @throws NotFoundHttpException
     */
    public function actionGroup(string $slug): string
    {
        $group = $this->groups->findBySlug($slug);

        if ($group === null) {
            throw new NotFoundHttpException('Раздел не найден.');
        }

        $dataProvider = $this->pages->getAllByGroup($group);

        $breadcrumbs = $this->groups->getBreadcrumbs(
            $group,
            static fn ($g) => Url::to(['/Page/page/group', 'slug' => $g->slug]),
        );

        return $this->render('group', [
            'group'        => $group,
            'dataProvider' => $dataProvider,
            'breadcrumbs'  => $breadcrumbs,
        ]);
    }
}
