<?php

namespace Digitickets\OmnipayGooglePayWithStripe;

use Stripe\StripeClient as Stripe;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

class Gateway extends AbstractGateway
{
    /**
     * @var Stripe
     */
    protected $stripe;

    /**
     * Create a new gateway instance.
     *
     * @param ClientInterface  $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest      $httpRequest A Symfony HTTP request object
     * @param Stripe           $stripe    The Stripe client
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct($httpClient, $httpRequest);
    }

    /**
     * Makes sure there is a valid Stripe client
     *
     * @return void
     */
    protected function assertStripeClient()
    {
        if (is_null($this->stripe)) {
            $this->stripe = new Stripe($this->getSecretKey());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequest($class, array $parameters)
    {
        $this->assertStripeClient();

        $obj = new $class($this->httpClient, $this->httpRequest, $this->stripe);

        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }

    /**
     * Get gateway name
     *
     * @return string
     */
    public function getName() : string
    {
        \Log::info('getName');
        return 'GooglePay with Stripe';
    }

    /**
     * Get gateway default parameters
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'testMode' => false,
            'secretKey' => '',
            'form' => [],
        ];
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * purchase function to be called to initiate a purchase
     *
     * @param  array $parameters
     * @return RequestInterface
     */
    public function purchase(array $parameters = []): RequestInterface
    {
        // dd($parameters);

        return $this->createRequest('\Digitickets\OmnipayGooglePayWithStripe\Message\PurchaseRequest', $parameters);
    }
}
