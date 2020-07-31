<?php

namespace Omnipay\Alma\Message;

use Omnipay\Alma\Message\AbstractRequest;

class EligibilityRequest extends AbstractRequest
{

    public function sendData($data)
    {
        $this->validate('amount');

        $this->response = new EligibilityResponse($this, $data);

        return $this->response;
    }

}