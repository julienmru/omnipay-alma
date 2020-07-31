<?php

namespace Omnipay\Alma\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Alma;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{

    protected $alma = null;

    public function getAlma() {
        if (is_null($this->alma)) {
            $this->alma = new Alma\API\Client($this->getApiKey(), ['mode' => $this->getTestMode() ? Alma\API\TEST_MODE : Alma\API\LIVE_MODE]);
        }
        return $this->alma;
    }

    public function getData()
    {
        $this->validate();

        $data = [
                'payment' => 
                    [
                        'purchase_amount' => round($this->getAmount()*100),
                    ]
            ];

        if ($this->getCurrency() != 'EUR') {
            throw new \Exception('Gateway only supports EUR');
        }
        if ($id = $this->getTransactionId()) {
            $data['payment']['custom_data']['transaction_id'] = $id;
        }

        if ($card = $this->getCard()) {
            $data['customer']['first_name'] = $card->getFirstName();
            $data['customer']['last_name'] = $card->getLastName();
            $data['customer']['email'] = $card->getEmail();
            $data['customer']['phone'] = $card->getPhone();

            $data['payment']['shipping_address']['line1'] = $card->getShippingAddress1();
            $data['payment']['shipping_address']['line2'] = $card->getShippingAddress2();
            $data['payment']['shipping_address']['postal_code'] = $card->getShippingPostcode();
            $data['payment']['shipping_address']['city'] = $card->getShippingCity();
            $data['payment']['shipping_address']['phone'] = $card->getShippingPhone();

            $data['payment']['billing_address']['first_name'] = $card->getBillingFirstName();
            $data['payment']['billing_address']['last_name'] = $card->getBillingLastName();
            $data['payment']['billing_address']['line1'] = $card->getBillingAddress1();
            $data['payment']['billing_address']['line2'] = $card->getBillingAddress2();
            $data['payment']['billing_address']['postal_code'] = $card->getBillingPostcode();
            $data['payment']['billing_address']['city'] = $card->getBillingCity();
        }

        if ($url = $this->getCancelUrl()) {
            $data['payment']['customer_cancel_url'] = $url;
        }

        if ($url = $this->getReturnUrl()) {
            $data['payment']['return_url'] = $url;
        }

        if ($url = $this->getNotifyUrl()) {
            $data['payment']['ipn_callback_url'] = $url;
        }

        return $data;
    }

    public function getApiKey()
    {
        return $this->getParameter('apikey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apikey', $value);
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint().'?'.http_build_query($data, '', '&');
        $response = $this->httpClient->get($url);

        $data = json_decode($response->getBody(), true);

        return $this->createResponse($data);
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
