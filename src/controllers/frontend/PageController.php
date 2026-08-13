<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\controllers\frontend;

use Besnovatyj\Contracts\theme\ViewVariantCatalog;
use Besnovatyj\Contracts\theme\ViewVariantsManifest;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\readModels\GroupReadRepository;
use Besnovatyj\Page\readModels\PageReadRepository;
use Yii;
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

        return $this->render($this->resolveView($page), [
            'page'        => $page,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Имя представления с учётом выбранного в админке варианта темы.
     *
     * Базовое `view` либо `view.variants/{ключ}` — но только если активная тема реально предлагает
     * этот вариант для слота {@see Page::VIEW_SLOT} (сохранённый ключ мог протухнуть после смены темы).
     * Каталог резолвится защитно: без пакета тем биндинг отсутствует — рендерим базовое представление.
     * Оверлей темы подхватит `view.variants/{ключ}.php` по тому же pathMap, что и обычные вьюхи.
     */
    private function resolveView(Page $page): string
    {
        $variant = (string)$page->template;
        if ($variant === '' || !Yii::$container->has(ViewVariantCatalog::class)) {
            return 'view';
        }

        $catalog = Yii::$container->get(ViewVariantCatalog::class);

        return $catalog->hasVariant(Page::VIEW_SLOT, $variant)
            ? 'view' . ViewVariantsManifest::VARIANTS_DIR_SUFFIX . '/' . $variant
            : 'view';
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
