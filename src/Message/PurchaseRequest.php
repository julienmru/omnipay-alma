<?php

namespace Omnipay\Alma\Message;

use Omnipay\Alma\Message\AbstractRequest;

class PurchaseRequest extends AbstractRequest
{

    public function sendData($data)
    {
        $this->validate('amount', 'card');

        $this->response = new PurchaseResponse($this, $data);

        return $this->response;
    }

}