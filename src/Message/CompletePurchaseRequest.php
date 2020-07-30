<?php

namespace Omnipay\Alma\Message;

use Omnipay\Alma\Message\AbstractRequest;

class CompletePurchaseRequest extends AbstractRequest
{

    public function getData()
    {
        $this->validate('amount');

        return ['transaction_reference' => $this->getTransactionReference()];
    }

    public function sendData($data)
    {
        $this->response = new CompletePurchaseResponse($this, $data);

        return $this->response;
    }

}