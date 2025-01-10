<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {
    public string|null $message = '';
    public Order|null $order;
    public object|null $payment;
    public bool $wasPaid = false;

    public function mount(string|null $code): void {
        if (!$code) {
            $this->redirect('/', navigate: true);
        }

        $this->order = Order::query()
            ->where('order_code', $code)
            ->first();
    }

    public function pay(): void
    {
        $payment = json_decode($this->order->payment_data);
        redirect()->away($payment['invoice_url']);
    }
}; ?>

<div>
    @if($order && $order->upload_image && $order->payment_method == 'NOT_XENDIT')
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg mx-auto p-6">
            <div class="text-green-500 flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                </svg>
            </div>
            <div class="text-center mt-3">
                <h3 class="text-lg font-bold">Pembayaran di Proses</h3>
                <p>Pembayaran telah di proses, mohon tunggu konfirmasi admin!</p>
            </div>
        </div>
    @endif

        @if($order && $order->status <> 'done' && $order->payment_method == 'XENDIT')
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg mx-auto p-6">
                <div class="text-green-500 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </div>
                <div class="text-center mt-3">
                    <h3 class="text-lg font-bold">Menunggu Proses Pembayaran / Konfirmasi Pembayaran</h3>
                    <p class="mb-3">Mohon tunggu sampai proses pembayaran selesai.</p>
                </div>
            </div>
        @endif

        @if($order && $order->status == 'done')
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg mx-auto p-6">
                <div class="text-green-500 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </div>
                <div class="text-center mt-3">
                    <h3 class="text-lg font-bold">Pembayaran Berhasil</h3>
                    <p class="mb-3">Terimakasih telah melakukan pembayaran.</p>
                </div>
            </div>
        @endif
</div>
