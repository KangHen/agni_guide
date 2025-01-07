<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {

    public Order|null $order;
    public array $atms = [
        'Masukkan kartu ATM dan PIN',
        'Pilih menu Transfer',
        'Masukkan rekening tujuan',
        'Masukkan nominal yang akan dibayarkan',
        'Konfirmasi data transaksi',
        'Transaksi berhasil'
    ];

    public function mount(string $code): void
    {
        $this->order = Order::with('user', 'product')
            ->where('order_code', $code)
            ->first();
    }
}; ?>

<div>
    <x-loading/>
    <div class="bg-white shadow-md overflow-hidden sm:rounded-lg  mx-auto">
        <div class="bg-white">
            <div class="p-6 text-gray-900">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                    <div>

                    </div>
                    <div>
                        <h1 class="mb-0 font-bold text-2xl">Cara Pembayaran</h1>
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
                            <ul class="flex flex-wrap -mb-px" id="transfer">
                                <li class="me-2">
                                    <a href="#atm" class="inline-block p-4 text-orange-400 border-b-2 border-orange-400 rounded-t-lg active dark:text-orange-400 dark:border-orange-400" aria-current="page">ATM Bersama</a>
                                </li>
                                <li class="me-2">
                                    <a href="#m-banking" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">M-Banking</a>
                                </li>
                            </ul>
                        </div>

                        <div class="px-8 py-4 bg-slate-50">
                            <ul class="list-decimal" id="atm">
                                @foreach($atms as $atm)
                                    <li class="pb-3">{{ $atm }}</li>
                                @endforeach
                            </ul>

                            <ul class="list-decimal hidden" id="m-banking">
                                <li>kkkk</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const tabs = $wire.$el.querySelectorAll('#transfer a');
        tabs.forEach(tab => {
           tab.addEventListener('click', (e) => {
               e.preventDefault();

               hideTab();

               const href = tab.getAttribute('href');
               const target = $wire.$el.querySelector(href);

                const tabActive = sessionStorage.getItem('tab-active');
                if (!tabActive) {
                    sessionStorage.setItem('tab-active', 'atm');
                }

               sessionStorage.setItem('tab-active', href.replace('#', ''));
               target.classList.remove('hidden');
           });
        });

        const hideTab = () => {
            tabs.forEach(tab => {
                const href = tab.getAttribute('href');
                const target = $wire.$el.querySelector(href);
                target.classList.add('hidden');
            });
        }
    </script>
    @endscript
</div>
