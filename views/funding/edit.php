<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\widgets\AmountField;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\DatePicker;
use yii\web\JsExpression;
use humhub\modules\file\widgets\Upload;

/** @var $myAsset Asset */
$upload = Upload::withName();
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>


<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'amount')->widget(AmountField::class, ['asset' => $model->challenge->asset])->label(Yii::t('XcoinModule.funding', 'Requested amount')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput()
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign title')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'deadline')->widget(DatePicker::class, [
                'dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'],
                'clientOptions' => ['minDate' => '+1d'],
                'options' => ['class' => 'form-control', 'autocomplete' => "off"]])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign deadline')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign country')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput()
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign city')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea(['maxlength' => 255])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign description')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.funding', 'Please enter your campaign needs & commitments')) ?>
        </div>
        <div class="col-md-12">
            <label class="control-label" for="funding-content"><?= Yii::t('XcoinModule.base', 'Gallery')?></label>
            <div class="row">
                <div class="col-md-2">
                    <?= $upload->button([
                        'label' => true,
                        'tooltip' => false,
                        'options' => ['accept' => 'image/*'],
                        'cssButtonClass' => 'btn-default btn-sm',
                        'dropZone' => '#account-form',
                        'max' => 7,
                    ]) ?>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-9">
                    <?= $upload->preview([
                        'options' => ['style' => 'margin-top:10px'],
                        'model' => $model,
                        'showInStream' => true,
                    ]) ?>
                </div>
            </div>
            <br>
            <?= $upload->progress() ?>
            <p class="help-block">
                <?= Yii::t('XcoinModule.funding', 'Please note that first picture will be used as cover for your crowdfunding campaign.') ?>
            </p>
        </div>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

