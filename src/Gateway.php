<?php

namespace Omnipay\Alma;

use Omnipay\Common\AbstractGateway;
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
            'key' => '',
            'testMode' => false,
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

    public function eligibility(array $parameters = array())
    {
        $alma = new Alma\API\Client($this->getApiKey(), ['mode' => $this->getTestMode() ? Alma\API\TEST_MODE : Alma\API\LIVE_MODE]);

        return $alma->payments->eligibility(['payment' => ['purchase_amount' => round($parameters['amount']*100)]]);
    }

    public function isEligible(array $parameters = array())
    {
        $eligibility = $this->eligibility($parameters);

        return $eligibility->isEligible();
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Alma\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        $parameters['transaction_reference'] = request('pid');
        return $this->createRequest('\Omnipay\Alma\Message\CompletePurchaseRequest', $parameters);
    }
}
