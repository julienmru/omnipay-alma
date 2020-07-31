<?php

namespace Omnipay\Alma\Message;


use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\Request;
use Omnipay\Alma\Gateway as AlmaGateway;
use Alma;
use Alma\API\Entities\Payment as AlmaPayment;
use Alma\API\Entities\Instalment as AlmaInstalment;

class Notification implements NotificationInterface
{
    protected $data;
    protected $gateway;

    public function __construct(AlmaGateway $gateway) {
        $this->gateway = $gateway;
    }

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference() {
        return request('pid');
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@link NotificationInterface::STATUS_COMPLETED},
     * {@link NotificationInterface::STATUS_PENDING}, or {@link NotificationInterface::STATUS_FAILED}.
     */
    public function getTransactionStatus() {
        $response = $this->getData();
        if (in_array($response->state, array(AlmaPayment::STATE_IN_PROGRESS, AlmaPayment::STATE_PAID))
            && $response->payment_plan[0]->state == AlmaInstalment::STATE_PAID
        ) {
            return NotificationInterface::STATUS_COMPLETED;
        } elseif (in_array($response->state, array(AlmaPayment::STATE_IN_PROGRESS))
            && $response->payment_plan[0]->state == AlmaInstalment::STATE_IN_PROGRESS
        ) {
            return NotificationInterface::STATUS_PENDING;
        } else {
            return NotificationInterface::STATUS_FAILED;
        }
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage() {
        return $this->getData()->state;
    }


    public function getData() {
        if (!$this->data) {
            $alma = new Alma\API\Client($this->gateway->getApiKey(), ['mode' => $this->gateway->getTestMode() ? Alma\API\TEST_MODE : Alma\API\LIVE_MODE]);
            $this->data = $alma->payments->fetch($this->getTransactionReference());
        }
        return $this->data;
    }

}