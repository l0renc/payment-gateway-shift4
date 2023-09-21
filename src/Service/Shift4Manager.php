<?php

namespace App\Service;

use Shift4\Response\Charge;
use Shift4\Response\ListResponse;
use Shift4\Shift4Gateway;

class Shift4Manager
{
    protected Shift4Gateway $gateway;

    const PRIVATE_KEY = 'sk_test_z7bbbOSuhGV22hUzCoBdfviT';

    public function init()
    {
        $privateKey = getenv('SHIFT4_PRIVATE_KEY');

        $this->gateway = new Shift4Gateway($privateKey);
        return $this->gateway;
    }

    public function processPayment($request)
    {
        $gateway = new Shift4Gateway(self::PRIVATE_KEY);
        $chargeRequest = $this->getFormattedRequest($request);

        /** @var Charge $response */
        $response = $gateway->createCharge($chargeRequest);

        return $response;
    }

    public function processRefund($request)
    {
        $gateway = new Shift4Gateway(self::PRIVATE_KEY);
        $refundRequest = $this->getFormattedRequest($request);
        /** @var Charge $response */
        $response = $gateway->createRefund($refundRequest);

        return $response;
    }

    public function getPaymentsList()
    {
        $gateway = new Shift4Gateway(self::PRIVATE_KEY);
        /** @var ListResponse $response */
        $list = $gateway->listCharges()->getList();

        $charges = array_map(function($charge) {
            /** @var Charge $charge */
            return [
                'id' => $charge->getId(),
                'created' => $charge->getCreated(),
                'amount' => $charge->getAmount(),
                'status' => $charge->getStatus(),
                'description' => $charge->getDescription()
            ];
        }, $list);

        return $charges;
    }

    public function getFormattedRequest($request)
    {
        $amount = number_format($request['amount'], 2);
        $amount = $amount  * 100;

        $request['amount'] = $amount;

        return $request;
    }
}