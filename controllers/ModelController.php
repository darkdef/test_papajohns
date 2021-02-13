<?php

namespace app\controllers;

use app\models\Model;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Response;


/**
 * @OA\Get (
 *     tags={"Models of bus"},
 *     summary="List of bus models",
 *     path="/model",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\Parameter(
 *         description="Page number",
 *         in="query",
 *         name="page",
 *         required=false,
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="List of bus models",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/model")
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
 *     tags={"Models of bus"},
 *     summary="One bus model info",
 *     path="/model/{id}",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\Parameter(
 *         description="ID of bus model to return",
 *         in="path",
 *         name="id",
 *         required=true,
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="One bus model info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/model")
 *         )
 *     )
 * );
 */

/**
 * @OA\Post (
 *     tags={"Models of bus"},
 *     summary="Create one bus model",
 *     path="/model",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/modelCreate")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Created bus model info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/model")
 *         )
 *     )
 * );
 */

/**
 * @OA\Put (
 *     tags={"Models of bus"},
 *     summary="Update one bus model",
 *     path="/model",
 *     security={{"bearerAuth":{}},{"tokenAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/model")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated bus model info",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/model")
 *         )
 *     )
 * );
 */
class ModelController extends ActiveController
{
    public $modelClass = 'app\models\Model';

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
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * Дата-провайдер для запроса общего списка
     *
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Model::find(),
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }
}