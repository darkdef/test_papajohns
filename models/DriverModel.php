<?php

namespace app\models;

use Yii;

/**
 * Связь между водителями и моделями
 *
 * @property int $driver_id
 * @property int $model_id
 *
 * @property Driver $driver
 * @property Model $model
 */
class DriverModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['driver_id', 'model_id'], 'required'],
            [['driver_id', 'model_id'], 'integer'],
            [['driver_id', 'model_id'], 'unique', 'targetAttribute' => ['driver_id', 'model_id']],
            [['driver_id'], 'exist', 'skipOnError' => true, 'targetClass' => Driver::class, 'targetAttribute' => ['driver_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::class, 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'driver_id' => 'Driver ID',
            'model_id' => 'Model ID',
        ];
    }

    /**
     * Gets query for [[Driver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriver()
    {
        return $this->hasOne(Driver::class, ['id' => 'driver_id']);
    }

    /**
     * Gets query for [[Model]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(Model::class, ['id' => 'model_id']);
    }
}
