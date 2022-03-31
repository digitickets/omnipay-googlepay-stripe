<?php
namespace Digitickets\OmnipayGooglePayWithStripe\Message;

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
        // var_dump(3); die;
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
        $token = json_decode($this->getToken());

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

        $metadata = $this->getMetaData();
        if ($metadata && isset($metadata[0])) {
            $metadata = array_map('strval', $metadata[0]); // all metadata values must be strings
        }

        return [
            'source' => $token->id,
            'amount' => $this->getAmount() * 100, // we receive amount in cents
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'shipping' => $this->getShipping(),
            'metadata' => $metadata,
            // 'billing' => $this->getBilling(),
            // 'status' => $this->getStatus(),
        ];

        return $data;
    }
}
