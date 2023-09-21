<?php

namespace App\Service;

use Shift4\Response\Charge;
use Shift4\Response\Customer;
use Shift4\Response\ListResponse;
use Shift4\Shift4Gateway;

class Shift4Manager
{
    protected Shift4Gateway $gateway;

    const PRIVATE_KEY = 'sk_test_z7bbbOSuhGV22hUzCoBdfviT';

    public function init()
    {
        $this->gateway = new Shift4Gateway(self::PRIVATE_KEY);
    }

    public function processPayment($request)
    {
        $chargeRequest = $this->getFormattedRequest($request);

        /** @var Charge $response */
        $response = $this->gateway->createCharge($chargeRequest);

        return $response;
    }

    public function processRefund($request)
    {
        $refundRequest = $this->getFormattedRequest($request);
        /** @var Charge $response */
        $response = $this->gateway->createRefund($refundRequest);

        return $response;
    }

    public function getPaymentsList()
    {
        /** @var ListResponse $response */
        $list = $this->gateway->listCharges()->getList();

        $charges = array_map(function($charge) {
            /** @var Charge $charge */
            return [
                'id' => $charge->getId(),
                'created' => $charge->getCreated(),
                'amount' => $charge->getAmount(),
                'status' => $charge->getStatus(),
                'description' => $charge->getDescription(),
                'currency' =>  $charge->getCurrency()
            ];
        }, $list);

        return $charges;
    }

    public function getCustomerList()
    {
        /** @var ListResponse $response */
        $list = $this->gateway->listCustomers()->getList();

        $customers = array_map(function($customer) {
            /** @var Customer $customer */
            return [
                'id' => $customer->getId(),
                'created' => $customer->getCreated(),
                'email' => $customer->getEmail(),
            ];
        }, $list);

        return $customers;
    }

    public function getFormattedRequest($request)
    {
        $amount = number_format($request['amount'], 2);
        $amount = $amount  * 100;

        $request['amount'] = $amount;

        return $request;
    }
}