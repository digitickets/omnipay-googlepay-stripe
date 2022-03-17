# Omnipay: Google Pay

**Google Pay driver for the Omnipay PHP payment library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+.

This package implements only Google Pay support for Omnipay 2.x Off-sites, where the customer is redirected to enter payment details

## Installation

This package is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "digitickets/omnipay.googlepay": "^0.*"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Google Pay

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

This is a sample code of standard Off-site controller using the driver.

### Request a payment
```
// Gateway setup
$gateway = $this->gatewayFactory('GooglePay');

// Create or fetch your product transaction
$transaction = $this->createTransaction($request);

// Get the data ready for the payment
// Please note that even off-site gateways make use of the CreditCard object,
// because often you need to pass customer billing or shipping details through to the gateway.
$cardData = $transaction->asOmniPay;
$itemsBag = $this->requestItemsBag($request);

// Authorize request
$request = $gateway->purchase(array(
    'amount' => $transaction->amount,
    'currency' => $transaction->currency,
    'card' => $cardData,
    'returnUrl' => $this->generateCallbackUrl(
        'GooglePay',
        $transaction->id
    ),
    'transactionId' => $transaction->id,
    'description' => $transaction->description,
    'items' => $itemsBag,
));

// Send request
$response = $request->send();

// Process response
$this->processResponse($response);
```

### Process payment result
```
// Fetch transaction details
$transaction = Transaction::findOrFail($transactionId);

// Gateway setup
$gateway = $this->gatewayFactory('GooglePay');

// Get the data ready to complete the payment. Since this is typically a stateless callback
// we need to first retrieve our original product transaction details
$params = [
    "amount" => $transaction->amount,
    "currency" => $transaction->currency,
    'returnUrl' => $this->generateCallbackUrl(
        'GooglePay',
        $transaction->id
    ),
    'transactionId' => $transaction->id,
    'transactionReference' => $transaction->ref,
];

// Complete purchase request
$request = $gateway->completePurchase($params);

// Send request
$response = $request->send();

// Process response
$this->processResponse($response);
```
