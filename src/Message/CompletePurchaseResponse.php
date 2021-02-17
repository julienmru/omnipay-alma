<?php


namespace Omnipay\Alma\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Alma\API\Entities\Payment as AlmaPayment;
use Alma\API\Entities\Instalment as AlmaInstalment;


class CompletePurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $successful = false;

    public function __construct(CompletePurchaseRequest $request, array $data)
    {
        parent::__construct($request, $data);

        $almaPaymentId = $request->getTransactionReference();

        $this->response = $this->request->getAlma()->payments->fetch($almaPaymentId);

        if (abs($this->response->purchase_amount - $this->request->getAmount()*100) > 2) {
            $reason = AlmaPayment::FRAUD_AMOUNT_MISMATCH;
            $reason .= " - " . $this->request->getAmount() . " * 100 vs " . $this->response->purchase_amount;
            try {
                $this->request->getAlma()->payments->flagAsPotentialFraud($almaPaymentId, $reason);
            } catch (RequestError $e) {
                \Log::warn("[Alma] Failed to notify Alma of amount mismatch");
            }
            throw new \Exception("FRAUD_AMOUNT_MISMATCH");
        }

        if ($this->request->getTransactionId() && $this->getTransactionId() != $this->request->getTransactionId()) {
            $reason = AlmaPayment::FRAUD_AMOUNT_MISMATCH;
            $reason .= " - " . $this->request->getTransactionId() . " * 100 vs " . $this->response->custom_data['transaction_id'];
            try {
                $this->request->getAlma()->payments->flagAsPotentialFraud($almaPaymentId, $reason);
            } catch (RequestError $e) {
                \Log::warn("[Alma] Failed to notify Alma of amount mismatch");
            }
            throw new \Exception("FRAUD_AMOUNT_MISMATCH");
        }

        if (!in_array($this->response->state, array(AlmaPayment::STATE_IN_PROGRESS, AlmaPayment::STATE_PAID))
            || $this->response->payment_plan[0]->state !== AlmaInstalment::STATE_PAID
        ) {
            $reason = AlmaPayment::FRAUD_STATE_ERROR;
            try {
                $this->request->getAlma()->payments->flagAsPotentialFraud($almaPaymentId, $reason);
            } catch (RequestError $e) {
                \Log::warn("[Alma] Failed to notify Alma of amount mismatch");
            }
            throw new \Exception("FRAUD_STATE_ERROR");
        }

        $this->successful = true;

    }

    public function getTransactionId() {
        return $this->response->custom_data['transaction_id'];
    }

    /**
     * @return bool
     */
    public function getRawResponse()
    {
        return ($this->response);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return ($this->successful);
    }


}