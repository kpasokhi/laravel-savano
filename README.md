Savano Payment
================
Savano Payment Gateway For Laravel

Installation
==============
The preferred way to install this extension is through composer.

Either run
```
php composer.phar require kpasokhi/savano
```
or add
```
"kpasokhi/savano": "1.*"
```
to the require section of your ``composer.json`` file.
    
How to use this extension
=========================
For example, imagine that you have a controller called this PaymentController at first you need 2 actions,
one of them is for request payment and another is verify payment.

You need to use an storage to save your payments and payments status.

``PaymentController.php``
```
..... 

<?php
public function actionRequest()
{
    /* Your Data */
    $pin      = 'Your Pin';
    $callback = 'Your Callback Url';

    /* Save Price, OrderId and Authority In Your Storage */
    $price    = 1000;
    $orderId  = 1;

    $savano = new Savano;
    $savano->pin = $pin;

    if($request = $savano->request($price, $orderId, $callback)->getResult() === 1)
    {
        // $authority = $savano->getAuthority();
        // You can save your payment request data to the database in here before redirect user to bank

        return $this->redirect($savano->getRedirectUrl());
    }
    else
    {
        // Show Error.
        echo $savano->getErrorMessage();
    }
}

public function actionVerify()
{
    $pin = 'Your Pin';

    /* Fetch Price, OrderId and Authority From Your Storage */
    $authority = 'xxxxxxxxxxxxxxx';
    $price     = 1000;
    $orderId   = 1;

    $savano = new Savano;
    $savano->pin = $pin;

    if($verify = $savano->verify($authority, $price, $orderId)->getResult() === 1)
    {
        // Payment Successfully
    }
    else
    {
        // Show Error
        echo $savano->getErrorMessage();
    }
}

.....
```
