<?php

namespace app\models;

use Yii;

/**
 * @OA\Schema(
 *     schema="model",
 *     @OA\Property(
 *                     property="id",
 *                     type="integer"
 *     ),
 *     @OA\Property(
 *                     property="name",
 *                     type="string"
 *     ),
 *     @OA\Property(
 *                     property="speed",
 *                     type="integer"
 *     ),
 *     example={"id": 1, "name": "Modelname", "speed": 60}
 * )
 */
/**
 * @OA\Schema(
 *     schema="modelCreate",
 *     @OA\Property(
 *                     property="name",
 *                     type="string"
 *     ),
 *     @OA\Property(
 *                     property="speed",
 *                     type="integer"
 *     ),
 *     example={"name": "Modelname", "speed": 60}
 * )
 */
/**
 * Список моделей автобусов
 *
 * @property int $id
 * @property string $name
 * @property int $speed
 *
 * @property DriverModel[] $driverModels
 * @property Driver[] $drivers
 */
class Model extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'speed'], 'required'],
            [['speed'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     * Набор полей для отображения в API
     */
    public function fields()
    {
        return ['id', 'name', 'speed'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'speed' => 'Speed',
        ];
    }

    /**
     * Gets query for [[DriverModels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriverModels()
    {
        return $this->hasMany(DriverModel::class, ['model_id' => 'id']);
    }

    /**
     * Gets query for [[Drivers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDrivers()
    {
        return $this->hasMany(Driver::class, ['id' => 'driver_id'])->viaTable('driver_model', ['model_id' => 'id']);
    }

    public function getDistance()
    {

    }
}