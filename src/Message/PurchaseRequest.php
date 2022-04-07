<?php
namespace DigiTickets\OmnipayGooglePayWithStripe\Message;

class PurchaseRequest extends AbstractRequest
{
    /**
     * Send the request with specified data.
     *
     * @param mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $response = $this->stripe->charges->create($data);

        return $this->createResponse($response);
    }

    /**
     * Gets the data to send
     *
     * @return array
     */
    public function getData()
    {
        // for some reason Stripe complains about the billing_details and status objects
        // although it exists in the documentation https://stripe.com/docs/api/charges/object

        return [
            'source' => $this->getSource(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'shipping' => $this->getShipping(),
            'metadata' => $this->getMetaData(),
            // 'billing_details' => $this->getBilling(),
            // 'status' => $this->getStatus(),
        ];

        return $data;
    }
}
