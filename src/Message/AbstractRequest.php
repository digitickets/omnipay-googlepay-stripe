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

    /**
     * Config
     *
     * @return void
     */
    public function configure()
    {
        //empty for now
    }

    /**
     * Return the secret key
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->getParameter('secretKey');
    }

    /**
     * Return the payment token
     *
     * @return string
     */
    public function getSource(): string
    {
        $token = json_decode($this->getToken());

        return $token->id;

        /**
         * Token example
         * {
         *   "id": "tok_1KZLVHDtx4Fjr45S7Kd8DpLW",
         *   "object": "token",
         *   "card": {
         *     "id": "card_1KZLVHDtx4Fjr45SdaeeTPTc",
         *     "object": "card",
         *     "address_city": null,
         *     "address_country": null,
         *     "address_line1": null,
         *     "address_line1_check": null,
         *     "address_line2": null,
         *     "address_state": null,
         *     "address_zip": null,
         *     "address_zip_check": null,
         *     "brand": "Visa",
         *     "country": "US",
         *     "cvc_check": null,
         *     "dynamic_last4": "4242",
         *     "exp_month": 12,
         *     "exp_year": 2024,
         *     "funding": "credit",
         *     "last4": "1111",
         *     "metadata": {
         *     }
         *   }
         * }
         */
    }

    /**
     * Return amount (in cents)
     *
     * @return float
     */
    public function getAmount(): float
    {
        return 100 // convert to cents
            * floatval($this->getParameter('amount'));
    }

    /**
     * Return currency code
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getParameter('currency');
    }

    /**
     * Return description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getParameter('description');
    }

    /**
     * Return shipping details
     *
     * @return array
     */
    public function getShipping(): array
    {
        $card = $this->getCard();

        return [
            'address' => [
                'city' => $card->getShippingCity(),
                'country' => $card->getShippingCountry(),
                'line1' => $card->getShippingAddress1(),
                'line2' => $card->getShippingAddress2(),
                'postal_code' => $card->getShippingPostcode(),
                'state' => $card->getShippingState(),
            ],
            'name' => $card->getShippingFirstName() . ' ' . $card->getShippingLastName(),
            'phone' => $card->getShippingPhone(),
        ];
    }

    /**
     * Return billing details
     *
     * @return array
     */
    public function getBilling(): array
    {
        $card = $this->getCard();

        return [
            'address' => [
                'city' => $card->getBillingCity(),
                'country' => $card->getBillingCountry(),
                'line1' => $card->getBillingAddress1(),
                'line2' => $card->getBillingAddress2(),
                'postal_code' => $card->getBillingPostcode(),
                'state' => $card->getBillingState(),
            ],
            'email' => $card->getEmail(),
            'name' => $card->getBillingFirstName() . ' ' . $card->getBillingLastName(),
            'phone' => $card->getBillingPhone(),
        ];
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return 'succeeded'; //'pending', 'failed'
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetaData(): array
    {
        $meta = [];
        $count = 0;

        // all metadata values must be strings
        foreach ($this->getItems() as $item) {
            $key = 'item' . ++$count;
            $meta[$key . '_name'] = strval($item->getName());
            $meta[$key . '_description'] = strval($item->getDescription());
            $meta[$key . '_price'] = strval($item->getPrice());
            $meta[$key . '_quantity'] = strval($item->getQuantity());
        }

        return $meta;
    }

    /**
     * Get Form
     */
    public function getForm()
    {
        return $this->getParameter('form');
    }

    /**
     * set Form
     *
     * @param [type] $value
     * @return void
     */
    public function setForm($value)
    {
        return $this->setParameter('form', $value);
    }

    /**
     * Return items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getParameter('items');
    }

    /**
     * Set items
     *
     * @param array $value
     * @return array
     */
    public function setItems($value)
    {
        return $this->setParameter('items', $value);
    }

    /**
     * Creates a response
     *
     * @param [type] $data
     * @return Response
     */
    protected function createResponse($data): Response
    {
        return $this->response = new Response($this, $data);
    }
}
