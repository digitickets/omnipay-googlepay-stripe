<?php
namespace DigiTickets\OmnipayGooglePayWithStripe\Message;

use Omnipay\Common\Message\AbstractResponse;

// https://github.com/thephpleague/omnipay-stripe/blob/master/src/Message/Response.php

/**
 * Response
 */
class Response extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return !isset($this->data['error']);
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful() && isset($this->data['error']) && isset($this->data['error']['message'])) {
            return $this->data['error']['message'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if (!$this->isSuccessful() && isset($this->data['error']) && isset($this->data['error']['code'])) {
            return $this->data['error']['code'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        if (isset($this->data['object']) && 'charge' === $this->data['object']) {
            return $this->data['id'];
        }

        return null;
    }
}
