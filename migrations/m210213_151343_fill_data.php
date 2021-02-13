<?php

use yii\db\Migration;

/**
 * Class m210213_151343_fill_data
 */
class m210213_151343_fill_data extends Migration
{
    private $allDriverCount = 29;
    private $allModelCount = 29;
    private $maxModelToDriver = 5;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Создадим $allDriverCount водителей
        for ($driverIndex=1; $driverIndex<=$this->allDriverCount; $driverIndex++) {
            $this->createDriver($driverIndex);
        }

        // Создадим $allModelCount моделей автобусов
        for ($modelIndex=1; $modelIndex<=$this->allModelCount; $modelIndex++) {
            $this->createModel($modelIndex);
        }

        // Зададим связи водителей с моделями
        for ($driverIndex=1; $driverIndex<=$this->allDriverCount;$driverIndex++) {
            $generatedModels = $this->generateModels(rand(1, $this->maxModelToDriver), $this->allModelCount);
            foreach ($generatedModels as $generatedModel) {
                $this->insert('{{%driver_model}}', ['driver_id' => $driverIndex, 'model_id' => $generatedModel]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%driver_model}}');
        $this->delete('{{%driver}}');
        $this->delete('{{%model}}');
    }

    /**
     * @param int $id
     */
    private function createDriver($id)
    {
        /**
         * Возраст водителей будет задан случайным образом от 18 до 28 лет
         */
        $this->insert('{{%driver}}', [
            'id' => $id,
            'name' => 'Фамилия' . rand(0, 1000) . ' Имя' . rand(0, 1000) . ' Отчество' . rand(0, 1000),
            'birth_date' => (new DateTime())->sub(new DateInterval('P18Y' . rand(0, 3650) . 'D'))->format('Y-m-d'),
        ]);
    }

    /**
     * @param int $id
     */
    private function createModel($id)
    {
        /**
         * Возраст водителей будет задан случайным образом от 18 до 28 лет
         */
        $this->insert('{{%model}}', [
            'id' => $id,
            'name' => 'Модель #' . rand(0, 1000),
            'speed' => rand(40,90),
        ]);
    }

    private function generateModels($countBusModel, $allModelCount)
    {
        $busModels = [];
        while ($countBusModel>0) {
            $createdBusModel = rand(1,$allModelCount);

            while(in_array($createdBusModel, $busModels)) {
                $createdBusModel = rand(1,$allModelCount);
            }

            $busModels[] = $createdBusModel;
            $countBusModel--;
        }

        return $busModels;
    }
}
