<?php

namespace App\Services;
use App\Models\Order;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\CustomerObject;
use Xendit\Invoice\InvoiceItem;

class PaymentService
{
    private string $xenditApiKey;
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected Order $order
    )
    {
        $this->xenditApiKey = config('app.xendit_api_key');
    }

    public static function make(
        Order $order
    ): self
    {
        return new static($order);
    }

    public function createNewInvoice(): mixed
    {
        Configuration::setXenditKey($this->xenditApiKey);

        $customer = new CustomerObject([
            'email' => $this->order->user->email,
            'given_names' => $this->order->user->name,
            'mobile_number' => $this->order->user->phone
        ]);

        $items = new InvoiceItem([
            'name' => $this->order->product->name,
            'price' => $this->order->product->price,
            'quantity' => $this->order->quantity
        ]);

        $params = new CreateInvoiceRequest([
            'external_id' => $this->order->order_code,
            'description' => $this->order->product->name,
            'amount' => $this->order->grand_total,
            'payer_email' => $this->order->user->email,
            'items' => [$items],
            'customer' => $customer,
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1,
            'success_redirect_url' => route('order-form.success', ['code' => $this->order->order_code]),
            'failure_redirect_url' => route('order-form.detail', $this->order->order_code)
        ]);

        $apiInstance = new InvoiceApi();

        try {
            return $apiInstance->createInvoice($params);
        } catch (\Throwable $exception ) {
            return $exception;
        }
    }
}
