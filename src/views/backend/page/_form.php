<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\forms\backend\PageForm;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/* @var $this View */
/* @var $model PageForm */
/* @var $page Page|null */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">

    <!-- Левая колонка: основные поля + контент -->
    <div class="col-12 col-xl-8">

        <!-- Основные данные -->
        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-card-text me-1"></i>Основное</span>
                <a class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="collapse"
                   href="#card-main" role="button" aria-expanded="true">
                    <i class="bi bi-plus-lg"></i>
                    <i class="bi bi-dash-lg"></i>
                </a>
            </div>
            <div class="collapse show" id="card-main">
                <div class="card-body">
                    <?= $form->field($model, 'title')->textInput([
                        'maxlength'   => true,
                        'class'       => 'form-control form-control-lg',
                        'placeholder' => 'Заголовок страницы',
                    ]) ?>

                    <?= $form->field($model, 'slug')->textInput([
                        'maxlength'   => true,
                        'class'       => 'form-control',
                        'placeholder' => 'slug-zapolnitsya-avtomaticheski',
                    ])->hint('Оставьте пустым — заполнится из заголовка') ?>

                    <?= $form->field($model, 'excerpt')->textarea([
                        'rows'        => 3,
                        'class'       => 'form-control',
                        'placeholder' => 'Краткое описание для анонсов и превью (не обязательно)',
                    ]) ?>
                </div>
                <div class="card-footer">
                    <div class="d-grid">
                        <?= Html::submitButton('<i class="bi bi-save me-1"></i>Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контент (CKEditor) -->
        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-body-text me-1"></i>Контент</span>
                <a class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="collapse"
                   href="#card-content" role="button" aria-expanded="true">
                    <i class="bi bi-plus-lg"></i>
                    <i class="bi bi-dash-lg"></i>
                </a>
            </div>
            <div class="collapse show" id="card-content">
                <div class="card-body">
                    <?php if (!isset($page)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Сохраните страницу, чтобы получить доступ к редактору контента.
                        </div>
                    <?php else: ?>
                        <?php
                        $editorConfig = [
                            'language'      => 'ru',
                            'fmDefaultPath' => '/static/origin/Page/' . $page->id,
                        ];
                        echo $form->field($model, 'content')->widget(
                            \Besnovatyj\Editor\EditorWidget::class,
                            $editorConfig,
                        )->label(false);
                        ?>
                    <?php endif; ?>
                </div>
                <?php if (isset($page)): ?>
                <div class="card-footer">
                    <div class="d-grid">
                        <?= Html::submitButton('<i class="bi bi-save me-1"></i>Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Правая колонка: настройки -->
    <div class="col-12 col-xl-4">

        <!-- Публикация / статус -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <i class="bi bi-toggles me-1"></i>Публикация
            </div>
            <div class="card-body">
                <?= $form->field($model, 'status')->dropDownList([
                    Page::STATUS_DRAFT     => 'Черновик',
                    Page::STATUS_PUBLISHED => 'Опубликована',
                    Page::STATUS_ARCHIVED  => 'Архив',
                ], ['class' => 'form-select']) ?>

                <?= $form->field($model, 'sort_order')->textInput([
                    'type'  => 'number',
                    'class' => 'form-control',
                    'min'   => 0,
                ])->hint('Меньшее число = выше в списке') ?>
            </div>
            <div class="card-footer">
                <div class="d-grid">
                    <?= Html::submitButton('<i class="bi bi-save me-1"></i>Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>

        <!-- Раздел -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <i class="bi bi-diagram-3 me-1"></i>Раздел
            </div>
            <div class="card-body">
                <?= $form->field($model, 'group_id')->dropDownList(
                    ['' => '— Без раздела —'] + (new TreeQueryScope(Group::class))->dropdownTree(),
                    ['class' => 'form-select'],
                )->label('Группа страниц') ?>
            </div>
        </div>

        <!-- SEO -->
        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-search me-1"></i>SEO</span>
                <a class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="collapse"
                   href="#card-seo" role="button" aria-expanded="false">
                    <i class="bi bi-plus-lg"></i>
                    <i class="bi bi-dash-lg"></i>
                </a>
            </div>
            <div class="collapse" id="card-seo">
                <div class="card-body">
                    <?= $form->field($model->meta, 'title')->textInput([
                        'class'       => 'form-control',
                        'placeholder' => 'SEO-заголовок',
                    ]) ?>
                    <?= $form->field($model->meta, 'description')->textarea([
                        'rows'        => 3,
                        'class'       => 'form-control',
                        'placeholder' => 'SEO-описание',
                    ]) ?>
                    <?= $form->field($model->meta, 'keywords')->textInput([
                        'class'       => 'form-control',
                        'placeholder' => 'Ключевые слова',
                    ]) ?>
                </div>
                <div class="card-footer">
                    <div class="d-grid">
                        <?= Html::submitButton('<i class="bi bi-save me-1"></i>Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>
