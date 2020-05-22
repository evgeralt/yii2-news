<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model CabinetForm */

use app\models\CabinetForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Cabinet';
$this->params['breadcrumbs'][] = $this->title;
$allowedSettings = \app\models\NotificationSettings::ALLOWED_SETTINGS;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'password_repeat')->passwordInput() ?>

            <?= $form->field($model, 'notification_settings')->checkboxList(array_combine($allowedSettings, $allowedSettings)) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'save']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>