<?php


namespace Omnipay\Alma\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class EligibilityResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(EligibilityRequest $request, array $data)
    {
        parent::__construct($request, $data);
        $this->response = $this->request->getAlma()->payments->eligibility($data);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->response->isEligible;
    }

}