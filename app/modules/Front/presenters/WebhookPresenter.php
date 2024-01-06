<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\StripeOrdersRepository;
use App\Services\PaddleService;
use App\Services\StripeService;
use Exception;
use Nette\Utils\DateTime;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\Webhook;
use Tracy\Debugger;
use Tracy\ILogger;

final class WebhookPresenter extends BasePresenter
{
    public StripeService $stripeService;
    public PaddleService $paddleService;
    public StripeOrdersRepository $stripeOrdersRepository;

    public function __construct(
        StripeService $stripeService,
        PaddleService $paddleService,
        StripeOrdersRepository $stripeOrdersRepository
    ) {
        parent::__construct();
        $this->stripeService = $stripeService;
        $this->paddleService = $paddleService;
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
            Debugger::log($payload, 'stripe');
        } catch (Exception $exception) {
            Debugger::log($exception->getMessage(), ILogger::ERROR);
        }

        switch ($event->type) {
            case 'customer.created':
            case 'customer.updated':
                $session = $event->data->object;
                $customer = Customer::retrieve($session->id);
                $user = $this->userRepository->getUserByEmail($customer->email);
                if ($user) {
                    $user->update([
                        'stripe_customer_id' => $session->id,
                    ]);
                }
                break;
            case 'customer.deleted':
                $session = $event->data->object;
                $customer = Customer::retrieve($session->id);
                $user = $this->userRepository->getUserByStripeCustomerId($customer->id);
                if ($user) {
                    $user->update([
                        'stripe_customer_id' => null,
                        'tier' => 1,
                        'stripe_subscription_id' => null,
                    ]);
                }
                break;
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $session = $event->data->object;
                $subscription = Subscription::retrieve($session->id);
                $customerId = $subscription->customer;
                $user = $this->userRepository->getUserByStripeCustomerId($customerId);
                if ($user) {
                    $subItem = $subscription->items->first();
                    $user->update([
                        'stripe_subscription_id' => $session->id,
                        'tier' => $subItem->price->metadata->tier,
                        'stripe_subscription_start_date' => DateTime::from($subscription->start_date),
                        'stripe_subscription_end_date' => DateTime::from($subscription->current_period_end),
                    ]);
                }
                break;
            case 'customer.subscription.deleted':
                $subscription = Subscription::retrieve($event->data->object->id);
                break;
            case 'checkout.session.completed':
                $this->checkoutSessionCompleted($event);
                break;
            case 'checkout.session.async_payment_succeeded':
                $this->checkoutSessionCompleted($event);
                break;
            case 'checkout.session.async_payment_failed':
                $this->emailCustomer($event->data->object);
                break;
            case 'payment_intent.succeeded':
                $intent = PaymentIntent::retrieve($event->data->object->id, ['expand' => ['customer', 'payment_method']]);
                $customer = $intent->customer;
                if (!$customer->name) {
                    $paymentMethod = $intent->payment_method;
                    if ($paymentMethod->billing_details->name) {
                        $customer->update($customer->id, [
                            'name' => $paymentMethod->billing_details->name,
                        ]);
                    }
                }
                break;
            case 'invoice.paid':
            case 'invoice.payment_failed':
            default:
                Debugger::log('Received unknown event type: ' . $event->type, 'stripe-unknown');
                break;
        }

        http_response_code(200);
        die;
    }

    public function actionPaddle()
    {
        $paddleSignature = $this->getHttpRequest()->getHeader('Paddle-Signature');
        $payload = $this->getHttpRequest()->getRawBody();
    }

    private function checkoutSessionCompleted($event)
    {
        $session = Session::retrieve($event->data->object->id, ['expand' => ['line_items', 'customer', 'subscription']]);
        $email = $session->customer->email;
        $user = $this->userRepository->getUserByEmail($email);
        if (!$user) {
            Debugger::log('User not found: ' . $email, ILogger::ERROR);
            http_response_code(200);
            die;
        }
        $this->stripeOrdersRepository->saveOrder($session, $event->type);
        if ($session->payment_status === 'paid') {
            if ($session->mode === 'subscription') {
                $price = Price::retrieve($session->line_items->data[0]->price->id);
                $this->userRepository->updateUser($user->id, [
                    'tier' => $price->metadata->tier,
                    'stripe_subscription_id' => $session->subscription->id,
                    'stripe_customer_id' => $session->customer->id,
                ]);
            } else {
                $this->userRepository->updateUser($user->id, [
                    'stripe_customer_id' => $session->customer->id,
                ]);
            }
        }
    }

    private function emailCustomer($session)
    {
        // TODO: Email customer
    }
}
