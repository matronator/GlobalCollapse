<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\StripeOrdersRepository;
use App\Services\StripeService;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Tracy\Debugger;
use Tracy\ILogger;

final class WebhookPresenter extends BasePresenter
{
    public StripeService $stripeService;
    public StripeOrdersRepository $stripeOrdersRepository;

    public function __construct(
        StripeService $stripeService,
        StripeOrdersRepository $stripeOrdersRepository
    ) {
        parent::__construct();
        $this->stripeService = $stripeService;
        $this->stripeOrdersRepository = $stripeOrdersRepository;
    }

    protected function startup()
    {
        parent::startup();
        $this->setLayout(false);
    }

    public function actionDefault()
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->stripeService->webhookSecret);
            Debugger::log($payload);
        } catch (Exception $exception) {
            Debugger::log($exception->getMessage(), ILogger::ERROR);
        }

        switch ($event->type) {
            case 'customer.created':
                $session = $event->data->object;
                $this->userRepository->updateUser($session->metadata->user_id, [
                    'stripe_customer_id' => $session->id,
                ]);
            case 'checkout.session.completed':
                $session = $event->data->object;
                if ($session->payment_status === 'paid') {
                    $this->stripeOrdersRepository->fulfillOrder($session);
                } else {
                    $this->stripeOrdersRepository->createOrder($session);
                }
                break;
            case 'checkout.session.async_payment_succeeded':
                $this->stripeOrdersRepository->fulfillOrder($event->data->object);
                break;
            case 'checkout.session.async_payment_failed':
                $this->emailCustomer($event->data->object);
                break;
            case 'invoice.paid':
            case 'invoice.payment_failed':
            default:
                Debugger::log('Received unknown event type: ' . $event->type, ILogger::WARNING);
                $this->terminate();
        }
    }

    private function checkoutSessionCompleted($event)
    {
        $session = Session::retrieve([
            'id' => $event->data->object->id,
            'expand' => ['line_items'],
        ]);

        $lineItems = $session->line_items;

        Debugger::log('Order fulfilled');
        Debugger::log($lineItems);
    }

    private function emailCustomer($session)
    {
        // TODO: Email customer
    }
}
