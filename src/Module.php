<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page;

use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesAdminMenu;
use Besnovatyj\Contracts\module\ProvidesDependencies;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\routing\AliasTarget;
use Besnovatyj\Contracts\routing\AliasTargetProvider;
use Besnovatyj\Contracts\menu\MenuTarget;
use Besnovatyj\Contracts\menu\MenuTargetProvider;
use Besnovatyj\Page\readModels\GroupReadRepository;
use Besnovatyj\Page\readModels\PageReadRepository;

/**
 * Модуль управления статическими страницами
 */
class Module extends CmsModule implements
    DeclaresModule, ProvidesAdminMenu,
    ProvidesDependencies, ProvidesDirectories,
    ProvidesMigrations, AliasTargetProvider, MenuTargetProvider
{
    public const bool EDITABLE = true;
    public const string VERSION = '2.0.0';
    public const string MODULE_ID = 'Page';

    public static function moduleId(): string { return self::MODULE_ID; }
    public static function moduleVersion(): string { return self::VERSION; }
    public static function isEditable(): bool { return self::EDITABLE; }
    public static function adminMenu(): array { return require __DIR__.'/config/adminMenu.php'; }
    public static function moduleConfig(): array { return require __DIR__.'/config/config.php'; }
    public static function dependencies(): array { return require __DIR__.'/config/dependencies.php'; }
    public static function migrationPath(): string { return __DIR__.'/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__.'\\migrations'; }
    public static function directories(): array { return ['@static/origin/Page','@static/cache/Page'];}

    /**
     * Цели, которым можно назначить короткий URL (пилот канала алиасов, ср. пилот Blog для URL-правил).
     * Реализация {@see AliasTargetProvider}; вызывается только модулем алиасов, если он установлен.
     *
     * @return AliasTarget[]
     */
    public function aliasTargets(): array
    {
        return [
            new AliasTarget('/Page/page/view', 'Страница', 'slug'),
            new AliasTarget('/Page/page/group', 'Раздел страниц', 'slug'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,string>
     */
    public function aliasSlugs(string $route): array
    {
        return match (ltrim($route, '/')) {
            'Page/page/view' => (new PageReadRepository())->slugTitleMap(),
            'Page/page/group' => (new GroupReadRepository())->slugNameMap(),
            default => [],
        };
    }

    /**
     * Цели для построения пунктов меню. Те же роуты, что и у канала алиасов, но отдельный контракт
     * {@see MenuTargetProvider} — меню строит навигационный узел с именем, а не rewrite URL.
     *
     * @return MenuTarget[]
     */
    public function menuTargets(): array
    {
        return [
            new MenuTarget('/Page/page/group', 'Раздел страниц', 'slug'),
            new MenuTarget('/Page/page/view', 'Страница', 'slug'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,string>
     */
    public function menuCandidates(string $route): array
    {
        return match (ltrim($route, '/')) {
            'Page/page/group' => (new GroupReadRepository())->slugNameMap(),
            'Page/page/view' => (new PageReadRepository())->slugTitleMap(),
            default => [],
        };
    }
}
