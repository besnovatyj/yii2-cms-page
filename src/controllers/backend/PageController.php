<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\controllers\backend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use Besnovatyj\Kernel\urlmanager\UrlManagerHelperTrait;
use DomainException;
use Exception;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\forms\backend\PageForm;
use Besnovatyj\Page\forms\backend\PageSearch;
use Besnovatyj\Page\services\manage\PageManageService;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Бэкэнд-контроллер управления статическими страницами
 */
class PageController extends Controller
{
    use ControllerTrait;
    use UrlManagerHelperTrait;

    public function __construct(
        $id,
        $module,
        private readonly PageManageService $service,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete'   => ['POST'],
                    'publish'  => ['POST'],
                    'draft'    => ['POST'],
                    'archive'  => ['POST'],
                    'ajax-save' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Список страниц с поиском и фильтрацией
     */
    public function actionIndex(): string
    {
        $searchModel  = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр страницы в бэкэнде
     *
     * @throws NotFoundHttpException|InvalidConfigException
     */
    public function actionView(int $id): string
    {
        $page                = $this->findModel($id);
        $absoluteFrontendUrl = $this->getAbsoluteFrontendRoute('/Page/page/view/', ['slug' => $page->slug]);
        $frontendUrl         = $this->getFrontendRoute('/Page/page/view/', ['slug' => $page->slug]);

        return $this->render('view', [
            'page'               => $page,
            'absoluteFrontendUrl' => $absoluteFrontendUrl,
            'frontendUrl'        => $frontendUrl,
        ]);
    }

    /**
     * Создать новую страницу
     */
    public function actionCreate(): Response|string
    {
        $form = new PageForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $page = $this->service->create($form);
                Yii::$app->session->setFlash('success', 'Страница создана.');
                return $this->redirect(['update', 'id' => $page->id]);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', YII_DEBUG ? VarDumper::dumpAsString($e->getMessage()) : 'Ошибка при создании страницы.');
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Редактировать страницу
     *
     * @throws NotFoundHttpException|InvalidConfigException
     */
    public function actionUpdate(int $id): Response|string
    {
        $page                = $this->findModel($id);
        $absoluteFrontendUrl = $this->getAbsoluteFrontendRoute('/Page/page/view/', ['slug' => $page->slug]);
        $frontendUrl         = $this->getFrontendRoute('/Page/page/view/', ['slug' => $page->slug]);

        $form = new PageForm($page);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($page->id, $form);
                Yii::$app->session->setFlash('success', 'Страница обновлена.');
                return $this->redirect(['update', 'id' => $page->id]);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', YII_DEBUG ? VarDumper::dumpAsString($e->getMessage()) : 'Ошибка при сохранении страницы.');
            }
        }

        return $this->render('update', [
            'model'               => $form,
            'page'                => $page,
            'absoluteFrontendUrl' => $absoluteFrontendUrl,
            'frontendUrl'         => $frontendUrl,
        ]);
    }

    /**
     * Опубликовать страницу
     */
    public function actionPublish(int $id): Response
    {
        try {
            $this->service->publish($id);
            Yii::$app->session->setFlash('success', 'Страница опубликована.');
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', 'Ошибка при публикации.');
        }
        return $this->goReferer();
    }

    /**
     * Перевести страницу в черновик
     */
    public function actionDraft(int $id): Response
    {
        try {
            $this->service->draft($id);
            Yii::$app->session->setFlash('success', 'Страница переведена в черновик.');
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', 'Ошибка.');
        }
        return $this->goReferer();
    }

    /**
     * Архивировать страницу
     */
    public function actionArchive(int $id): Response
    {
        try {
            $this->service->archive($id);
            Yii::$app->session->setFlash('success', 'Страница архивирована.');
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', 'Ошибка.');
        }
        return $this->goReferer();
    }

    /**
     * Удалить страницу
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
            Yii::$app->session->setFlash('success', 'Страница удалена.');
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', YII_DEBUG ? VarDumper::dumpAsString($e->getMessage()) : 'Ошибка при удалении.');
        }
        return $this->redirect(['index']);
    }

    /**
     * AJAX-сохранение контента из CKEditor (внешний виджет).
     *
     * Обработка ошибок делегирована {@see \yii\web\ErrorHandler}: клиентские ошибки — типизированный
     * {@see BadRequestHttpException} (реальный HTTP 400 + сообщение); инфраструктурные исключения
     * сервиса всплывают к ErrorHandler (в проде скрыты, в debug видны). Успех — конверт `{status:'success', ...}`.
     *
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    public function actionAjaxSave(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        $content   = Yii::$app->request->post('editor_content') ?: '';
        $id        = Yii::$app->request->post('model_id') ?: null;
        $fieldName = Yii::$app->request->post('field_name') ?: null;

        if (empty($id) || empty($fieldName)) {
            throw new BadRequestHttpException('Сначала сохраните страницу обычным способом.');
        }

        $page = $this->findModel((int)$id);

        if (!isset($page->$fieldName)) {
            throw new BadRequestHttpException('Несуществующее поле: ' . $fieldName);
        }

        $form = new PageForm($page);
        $form->$fieldName = urldecode($content);
        if (!$form->validate()) {
            throw new BadRequestHttpException('Ошибка валидации: ' . implode('; ', $form->getFirstErrors()));
        }
        $this->service->edit($page->id, $form);

        return ['status' => 'success', 'message' => 'Сохранено!'];
    }

    /**
     * Найти страницу по ID или выбросить 404
     *
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Page
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }
}
