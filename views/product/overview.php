<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\widgets\BuyProductButton;
use humhub\widgets\TimeAgo;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;

Assets::register($this);

/**
 * @var $product Product
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-9 fundingPanel">
            <div class="row">
                <?php
                $space = $product->getSpace()->one();
                $picture = $product->getPicture();
                ?>

                <div class="col-md-12">
                    <div class="panel cover">
                        <div class="panel-heading">
                            <!-- product image start -->
                            <div class="img-container">

                                <?php if ($picture) : ?>
                                    <div class="bg" style="background-image: url('<?= $picture->getUrl() ?>')"></div>
                                    <?= Html::img($picture->getUrl(), ['width' => '100%']) ?>
                                <?php else : ?>
                                    <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                        'width' => '100%'
                                    ]) ?>
                                <?php endif ?>

                            </div>
                            <!-- product image end -->
                            <!-- product buy action start -->
                            <div class="invest-btn">
                                <?= BuyProductButton::widget(['guid' => $product->getCreatedBy()->one()->guid])?>
                            </div>
                            <!-- product buy action end -->
                            <!-- product edit button start -->

                            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) || $product->isOwner(Yii::$app->user->identity)): ?>
                                <?= Html::a(Yii::t('XcoinModule.base', '<i class="fa fa-pencil"></i>Edit'), ['/xcoin/product/edit', 'id' => $product->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                            <?php endif; ?>
                            <!-- product edit button end -->

                        </div>
                        <div class="panel-body">

                            <!-- product name start -->
                            <h4 class="funding-title"><?= Html::encode($product->name); ?></h4>
                            <!-- product name end -->

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- product description start -->
                                    <p class="media-heading"><?= Html::encode($product->description); ?></p>
                                    <!-- product description end -->
                                </div>
                                <div class="col-md-3"></div>
                                <div class="col-md-3">
                                    <div class="col-md-12 funding-details">
                                        <!-- product pricing & discount start -->
                                        <?= SpaceImage::widget([
                                            'space' => $product->asset->space,
                                            'width' => 30,
                                            'showTooltip' => true,
                                            'link' => false
                                        ]); ?>
                                        <div class="text-center">
                                            <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                                                Price : <b><?= $product->price ?></b>
                                                <small> <?= $product->getPaymentType() ?> </small>
                                            <?php else : ?>
                                                <?= $product->discount ?> % Discount
                                            <?php endif; ?>
                                        </div>
                                        <!-- product pricing & discount end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <?= $product->status ? 'Available' : 'Unavailable' ?> |
                            <?= Html::icon('time') ?>
                            <?= TimeAgo::widget(['timestamp' => $product->created_at]); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel content">
                        <div class="panel-body">
                            <!-- product content start -->
                            <?= RichText::output($product->content); ?>
                            <!-- product content end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>