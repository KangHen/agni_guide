<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookXenditController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): void
    {
        $calbackToken = config('app.xendit_token_callback');
        $token = $request->header('x-callback-token');

        if ($token !== $calbackToken) {
            Log::error('Invalid callback token');

            return;
        }

        $payload = $request->all();
        $externalId = $payload['external_id'];

        $order = Order::query()
            ->where('order_code', $externalId)
            ->first();

        if (!$order) {
            Log::error('Order not found');

            return;
        }

        $payload['invoice_url'] = $order->payment_data ? json_decode($order->payment_data)->invoice_url : null;
        $order->status = 'done';
        $order->payment_data  = json_encode($payload);

        $order->save();
    }
}
