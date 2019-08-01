<?php

namespace humhub\modules\xcoin\grids;

use humhub\modules\content\models\ContentContainer;
use humhub\modules\content\models\ContentContainerSetting;
use humhub\modules\xcoin\helpers\SpaceHelper;
use Yii;
use yii\bootstrap\Html;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class AccountsGridView extends GridView
{

    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Module settings allowDirectCoinTransfer parameter value
//        $allowDirectCoinTransfer = null;
//        if ($this->contentContainer instanceof Space) {
//            $module = Yii::$app->getModule('xcoin');
//            $allowDirectCoinTransfer = $module->settings->space()->get('allowDirectCoinTransfer');
//        }


        $this->dataProvider = new ActiveDataProvider([
            'query' => AccountHelper::getAccountsQuery($this->contentContainer),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->columns = [
            [
                'attribute' => 'space_id',
                'label' => Yii::t('XcoinModule.base', 'Owner'),
                'format' => 'raw',
                'options' => ['style' => 'width:35px'],
                'visible' => (!$this->contentContainer instanceof Space),
                'value' => function ($model) {
                    if ($model->space !== null) {
                        return SpaceImage::widget(['space' => $model->space, 'width' => 26]);
                    }

                    return '-';
                }
            ],
            [
                'attribute' => 'user_id',
                'label' => Yii::t('XcoinModule.base', 'Manager'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'visible' => (!$this->contentContainer instanceof User),
                'value' => function ($model) {
                    if ($model->user === null) {
                        return '-';
                    }

                    return UserImage::widget(['user' => $model->user, 'width' => 26]);
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->account_type == Account::TYPE_ISSUE) {
                        return '<span class="label label-info">ISSUES</span>';
                    }
                    if ($model->account_type == Account::TYPE_FUNDING) {
                        return $model->funding ?
                            '<span class="label label-info">FUNDINGS</span> ' .
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/xcoin/funding/overview',
                                    'fundingId' => $model->funding->id,
                                    'container' => $this->contentContainer
                                ]
                            ) :
                            '<span class="label label-danger">FUNDINGS</span> ' .
                            Html::encode($model->title) . ' ('. Yii::t('XcoinModule.funding', 'Deleted Campaign'). ' )';
                    }
                    if ($model->account_type == Account::TYPE_DEFAULT) {
                        return '<span class="label label-info">DEFAULT</span>';
                    }
                    if ($model->account_type == Account::TYPE_TASK) {
                        return '<span class="label label-info">TASK</span> ' .
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/tasks/task/view',
                                    'id' => $model->task->id,
                                    'container' => $this->contentContainer
                                ]
                            );
                    }

                    return Html::encode($model->title) . '<i class="fa fa-users>"></i>';
                }
            ],
            [
                'label' => Yii::t('XcoinModule.base', 'Asset(s) balance'),
                'format' => 'raw',
                'value' => function ($model) {
                    $list = [];
                    foreach ($model->getAssets() as $asset) {
                        $list[] = '<strong>' . $model->getAssetBalance($asset) . '</strong>&nbsp; ' .
                            SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]) . '</span>';
                    }

                    return implode('&nbsp;&nbsp;&middot;&nbsp;&nbsp;', $list);
                }
            ],
            [
                'format' => 'raw',
                'options' => ['style' => 'width:220px;text-align:right'],
                'contentOptions' => ['style' => 'text-align:right'],
                'value' => function ($model) {

                    $transferButton = '';
                    if (AccountHelper::canManageAccount($model) && Account::TYPE_TASK != $model->account_type) {

                        // Module settings allowDirectCoinTransfer parameter value
                        $allowDirectCoinTransfer = SpaceHelper::allowDirectCoinTransfer($model);

                        if(!isset($allowDirectCoinTransfer) || $allowDirectCoinTransfer) {
                            $accountAssetsList = AccountHelper::getAssetsList($model);

                            if (!empty($accountAssetsList))
                                $transferButton = Html::a('<i class="fa fa-exchange" aria-hidden="true"></i>', ['/xcoin/transaction/transfer', 'accountId' => $model->id, 'container' => $this->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']) . '&nbsp;';
                            else
                                $transferButton = Html::a(
                                        '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                        ['javascript:;'],
                                        [
                                            'class' => 'btn btn-default',
                                            'disabled' => true,
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'right',
                                            'title' => 'No assets available on this account!',
                                            'onclick' => 'return false;'
                                        ]
                                    ) . '&nbsp;';
                        }else
                            $transferButton = Html::a(
                                '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                ['javascript:;'],
                                [
                                    'class' => 'btn btn-default',
                                    'disabled' => true,
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'right',
                                    'title' => Yii::t('XcoinModule.base', 'Direct coin transfer disabled by the space admin'),
                                    'onclick' => 'return false;'
                                ]
                            ) . '&nbsp;';
                    }


                    $overviewButton = Html::a('<i class="fa fa-search" aria-hidden="true"></i>', ['/xcoin/account', 'id' => $model->id, 'container' => $this->contentContainer], ['class' => 'btn btn-default']);

                    return $transferButton . $overviewButton;
                }
            ],
        ];


        parent::init();
    }

}
