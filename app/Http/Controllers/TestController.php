<?php

namespace App\Http\Controllers;

use App\Mail\RegisterVerifyMail;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        if (session()->get('xendit_response')) {

            $r = session()->get('xendit_response');
            dd($r);
            return;
        }
        $order = Order::with('product', 'user')
            ->where('order_code', 'AGlBRDnfqZ4md0j6Va')
            ->first();
        $payment = PaymentService::make($order);

        $e = $payment->createNewInvoice();

        session()->put('xendit_response', $e);
        //echo bcrypt('password');

    }
}
