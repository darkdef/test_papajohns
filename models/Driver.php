<?php

namespace app\models;

use Yii;

/**
 * @OA\Schema(
 *     schema="driver",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         description="Date in format YYYY-MM-DD"
 *     ),
 *     @OA\Property(
 *         property="age",
 *         type="integer"
 *     ),
 *     @OA\Property  (
 *         type="array",
 *         property="models",
 *         @OA\Items(
 *             ref="#/components/schemas/model"
 *         )
 *     ),
 *     example={"id": 1, "name": "John Ivanov", "birth_date": "1980-09-02", "age": 40, "models": {{"id": 1, "name": "Modelname1", "speed": 60},{"id": 2, "name": "Modelname2", "speed": 48}}}
 * )
 */

/**
 * @OA\Schema(
 *     schema="driverUpdate",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         description="Date in format YYYY-MM-DD"
 *     ),
 *     @OA\Property  (
 *         type="array",
 *         property="models",
 *         @OA\Items(
 *            type="integer"
 *         )
 *     ),
 *     example={"id": 1, "name": "John Ivanov", "birth_date": "1980-09-02", "models": {1,2,3}}
 * )
 */

/**
 * @OA\Schema(
 *     schema="driverCreate",
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         description="Date in format YYYY-MM-DD"
 *     ),
 *     @OA\Property  (
 *         type="array",
 *         property="models",
 *         @OA\Items(
 *             type="integer"
 *         )
 *     ),
 *     example={"name": "John Ivanov", "birth_date": "1980-09-02", "models": {1,2,3}}
 * )
 */

/**
 * @OA\Schema(
 *     schema="driverTransit",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         description="Date in format YYYY-MM-DD"
 *     ),
 *     @OA\Property(
 *         property="age",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="travel_time",
 *         type="integer",
 *         description="travel time in days"
 *     ),
 *     example={"id": 1, "name": "John Ivanov", "birth_date": "1980-09-02", "age": 40, "travel_time": 2}
 * )
 */
/**
 * Список водителей
 *
 * @property int $id
 * @property string $name
 * @property string $birth_date
 *
 * @property DriverModel[] $driverModels
 * @property Model[] $models
 */
class Driver extends \yii\db\ActiveRecord
{
    /**
     * @var integer[] виртуальное свойство для получения связей в запросах insert/update
     */
    public $models = [];

    /**
     * @var integer расстояние между городами, для расчёта времени прохождения
     */
    public $distance;

    /**
     * @var integer виртуальное свойство для получения данных из запроса
     */
    public $speed;

    /**
     * @var integer количество рабочих часов в сутках
     */
    private $workHoursPerDay = 8;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->workHoursPerDay = Yii::$app->params['workHoursPerDay']??$this->workHoursPerDay;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'birth_date'], 'required'],
            [['birth_date', 'models'], 'safe'],
            [['models'], 'each', 'rule' => ['integer']],
            [
                ['models'],
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Model::class,
                    'targetAttribute' => 'id',
                    'message' => 'Bus model not found {id}',
                ],
            ],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     * Набор полей для отображения в API
     */
    public function fields()
    {
        $fields = [
            'id',
            'name',
            'birth_date',
            'age' => function ($data) {
                return (new \DateTime($this->birth_date))->diff(new \DateTime())->y;
            },
        ];

        // Отдельный сценарий, для отображения моделей со временем требующимся на преодоление расстояния между городами
        if ($this->scenario == 'transit') {
            $fields['travel_time'] = function($data) {
                return ceil($data->distance / $data->speed / $this->workHoursPerDay);
            };
        } else {
            $fields['models'] = 'listModels';
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Fio',
            'birth_date' => 'Birthday',
        ];
    }

    /**
     * Gets query for [[DriverModels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriverModels()
    {
        return $this->hasMany(DriverModel::class, ['driver_id' => 'id']);
    }

    /**
     * Gets query for [[Models]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getListModels()
    {
        return $this->hasMany(Model::class, ['id' => 'model_id'])->viaTable('driver_model', ['driver_id' => 'id']);
    }

    /**
     * Gets max speed for [[Models]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaxSpeed()
    {
        return $this->hasOne(Model::class, ['id' => 'model_id'])->viaTable('driver_model', ['driver_id' => 'id'])->orderBy('speed');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            DriverModel::deleteAll(['driver_id' => $this->id]);
        }
        if (!empty($this->models)) {
            foreach ($this->models as $model) {
                $this->link('driverModels', new DriverModel(['model_id' => $model]));
            }
        }
    }
}