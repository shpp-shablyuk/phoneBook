<?php

namespace app\controllers;

use app\models\Names;
use Yii;
use yii\helpers\Json;

class NamesController extends \yii\web\Controller
{
    public function actionList()
    {
	    if (!Yii::$app->request->isAjax) {
		    return;
	    }

	    $q = Yii::$app->request->get('term');

	    if (empty($q)) {
		    return;
	    }

	    $names = Names::find()
		    ->select(['Name'])
		    ->where(['like','name', $q . '%', false])->limit(10)->column();

	    if ($names) {
		    echo Json::encode($names);
	    }
    }
}
