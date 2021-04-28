<?php

use humhub\libs\Html;

/** @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/** @var $name string */

/** @var $res string */

$res = Yii::$app->request->get('res');

?>

<div class="purchase-container" style="display: inline">
    <?= Html::a('<i class="fa fa-money" aria-hidden="true"></i> Buy ' . $name, [
        '/xcoin/overview/purchase-coin',
        'container' => $contentContainer
    ], ['class' => 'btn btn-default', 'data-target' => '#globalModal', 'style' => 'float: right; margin-bottom: 10px;']) ?>
    <?php if ($res === 'success') : ?>
        <p class="alert alert-info col-md-12">
            <?= Yii::t('XcoinModule.overview', 'Your purchase is being processed.. hold tight, it may take a while') ?>
        </p>
    <?php endif; ?>
    <?php if ($res === 'error') : ?>
        <p class="alert alert-danger col-md-12">
            <?= Yii::t('XcoinModule.overview', 'Your purchase is not processed, an error has occurred on the bridge side') ?>
        </p>
    <?php endif; ?>
</div>
