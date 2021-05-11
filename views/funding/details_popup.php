<?php

use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;

use humhub\modules\xcoin\assets\Assets;
use yii\bootstrap\Carousel;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\libs\Iso3166Codes;
use yii\bootstrap\Progress;
use humhub\modules\content\widgets\richtext\RichText;


Assets::register($this);

/**
 * @var $funding Funding
 */
?>

<div class="space-funding funding-details-popup" id="funding-popup">
<?php
    $space = $funding->getSpace()->one();
    $cover = $funding->getCover();
    $gallery = $funding->getGallery();

    $carouselItems = [];

    $coverItemUrl = '';

    if ($cover):
        $coverItemUrl = $cover->getUrl();
    else:
        $coverItemUrl = Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png';
    endif;

    $coverItem = "<div class=\"carousel-item\">";
    $coverItem .= "<div class=\"bg\" style=\"background-image: url('{$coverItemUrl}')\"></div>";
    $coverItem .= Html::img($coverItemUrl, ['width' => '100%']);
    $coverItem .= "</div>";

    $carouselItems[] = $coverItem;

    foreach ($gallery as $item):

        $carouselItem = "<div class=\"carousel-item\">";
        $carouselItem .= "<div class=\"bg\" style=\"background-image: url('{$item->getUrl()}')\"></div>";
        $carouselItem .= Html::img($item->getUrl(), ['width' => '100%']);
        $carouselItem .= "</div>";

        $carouselItems[] = $carouselItem;
    endforeach;

    $fundingUrl = $space->createUrl('/xcoin/funding/overview', ['fundingId' => $funding->id], true);
    ?>
    <?php ModalDialog::begin(['header' => Html::encode($funding->title), 'closable' => false]) ?>
    <?php $form = ActiveForm::begin(['id' => 'funding-details']); ?>
    <div class="modal-container">
        <div class="modal-heading">
            <div class="modal-subtitle">
                <span class="text"><?= Html::encode($funding->description); ?></span>
                <div class="social-share">
                    <span class="text"><?= Yii::t('XcoinModule.funding', 'Share') ?></span>
                    <span class="sharer" data-sharer="fb" data-link="<?= $fundingUrl ?>"><i class="fa fa-facebook-square"></i></span>
                    <span class="sharer" data-sharer="tw" data-link="<?= $fundingUrl ?>"><i class="fa fa-twitter-square"></i></span>
                    <span class="sharer" data-sharer="in" data-link="<?= $fundingUrl ?>"><i class="fa fa-linkedin-square"></i></span>
                </div>
            </div>
            <div class="img-container">
                <?php if ($cover) : ?>
                    <?php if (count($carouselItems) > 1): ?>
                        <?= Carousel::widget([
                            'items' => $carouselItems,
                        ]) ?>
                    <?php else: ?>
                        <div class="bg"
                            style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                        <?= Html::img($cover->getUrl(), [
                            'width' => '100%'
                        ]) ?>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="bg"
                        style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                        'width' => '100%'
                    ]) ?>
                <?php endif ?>
            </div>
        </div>
        <div class="modal-info row">
            <div class="col-md-8">
                <div class="text-content">
                    <span class="text-heading"><?= Yii::t('XcoinModule.funding', 'About the project') ?></span>
                    <div class="funding-content">
                        <?= RichText::output($funding->content); ?>
                    </div>
                </div>
            </div>
            <div class="info col-md-4">
                <div class="side-widget">
                    <div class="info">
                        <label><?= Yii::t('XcoinModule.funding', 'Created by') ?></label>
                        <span><strong><?= $space->name ?></strong></span>

                        <label><?= Yii::t('XcoinModule.funding', 'Requesting') ?></label>
                        <span class="requesting">
                            <strong>
                                <?= $funding->getRequestedAmount() ?>
                            </strong>
                            <?= SpaceImage::widget([
                                'space' => $funding->getChallenge()->one()->asset->space,
                                'width' => 24,
                                'showTooltip' => true,
                                'link' => true
                            ]); ?>
                        </span>

                        <label><?= Yii::t('XcoinModule.funding', 'Rewarding') ?></label>
                        <span></span>

                        <label><?= Yii::t('XcoinModule.funding', 'Location') ?></label>
                        <span><strong><?= Iso3166Codes::country($funding->country) . ', ' . $funding->city ?></strong></span>

                        <label><?= Yii::t('XcoinModule.funding', 'Category') ?></label>
                        <span>
                            <?= join(', ', array_map(function($cat) {
                                return '<strong>' . $cat->name . '</strong>';
                            }, $funding->getCategories()->all())) ?> 
                        </span>
                    </div>
                    <hr/>
                    <div class="funding-progress">
                        <div class="progress-info">
                            <div class="raised">
                                <i class="fa fa-dot-circle-o"></i>
                                <div class="infos">
                                    <strong><?= $funding->getRaisedAmount() ?></strong>
                                    (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                    <br>
                                    <h6><?= Yii::t('XcoinModule.funding', 'Raised:') ?></h6>
                                </div>
                            </div>
                            <div class="days">
                                <i class="fa fa-clock-o"></i>
                                <div class="infos">
                                    <?php if ($funding->getRemainingDays() > 0) : ?>
                                        <strong><?= $funding->getRemainingDays() ?></strong>
                                        <br>
                                        <h6><?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?></h6>
                                    <?php else : ?>
                                        <strong><?= Yii::t('XcoinModule.funding', 'Closed') ?></strong>
                                        <br>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="progress-value">
                            <?php echo Progress::widget([
                                'percent' => $funding->getRaisedPercentage(),
                            ]); ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="contributors">
                        <h6><?= Yii::t('XcoinModule.funding', 'Contributors') ?></h6>
                        <?php $distributions = $funding->getContributors(); ?>
                        <?php if (count($distributions) === 0): ?>
                            <?= Yii::t('XcoinModule.funding', 'No contribution done yet.') ?>
                        <?php endif; ?>

                        <?php foreach ($distributions as $info): ?>
                            <?php
                            $total = round($info['balance'], 4) . ' (' . Yii::t('XcoinModule.base', 'Shareholdings:') . ' ' . $info['percent'] . '%)';
                            ?>
                            <?= UserImage::widget(['user' => $info['record'], 'showTooltip' => true, 'tooltipText' => $info['record']->displayName . "\n" . $total]) ?>
                        <?php endforeach; ?>
                    </div>
                    <hr/>
                    <div class="actions">
                        <!-- campaign invest action start -->
                        <div class="invest-btn <?= !$funding->canInvest() ? 'disabled' : '' ?>">
                            <?php if (Yii::$app->user->isGuest): ?>
                                <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                            <?php else: ?>
                                <?= Html::a(Yii::t('XcoinModule.funding', 'Fund this project'), [
                                    'invest',
                                    'fundingId' => $funding->id,
                                    'container' => $this->context->contentContainer
                                ], ['data-target' => '#globalModal']); ?>
                            <?php endif; ?>
                        </div>
                        <!-- campaign invest action end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <?php ModalDialog::end() ?>
</div>
<script>
    $('.social-share .sharer').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let sharer = $(this).data('sharer');
        let link = $(this).data('link');
        console.log();
        switch (sharer) {
            case 'fb':
                window.open("https://www.facebook.com/sharer.php?u="+link,"","height=368,width=600,left=100,top=100,menubar=0");
                break;
            case 'tw':
                window.open("https://twitter.com/share?url="+link+"&text=Come%20join","","height=260,width=500,left=100,top=100,menubar=0");
                break;
            case 'in':
                window.open("https://www.linkedin.com/sharing/share-offsite/?url="+link,"","height=260,width=500,left=100,top=100,menubar=0");
                break;
        
            default:
                break;
        }
    });
</script>