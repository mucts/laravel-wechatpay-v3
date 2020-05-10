# laravel-wechatpay-v3

用于 Laravel/Lumen 框架的微信支付 V3 的 API

## 安装
不低于 Laravel 5.7

```shell
$ composer require mucts/laravel-wechatpay-v3:^1.0
```

## Laravel 配置方法

由于设置了 Laravel providers 自动加载，所以不需要额外操作。

## Lumen 配置方法

在 `bootstrap/app.php` 中增加：
```php
$app->register(MuCTS\Laravel\WeChatPayV3\ServiceProvider::class);
```

## 使用
### API 列表
```php
use MuCTS\Laravel\WeChatPayV3\Facades\WeChatPay;

$weChatPay = WeChatPay::app();

// 证书目录
$weChatPay->certificate->all($query, $options);

// 解析异步通知
$weChatPay->notify->parseResponse($response);

// 上传媒体文件
$weChatPay->media->upload($fileName, $content, $mimeType, $options);

// 子商户入驻（申请）
$weChatPay->applyment->create($params, $options);

// 子商户入驻（查询）
$weChatPay->applyment->retrieve($id, $query, $options);

// 合单支付（app）
$weChatPay->combineTransaction->createByApp($params, $options);

// 合单支付（jsApi）
$weChatPay->combineTransaction->createByJsApi($params, $options);

// 合单支付查询
$weChatPay->combineTransaction->retrieveByOutTradeNo($outTradeNo, $query, $options); // 使用商户订单号

// 合单支付关闭
$weChatPay->combineTransaction->closeByOutTradeNo($outTradeNo, $query, $options); // 使用商户订单号

// 退款（发起）
$weChatPay->refund->create($params, $options);

// 退款（查询）
$weChatPay->refund->retrieveByOutRefundNo($id, $query, $options); // 使用商户退款单号
$weChatPay->refund->retrieve($id, $query, $options);  // 使用微信退款单号

// 分账（请求分账）
$weChatPay->profitSharingOrder->create($params, $options);

// 分账（查询分账）
$weChatPay->profitSharingOrder->retrieve($id, $query, $options);

// 分账（请求分账回退）
$weChatPay->profitSharingReturnOrder->create($params, $options);

// 分账（查询分账回退）
$weChatPay->profitSharingReturnOrder->retrieve($id, $query, $options);

// 分账（完结分账）
$weChatPay->profitSharingFinishOrder->create($params, $options);

// 提现（发起）
$weChatPay->withdraw->create($params, $options);

// 提现（查询）
$weChatPay->withdraw->retrieve($id, $query, $options);

// 查询余额
$weChatPay->balance->retrieve($subMerchantId, $query, $options);

// 申请交易账单
$weChatPay->bill->retrieveTradeBill($query, $options);

// 申请资金账单
$weChatPay->bill->retrieveFundFlowBill($query, $options);

// 账单文件下载
$weChatPay->bill->download($body); // $body 使用申请交易账单或申请资金账单接口返回的数据
```

### 敏感参数加解密
在设置请求的参数($query 或 $params)时，无需手动对敏感参数进行加解密。仅需要在 $options 参数中申明需要加解密的参数（支持点运算符）即可。
例如：
```php
$options = [
    // 加密
    'encode_params' => [
        'id_card_info.id_card_name',
        'id_card_info.id_card_number',
        'account_info.account_name',
        'account_info.account_number',
        'contact_info.contact_name',
        'contact_info.contact_id_card_number',
        'contact_info.mobile_phone',
        'contact_info.contact_email',
    ],
    // 解密
    'decode_params' => [
        'account_validation.account_name',
        'account_validation.pay_amount',
    ]
];

```