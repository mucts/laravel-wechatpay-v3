<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LaravelWechatpayV3\Service;

use LaravelWechatpayV3\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property \LaravelWechatpayV3\Service\Certificate\Client $certificate
 * @property \LaravelWechatpayV3\Service\Notify\Client $notify
 * @property \LaravelWechatpayV3\Service\Merchant\Media\Client $media
 * @property \LaravelWechatpayV3\Service\CombineTransaction\Client $combineTransaction
 * @property \LaravelWechatpayV3\Service\Ecommerce\Applyment\Client $applyment
 * @property \LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\Order\Client $profitSharingOrder
 * @property \LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\ReturnOrder\Client $profitSharingReturnOrder
 * @property \LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\FinishOrder\Client $profitSharingFinishOrder
 * @property \LaravelWechatpayV3\Service\Ecommerce\Refund\Client $refund
 * @property \LaravelWechatpayV3\Service\Fund\Withdraw\Client $withdraw
 * @property \LaravelWechatpayV3\Service\Fund\Balance\Client $balance
 * @property \LaravelWechatpayV3\Service\Bill\Client $bill
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Certificate\ServiceProvider::class,
        Notify\ServiceProvider::class,
        Merchant\Media\ServiceProvider::class,
        CombineTransaction\ServiceProvider::class,
        Ecommerce\Applyment\ServiceProvider::class,
        Ecommerce\ProfitSharing\Order\ServiceProvider::class,
        Ecommerce\ProfitSharing\ReturnOrder\ServiceProvider::class,
        Ecommerce\ProfitSharing\FinishOrder\ServiceProvider::class,
        Ecommerce\Refund\ServiceProvider::class,
        Fund\Withdraw\ServiceProvider::class,
        Fund\Balance\ServiceProvider::class,
        Bill\ServiceProvider::class,
    ];
}
