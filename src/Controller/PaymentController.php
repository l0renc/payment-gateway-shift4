<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Shift4Manager;

class PaymentController extends AbstractController
{

    #[Route('/payments', name : 'process_payment_form', methods: ['GET'])]
    public function processPaymentForm()
    {
        return $this->render('payment-form.html.twig');
    }

    #[Route('/checkout/payments', name : 'checkout_process_payment_form', methods: ['GET'])]
    public function checkoutProcessPaymentForm()
    {
        return $this->render('shift4-payment-form.html.twig');
    }

    #[Route('/payments', name : 'process_payment', methods: ['POST'])]
    public function processPaymentAction(Request $request, Shift4Manager $shift4Manager)
    {
        $data = $request->request->all();
        $shift4Manager->init();
        $response = $shift4Manager->processPayment($data);

        return $this->json($response, Response::HTTP_CREATED);

    }

    #[Route('/payments/refund/{chargeId}', name : 'process_refund', methods: ['POST'])]
    public function processRefundAction($chargeId, Request $request, Shift4Manager $shift4Manager)
    {
        $refundRequest = ['chargeId' => $chargeId, 'amount' => $request->get('amount')];
        $shift4Manager->init();
        $response = $shift4Manager->processRefund($refundRequest);

        return $this->json($response, Response::HTTP_CREATED);

    }

    #[Route('/payment-handler', name : 'payment_handler', methods: ['POST'])]
    public function payemntHandlerAction(Request $request)
    {
        return $this->json($request->request->all(), Response::HTTP_CREATED);

    }

    #[Route('/payments/list', name : 'payments_list', methods: ['GET'])]
    public function paymentsListAction(Shift4Manager $shift4Manager)
    {
        $shift4Manager->init();
        $charges = $shift4Manager->getPaymentsList();

        return $this->render('payments-list.html.twig', [
            'charges' => $charges,
        ]);
    }

    #[Route('/customers/list', name : 'customers_list', methods: ['GET'])]
    public function customersListAction(Shift4Manager $shift4Manager)
    {
        $shift4Manager->init();
        $customers = $shift4Manager->getCustomersList();

        return $this->render('customers-list.html.twig', [
            'customers' => $customers,
        ]);
    }

}