<?php

namespace app\components;

use yii\base\Component;
use yii\db\Exception;
use yii\helpers\Html;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

class DistanceComponent extends Component{ // объявляем класс

    /**
     * @var string
     */
    private $apiUrl = 'https://darkdef.com/distance.php';

    /**
     * @param string $from
     * @param string $to
     *
     * @return int
     * Результаты запросов кэшируем, чтобы не нагружать API
     * Можно было бы реализовать хранение в параметре и мы бы имели данные только в рамках одного запроса,
     * но мы будем исходить из предположения, что расстояние между городами не меняется
     */
    public function getDistance($from, $to)
    {
        return \Yii::$app->cache->getOrSet(
            [
                'distance',
                'from' => $from,
                'to' => $to,
            ], function () use($from, $to) {
            return $this->getDistanceFromApi($from, $to);
        });
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return int
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     *
     * Используется собственная реализация API для сервис получения информации.
     * В API-заглушке для расстояний есть расстояния между двумя городами
     * Москва - Казань - 11000км
     * Москва - Краснодар - 13000км
     * (и обратные расстояния )))
     */
    private function getDistanceFromApi($from, $to)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '?' . http_build_query([
                'from' => $from,
                'to' => $to,
            ])
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new NotAcceptableHttpException('Error on get distance from API', 500);
        }

        $data = json_decode($result, true);

        if (empty($data['distance'])) {
            throw new NotFoundHttpException($data['error']??'Error on get distance from API', 404);
        }
        return (int)$data['distance'];
    }
}