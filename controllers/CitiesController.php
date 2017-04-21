<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use app\models\Cities;

class CitiesController extends \yii\web\Controller
{
    public function actionIndex()
    {

	    $countryId = Yii::$app->request->post('depdrop_parents', '');
	    if (empty($countryId)) {
		    echo Json::encode(['output'=>'', 'selected'=>'']);
	    }

	    $model = Cities::find()
		    ->select(['id', 'name'])
		    ->where(['country_id' => $countryId])
		    ->orderBy('name')
		    ->asArray()->all();

	    echo Json::encode(['output'=>$model, 'selected'=>'']);
	    return;
    }

}
