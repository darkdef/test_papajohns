<?php

namespace app\controllers;

use app\models\Driver;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

/**
 * @OA\Info(
 *     title="Driver API",
 *     version="0.1",
 *     description="For auth you may use token=`100-token` (without quotes)"
 * );
 * @OA\SecuritySchemes(
 *     "oneOf",
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         in="header",
 *         name="bearerAuth",
 *         type="http",
 *         scheme="bearer"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="tokenAuth",
 *         in="query",
 *         name="token",
 *         type="apiKey",
 *         scheme="token"
 *     )
 * );
 */


/**
 * @OA\Get (
 *     tags={"Drivers"},
 *     summary="List all drivers",
 *     path="/driver",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\Parameter(
 *         description="Page number",
 *         in="query",
 *         name="page",
 *         required=false,
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="List of drivers",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/driver")
 *             )
 *         ),
 *         @OA\Header(header="X-Pagination-Current-Page", @OA\Schema(type="int"), description="Current page"),
 *         @OA\Header(header="X-Pagination-Page-Count", @OA\Schema(type="int"), description="Count of pages"),
 *         @OA\Header(header="X-Pagination-Per-Page", @OA\Schema(type="int"), description="Count elements of page"),
 *         @OA\Header(header="X-Pagination-Total-Count", @OA\Schema(type="int"), description="Total count of elements"),
 *     )
 * );
 */

/**
 * @OA\Get (
 *     tags={"Drivers"},
 *     summary="Get one driver info",
 *     path="/driver/{id}",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\Parameter(
 *         description="ID of driver to return",
 *         in="path",
 *         name="id",
 *         required=true,
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="One driver info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/driver")
 *         )
 *     )
 * );
 */

/**
 * @OA\Post (
 *     tags={"Drivers"},
 *     summary="Create driver",
 *     path="/driver",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/driverCreate")
 *         )
 *     ),
 *     @OA\Response(
 *          response="200",
 *          description="Created driver info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/driver")
 *         )
 *     )
 * );
 */

/**
 * @OA\Put (
 *     tags={"Drivers"},
 *     summary="Update one driver",
 *     path="/driver",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/driverUpdate")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated driver info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/driver")
 *         )
 *     )
 * );
 */
class DriverController extends ActiveController
{
    public $modelClass = 'app\models\Driver';

    /**
     * @var int Задаём размер страницы
     */
    private $pageSize = 10;

    public function init()
    {
        $this->pageSize = \Yii::$app->params['pageSize']??$this->pageSize;
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                [
                    'class' => QueryParamAuth::class,
                    'tokenParam' => 'token',
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @OA\Get (
     *     tags={"Transit"},
     *     summary="Transit time for all drivers",
     *     description="In external API enabled cities: `Краснодар`, `Казань`, `Москва`",
     *     path="/driver/transit",
     *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
     *     @OA\Parameter(
     *         description="Page number",
     *         in="query",
     *         name="page",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         description="City from",
     *         in="query",
     *         name="from",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="City to",
     *         in="query",
     *         name="to",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get transit time for all drivers",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/driverTransit")
     *             )
     *         ),
     *         @OA\Header(header="X-Pagination-Current-Page", @OA\Schema(type="int"), description="Current page"),
     *         @OA\Header(header="X-Pagination-Page-Count", @OA\Schema(type="int"), description="Count of pages"),
     *         @OA\Header(header="X-Pagination-Per-Page", @OA\Schema(type="int"), description="Count elements of page"),
     *         @OA\Header(header="X-Pagination-Total-Count", @OA\Schema(type="int"), description="Total count of elements"),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="City/driver not found",
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="City API not works",
     *     ),
     * );
     */
    /**
     * Используем вариант с группирокой для определения максимальной скорости модели автобуса для водителя
     * Вариант с подзапросами возможен, но мне не нравится. Для небольших наборов данных разницы не будет.
     * А если данных будет много, то нужно другое решение, как пример:
     * Навесить триггер на update/insert/delete на таблицу DriverModel для однозначной идентификации
     * максимального значения, чтобы использовать left join
     *
     * @return ActiveDataProvider
     */
    public function actionTransits()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Driver::find()->joinWith(['listModels'])
                ->select(['driver.id', 'driver.name', 'driver.birth_date', 'speed' => new Expression('max(model.speed)')])
                ->groupBy(['driver.id', 'driver.name', 'driver.birth_date'])
                ->orderBy(['speed' => SORT_DESC]),
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
            'sort' => [
                'sortParam' => false,
            ],
        ]);

        /**
         * Вместо всего этого возможно было бы правильнее сделать свой DataProvider, но учитывая затраты на обращение к
         * стороннему API для получения расстояний, задержками на перебор моделей можно пренебречь
         */
        $models = $dataProvider->getModels();
        foreach ($models as &$model) {
            $model->distance = $this->getDistance($this->request);
            $model->setScenario('transit');
        }
        $dataProvider->setModels($models);

        return $dataProvider;
    }

    /**
     * @OA\Get (
     *     tags={"Transit"},
     *     summary="Transit time for one driver",
     *     description="In external API enabled cities: `Краснодар`, `Казань`, `Москва`",
     *     path="/driver/transit/{id}",
     *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
     *     @OA\Parameter(
     *         description="ID of driver",
     *         in="path",
     *         name="id",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="City from",
     *         in="query",
     *         name="from",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="City to",
     *         in="query",
     *         name="to",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Transit time for one driver",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/driverTransit")
     *             )
     *         ),
     *         @OA\Header(header="X-Pagination-Current-Page", @OA\Schema(type="int"), description="Current page"),
     *         @OA\Header(header="X-Pagination-Page-Count", @OA\Schema(type="int"), description="Count of pages"),
     *         @OA\Header(header="X-Pagination-Per-Page", @OA\Schema(type="int"), description="Count elements of page"),
     *         @OA\Header(header="X-Pagination-Total-Count", @OA\Schema(type="int"), description="Total count of elements"),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Missing required parameters",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="City/driver not found",
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="City API not works",
     *     ),
     * );
     */
    public function actionTransit($id)
    {
        $model = Driver::find()
            ->andWhere(['driver.id' => $id])
            ->addSelect(['driver.*', 'model.speed'])
            ->joinWith(['listModels'])
            ->orderBy(['model.speed' => SORT_DESC])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('Driver not found');
        }

        $model->distance = $this->getDistance($this->request);
        $model->setScenario('transit');

        return $model;
    }

    /**
     * Дата-провайдер для запроса общего списка
     *
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Driver::find()->joinWith(['listModels']),
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'sortParam' => false,
            ],
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function getDistance($request)
    {
        $from = $request->get('from', '');
        $to = $request->get('to', '');

        if (empty($from) || empty($to)) {
            throw new \yii\web\BadRequestHttpException('Params `from` and `to` is required!');
        }

        return \Yii::$app->distance->getDistance($from, $to);
    }
}