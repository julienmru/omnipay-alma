<?php


namespace Omnipay\Alma\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(PurchaseRequest $request, array $data)
    {
        parent::__construct($request, $data);
        $this->response = $this->request->getAlma()->payments->createPayment($data);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isTransparentRedirect()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->response->url;
    }

    public function getRedirectData()
    {
        return null;
    }
}