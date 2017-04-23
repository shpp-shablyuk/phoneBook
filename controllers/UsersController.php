<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Countries;
use app\models\Phones;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use app\models\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
	            'class' => \yii\filters\AccessControl::className(),
	            'only' => ['create','update'],
	            'rules' => [
		            // allow authenticated users
		            [
			            'allow' => true,
			            'roles' => ['@'],
		            ],
		            // everything else is denied
	            ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();
	    $modelsPhones = [new Phones()];

	    if ($model->load(Yii::$app->request->post())) {
		    $modelsPhones = Model::createMultiple(Phones::classname());
		    Model::loadMultiple($modelsPhones, Yii::$app->request->post());

		    // validate all models
		    $valid = $model->validate();
		    $valid = Model::validateMultiple($modelsPhones) && $valid;

		    if ($valid) {
			    $transaction = \Yii::$app->db->beginTransaction();
			    try {
				    if ($flag = $model->save(false)) {
					    foreach ($modelsPhones as $modelPhones) {
						    $modelPhones->user_id = $model->id;
						    if (! ($flag = $modelPhones->save(false))) {
							    $transaction->rollBack();
							    break;
						    }
					    }
				    }
				    if ($flag) {
					    $transaction->commit();
					    return $this->redirect(['view', 'id' => $model->id]);
				    }
			    } catch (\Exception $e) {
				    $transaction->rollBack();
			    }
		    }
	    }

        return $this->render('create', [
            'model' => $model,
            'modelsPhones' => (empty($modelsPhones)) ? [new Phones] : $modelsPhones,
            'countries' => ArrayHelper::map(Countries::find()->all(), 'id', 'name'),
            'city' => ArrayHelper::map([Cities::findOne($model->city_id)], 'id', 'name'),
        ]);

    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
	    $modelsPhones = $model->phones;

	    if ($model->load(Yii::$app->request->post())) {

		    $oldIDs = ArrayHelper::map($modelsPhones, 'id', 'id');
		    $modelsPhones = Model::createMultiple(Phones::classname(), $modelsPhones);
		    Model::loadMultiple($modelsPhones, Yii::$app->request->post());
		    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsPhones, 'id', 'id')));

		    // validate all models
		    $valid = $model->validate();
		    $valid = Model::validateMultiple($modelsPhones) && $valid;

		    if ($valid) {
			    $transaction = \Yii::$app->db->beginTransaction();
			    try {
				    if ($flag = $model->save(false)) {
					    if (! empty($deletedIDs)) {
						    Phones::deleteAll(['id' => $deletedIDs]);
					    }
					    foreach ($modelsPhones as $modelPhones) {
						    $modelPhones->user_id = $model->id;
						    if (! ($flag = $modelPhones->save(false))) {
							    $transaction->rollBack();
							    break;
						    }
					    }
				    }
				    if ($flag) {
					    $transaction->commit();
					    return $this->redirect(['view', 'id' => $model->id]);
				    }
			    } catch (\Exception $e) {
				    $transaction->rollBack();
			    }
		    }
	    }

        return $this->render('update', [
            'model' => $model,
            'modelsPhones' => (empty($modelsPhones)) ? [new Phones] : $modelsPhones,
            'countries' => ArrayHelper::map(Countries::find()->all(), 'id', 'name'),
            'city' => ArrayHelper::map([Cities::findOne($model->city_id)], 'id', 'name'),
        ]);

    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
