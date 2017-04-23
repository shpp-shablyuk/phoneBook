<?php

use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

	<?= $form->field($model, 'fio')->widget(\yii\jui\AutoComplete::classname(), [
		'options' => [
			'placeholder' => 'Type name',
			'class' => 'form-control',
		],
		'clientOptions' => [
			'source' => Url::to(['/names/list']),
		],
	]) ?>

    <?= $form->field($model, 'country_id')->widget(Select2::classname(), [
	    'data' => $countries,
	    'options' => [
		    'placeholder' => 'Select a state ...',
		    'id' => 'countries',
	    ],
	    'pluginOptions' => [
		    'allowClear' => true
	    ],
    ]); ?>

    <?= $form->field($model, 'city_id')->widget(DepDrop::classname(), [
	    'data'=> $city,
	    'options'=>[
		    'id'=>'cities',
	    ],
	    'type' => DepDrop::TYPE_SELECT2,
	    'pluginOptions'=>[
		    'depends'=>['countries'],
		    'placeholder'=>'Select...',
		    'url'=>Url::to(['/cities/list'])
	    ],
    ]); ?>

	<div class="panel panel-default">
		<div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> Phones</h4></div>
		<div class="panel-body">
			<?php DynamicFormWidget::begin([
				'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
				'widgetBody' => '.container-items', // required: css class selector
				'widgetItem' => '.item', // required: css class
				'limit' => 4, // the maximum times, an element can be cloned (default 999)
				'min' => 1, // 0 or 1 (default 1)
				'insertButton' => '.add-item', // css class
				'deleteButton' => '.remove-item', // css class
				'model' => $modelsPhones[0],
				'formId' => 'dynamic-form',
				'formFields' => [
					'phone',
				],
			]); ?>

			<div class="container-items"><!-- widgetContainer -->
				<?php foreach ($modelsPhones as $i => $modelPhones): ?>
					<div class="item panel panel-default"><!-- widgetBody -->
						<div class="panel-heading">
							<h3 class="panel-title pull-left">Phones</h3>
							<div class="pull-right">
								<button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
								<button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<?php
							// necessary for update action.
							if (! $modelPhones->isNewRecord) {
								echo Html::activeHiddenInput($modelPhones, "[{$i}]id");
							}
							?>
							<?= $form->field($modelPhones, "[{$i}]phone")->textInput(['maxlength' => true]) ?>

						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php DynamicFormWidget::end(); ?>
		</div>
	</div>

	<div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
