<?php

namespace app\controllers;

use yii\web\Controller;
use OpenApi\Serializer;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['/docs']);
    }
}
