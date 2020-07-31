<?php

namespace Omnipay\Alma;

use Omnipay\Common\AbstractGateway;
use Omnipay\Alma\Message\Notification;
use Alma;

/**
 * Alma Gateway
 */
class Gateway extends AbstractGateway
{

    public function getName()
    {
        return 'Alma';
    }

    public function getDefaultParameters()
    {
        return array(
            'apikey' => config('alma.credentials.key'),
            'testMode' => config('alma.credentials.sandbox'),
        );
    }

    public function getApiKey()
    {
        return $this->getParameter('apikey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apikey', $value);
    }

    public function acceptNotification()
    {
        return new Notification($this);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Alma\Message\PurchaseRequest', $parameters);
    }
    
    public function eligibility(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Alma\Message\EligibilityRequest', $parameters);
    }
}
