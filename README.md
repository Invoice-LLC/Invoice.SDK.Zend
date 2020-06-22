<h1>Invoice Payment Module</h1>

<h3>Установка</h3>

Установите пакет через Composer:
```
composer require invoice-llc/payment-zend:dev-master
```

Перейдите в папку: **vendor/invoice-llc/zend-module/config**
В файле module.config.php впишите свой логин от личного кабинета и API Key
```
return [
  'invoice' => [
      'api_key' => '1526fec01b5d11f4df4f2160627ce351',
      'login' => 'demo',
  ]
];
```

<h3>Создание контроллера уведомлений</h3>

1. Создайте контроллер и унаследуйте класс AbstractNotifyController

```php
<?php

class InvoiceController extends AbstractNotfyController {

        //orderID - ID заказа в вашей системе

        function onPay($orderId, $amount)
        {
    
            //При успешной оплате
        }
    
        function onFail($orderId)
        {
            //При неудачной оплате
        }
    
        function onRefund($orderId)
        {
            //При возврате средств
        }
}
```

2. Создайте маршрут

```php
'invoice' => [
    'type' => Literal::class,
    'options' => [
        'route' => '/notify',
        'defaults' => [
            'controller' => Controller\InvoiceController::class,
            'action' => 'notify'
        ]
    ],
],
```

3. В личном кабинете Invoice(Настройки->Уведомления->Добавить) добавьте уведомление с типом **WebHook**
и адресом, который вы задали в конфиге(например: %url%/notify)

<h3>Создание платежей</h3>

```php
<?php

$invoice = new InvoicePaymentManager();

$items = [
    //Название, цена за 1шт, кол-во, итоговая цена
    new ITEM('Какой-то предмет',10,1,10)
];
//ID заказа, цена, товары
$payment = $invoice->createPayment('ID заказа в вашей системе', 10, $items);

echo($payment->payment_url);
```

<h3>Поулчение статуса платежа</h3>

```php
<?php

$invoice = new InvoicePaymentManager();

$payment = $invoice->getPayment('ID заказа в вашей системе');

echo($payment->payment_url);
```

<h3>Создание возврата</h3>

```php
<?php

$invoice = new InvoicePaymentManager();

//ID заказа в вашей системе, сумма возврата, причина
$refundInfo = $invoice->createRefund('ID заказа в вашей системе', 10, 'Причина');

```
