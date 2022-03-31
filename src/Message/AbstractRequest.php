<?php

namespace Digitickets\OmnipayGooglePayWithStripe\Message;

use Stripe\StripeClient as Stripe;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Guzzle\Http\Client;

/**
 * Abstract Request.
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /**
     * @var Gateway
     */
    protected $stripe;

    protected $form;

    /**
     * Create a new Request.
     *
     * @param Client $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     * @param Stripe          $stripe      The Stripe Gateway
     */
    public function __construct(
        Client $httpClient,
        HttpRequest $httpRequest,
        Stripe $stripe
    ) {
        $this->stripe = $stripe;

        parent::__construct($httpClient, $httpRequest);
    }

    /**
     * Set the correct configuration sending.
     *
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function send()
    {
        $this->configure();

        return parent::send();
    }

    public function configure()
    {
        //empty for now
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    // public function setSecretKey($value)
    // {
    //     return $this->setParameter('secretKey', $value);
    // }

    public function getAmount()
    {
        return floatval($this->getParameter('amount'));
    }

    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    public function getDescription()
    {
        return $this->getParameter('description');
    }

    public function getShipping()
    {
        $form = $this->getForm();

        return [
            'address' => [
                'city' => $form['shippingCity'],
                'country' => $form['shippingCountry'],
                'line1' => $form['shippingAddress1'],
                'line2' => $form['shippingAddress2'],
                'postal_code' => $form['shippingPostcode'],
                'state' => $form['shippingState'],
            ],
            'name' => $form['firstName'] . ' '. $form['lastName'],
            'phone' => $form['shippingPhone'],
        ];
    }

    public function getBilling()
    {
        $form = $this->getForm();

        return [
            'address' => [
                'city' => $form['billingCity'],
                'country' => $form['billingCountry'],
                'line1' => $form['billingAddress1'],
                'line2' => $form['billingAddress2'],
                'postal_code' => $form['billingPostCode'],
                'state' => $form['billingState'],
            ],
            'email' => $form['email'],
            'name' => $form['firstName'] . ' '. $form['lastName'],
            'phone' => $form['billingPhone'],
        ];
    }

    public function getStatus()
    {
        return 'succeeded'; //'pending', 'failed'
    }

    public function getMetaData()
    {
        $meta = [];

        foreach ($this->getItems() as $item) {
            $meta[] = [
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return $meta;
    }

    public function getForm()
    {
        return $this->getParameter('form');
    }

    public function setForm($value)
    {
        return $this->setParameter('form', $value);
    }

    public function getItems()
    {
        return $this->getParameter('items');
    }

    public function setItems($value)
    {
        return $this->setParameter('items', $value);
    }

    /**
     * @return array
     */
    // public function getCardData()
    // {
    //     $card = $this->getCard();

    //     if (! $card) {
    //         return [];
    //     }

    //     return [
    //         'billing' => [
    //             'company' => $card->getBillingCompany(),
    //             'firstName' => $card->getBillingFirstName(),
    //             'lastName' => $card->getBillingLastName(),
    //             'streetAddress' => $card->getBillingAddress1(),
    //             'extendedAddress' => $card->getBillingAddress2(),
    //             'locality' => $card->getBillingCity(),
    //             'postalCode' => $card->getBillingPostcode(),
    //             'region' => $card->getBillingState(),
    //             'countryName' => $card->getBillingCountry(),
    //         ],
    //         'shipping' => [
    //             'company' => $card->getShippingCompany(),
    //             'firstName' => $card->getShippingFirstName(),
    //             'lastName' => $card->getShippingLastName(),
    //             'streetAddress' => $card->getShippingAddress1(),
    //             'extendedAddress' => $card->getShippingAddress2(),
    //             'locality' => $card->getShippingCity(),
    //             'postalCode' => $card->getShippingPostcode(),
    //             'region' => $card->getShippingState(),
    //             'countryName' => $card->getShippingCountry(),
    //         ],
    //     ];
    // }

    /**
     * @return array
     */
    // public function getOptionData()
    // {
    //     $data = [
    //         'addBillingAddressToPaymentMethod' => $this->getAddBillingAddressToPaymentMethod(),
    //         'failOnDuplicatePaymentMethod' => $this->getFailOnDuplicatePaymentMethod(),
    //         'holdInEscrow' => $this->getHoldInEscrow(),
    //         'makeDefault' => $this->getMakeDefault(),
    //         'storeInVault' => $this->getStoreInVault(),
    //         'storeInVaultOnSuccess' => $this->getStoreInVaultOnSuccess(),
    //         'storeShippingAddressInVault' => $this->getStoreShippingAddressInVault(),
    //         'verifyCard' => $this->getVerifyCard(),
    //         'verificationMerchantAccountId' => $this->getVerificationMerchantAccountId(),
    //     ];

    //     // Remove null values
    //     $data = array_filter($data, function ($value) {
    //         return ! is_null($value);
    //     });

    //     if (empty($data)) {
    //         return $data;
    //     } else {
    //         return ['options' => $data];
    //     }
    // }

    /**
     * @return array
     */
    // public function getLineItems()
    // {
    //     $line_items = array();

    //     if (!$items = $this->getItems()) {
    //         return $line_items;
    //     }

    //     foreach ($items as $item) {

    //         $item_kind = ($item->getPrice() >= 0.00)
    //             ? 'debit'
    //             : 'credit';

    //         $unit_amount = ($item->getQuantity() > 0)
    //             ? $item->getPrice() / $item->getQuantity()
    //             : $item->getPrice();

    //         array_push($line_items, array(
    //             'name' => $item->getName(),
    //             'description' => $item->getDescription(),
    //             'totalAmount' => abs($item->getPrice()),
    //             'unitAmount' => abs($unit_amount),
    //             'kind' => $item_kind,
    //             'quantity' => $item->getQuantity(),
    //         ));
    //     }

    //     return $line_items;
    // }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
