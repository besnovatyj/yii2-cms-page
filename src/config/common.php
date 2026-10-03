<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\Module;
use Besnovatyj\Validators\SlugValidator;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Объявляется через `extra.config-plugin`, собирается modman в merge-plan и мёржится в рантайме.
 * Содержит регистрацию модуля. Меню админки — `adminMenu.php` (группа `admin-menu`), миграции — вклад modman.
 * Значения берутся из статических методов {@see Module} — единый источник, без дублирования.
 *
 * URL-правила фронтенда — вклад в `frontendUrlManager` группы `common`. Страница и группа адресуются
 * слагом в собственных префиксах (`page/…`, `pages/…`), с числовым `<id>` не конкурируют — у страниц
 * поэтому {@see SlugValidator::SLUG_ANY}; группа — узел дерева, STRICT. Короткие адреса без префикса
 * (`/price`) — алиасы модуля route-alias (см. Module::aliasTargets()). Гейтятся modman.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    'components' => [
        'frontendUrlManager' => [
            'rules' => [
                'page/<slug:' . SlugValidator::SLUG_ANY . '>'     => 'Page/page/view',
                'pages/<slug:' . SlugValidator::SLUG_STRICT . '>' => 'Page/page/group',
            ],
        ],
    ],
];
