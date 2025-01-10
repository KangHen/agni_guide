<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Services\PaymentService;
use Xendit\Invoice\Invoice;

new class extends Component {
    use WithFileUploads;

    public string|null $orderCode;
    public Order|null $order;
    public object|null $payment;
    public array $atms = [
        'Masukkan kartu ATM dan PIN',
        'Pilih menu Transfer',
        'Masukkan rekening tujuan',
        'Masukkan nominal yang akan dibayarkan',
        'Konfirmasi data transaksi',
        'Transaksi berhasil'
    ];
    public array $mbanks = [
        'Buka Aplikasi M-Banking Anda',
        'Pilih menu Transfer atau Kirim Uang pada layar utama aplikasi',
        'Pilih Jenis Transfer Internal atau External',
        'Masukkan rekening tujuan',
        'Masukkan nominal yang akan dibayarkan',
        'Pilih Metode Transfer Real Time Online atau SKN / RTGS',
        'Konfirmasi data transaksi',
        'Transaksi berhasil'
    ];

    public $file;

    public function mount(string $code): void
    {
        if (!$code) {
            $this->redirect('/', navigate: true);
        }

        $this->orderCode = $code;
        $this->order = Order::with('user', 'product')
            ->where('order_code', $code)
            ->first();

        if (!$this->order || !$this->order?->product) {
            $this->redirect('/', navigate: true);
        }

        $this->payment = $this->order->payment_data ? json_decode($this->order->payment_data) : null;
    }

    public  function saved(): void
    {
        $this->validate([
            'file' => 'required|file|max:2048|mimes:jpg,png'
        ],
        [
            'file.required' => 'File upload wajib isi',
            'file.mimes' => 'Extensi JPG atau PNG',
            'file.max' => 'Ukuran maksimal 2Mb'
        ]);

        $path = public_path('uploads/confirms');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $order = Order::query()
            ->where('order_code', $this->orderCode)
            ->first();

        if (!$order) {
            $this->redirect('/', navigate: true);
        }

        $fileExtension = $this->file->getClientOriginalExtension();
        $fileRandom = rand(111111,999999) . $this->file->getClientOriginalName();
        $fileName = md5($fileRandom) . '.' . $fileExtension;

        $this->file->storeAs('confirms', $fileName, 'upload_public_path');
        $staticUploadPath = public_path('uploads/confirms/' . $fileName);

        $order->upload_image = $fileName;
        $order->status = 'process';
        $order->payment_method = 'NON_XENDIT';

        if ($order->save()) {
            Image::read($staticUploadPath)
                ->scale(width: 600);

            $this->redirect('/order-form-success?code=' . $this->orderCode, navigate: true);
        } else {
            $this->reset('file');
            session()->flash('error', 'Upload Gagal!');
        }
    }

    public function pay(): void
    {
        $order = Order::with('product', 'user')
            ->where('order_code', $this->orderCode)
            ->first();

        $this->payment = $order->payment_data ? json_decode($order->payment_data) : null;

        if ($order->payment_data && $this->payment->invoice_url) {
            redirect()->away($this->payment->invoice_url);
            return;
        }

        if (!$order->product || !$order->user) {
            session()->flash('error', 'Terjadi Kesalahan!');
            $this->redirect('/order-form-detail/' . $this->orderCode, navigate: true);
        }

        $payment = PaymentService::make($order)->createNewInvoice();

        if ($payment instanceof Invoice) {
            $order->payment_method = 'XENDIT';
            $order->payment_data = json_encode([
                'invoice_url' => $payment['invoice_url'],
                'invoice_id' => $payment['id'],
                'expired' => $payment['expiry_date'],
                'created' => $payment['created_at'],
                'currency' => $payment['currency']
            ]);

            if ($order->save()) {
                redirect()->away($payment['invoice_url']);

                return;
            }

            session()->flash('error', 'Terjadi Kesahalan!');
            $this->redirect('/order-form-detail/' . $this->orderCode, navigate: true);

            return;
        }

        session()->flash('error', $payment->getMessage());
        $this->redirect('/order-form-detail/' . $this->orderCode, navigate: true);
    }
}; ?>

<div>
    <x-loading/>
    <x-alert-message/>
    <x-application-logo class="w-36 mb-3 mx-auto" />
    <div class="flex justify-end mb-3">
        <x-secondary-button data-url="{{ route('order-form.detail', $orderCode) }}" class="copy-confirm-page">
            <span class="me-2">{{ __('Simpan Halaman Ini') }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
            </svg>
        </x-secondary-button>
    </div>
    <div class="bg-white shadow-md overflow-hidden sm:rounded-lg  mx-auto mb-3">
        <div class="bg-white">
            <div class="p-6 text-gray-900">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                    <div>
                        <h1 class="mb-0 font-bold text-2xl">Order <span class="text-orange-400 text-sm">#{{ $orderCode }}</span></h1>
                        <h3 class="mb-0 font-semibold text-xl mb-3">
                            {{ $order->product->name }} x {{ $order->quantity }}
                        </h3>

                        <div class="mb-3">
                            <x-input-label :value="__('Nama Peserta')"/>
                            <p class="font-semibold">{{ $order->user->name }}</p>
                        </div>
                        <div class="mb-3">
                            <x-input-label :value="__('No Telpon')"/>
                            <p class="font-semibold">{{ $order->user->phone }}</p>
                        </div>
                        <div class="mb-6">
                            <x-input-label :value="__('Alamat')"/>
                            <p class="font-semibold">{{ $order->user->address }}</p>
                        </div>
                    </div>
                    <div>
                        <h1 class="mb-0 font-bold text-2xl">Cara Pembayaran</h1>
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                            <ul class="flex flex-wrap -mb-px" id="transfer">
                                <li class="me-2">
                                    <a href="#atm" class="inline-block p-4 text-orange-400 rounded-t-lg border-b-2 border-orange-400" aria-current="page">ATM Bersama</a>
                                </li>
                                <li class="me-2">
                                    <a href="#m-banking" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">M-Banking</a>
                                </li>
                            </ul>
                        </div>

                        <div class="px-8 py-4 bg-slate-50 mb-3">
                            <ul class="list-decimal" id="atm">
                                @foreach($atms as $atm)
                                    <li class="pb-3">{{ $atm }}</li>
                                @endforeach
                            </ul>

                            <ul class="list-decimal hidden" id="m-banking">
                                @foreach($mbanks as $bank)
                                    <li class="pb-3">{{ $bank }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-lg flex justify-between mb-3">
                            <div><h1 class="mb-0 font-extrabold text-3xl text-slate-800">Rp {{ number_format($order->grand_total) }}</h1></div>
                            <div>
                                <x-secondary-button data-nominal="{{ $order->grand_total }}" class="copy-nominal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
                                    </svg>
                                </x-secondary-button>
                            </div>
                        </div>
                        @if (config('app.xendit_payment'))
                        <x-primary-button class="w-full py-4 px-4" wire:click="pay()">
                            <span class="text-center w-full">Bayar Sekarang (Otomatis Konfirmasi)</span>
                        </x-primary-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md overflow-hidden sm:rounded-lg mx-auto p-6">
        <div class="mb-3">
            <h1 class="mb-0 font-bold text-2xl">Manual Konfirmasi</h1>
            <p class="mb-3">Upload Bukti Pembayaran</p>
            <x-input-label :value="__('File')"/>
            <x-text-input accept=".jpg, .jpeg, .png" wire:model="file" id="file" name="file" type="file" class="mt-1 block w-full border p-1" />
            <x-input-error :messages="$errors->get('file')" class="mt-2"/>
        </div>
        <div>
            <x-primary-button wire:click="saved()">
                {{ __('Upload') }}
            </x-primary-button>
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
               target.classList.remove('hidden');

               tab.classList.add('border-orange-400');
               tab.classList.add('text-orange-400');
               tab.classList.add('border-b-2');
           });
        });

        const hideTab = () => {
            tabs.forEach(tab => {
                const href = tab.getAttribute('href');
                const target = $wire.$el.querySelector(href);
                target.classList.add('hidden');
                tab.classList.remove('border-orange-400');
                tab.classList.remove('text-orange-400');
                tab.classList.remove('border-b-2');
                tab.classList.remove('border-transparent');
            });
        }

        const copyConfirmPage = document.querySelector('.copy-confirm-page');
        copyConfirmPage.addEventListener('click', function (e) {
            e.preventDefault();
            const URL = this.getAttribute('data-url');
            navigator.clipboard.writeText(URL).then(() => {
                const notify = new Notyf();
                notify.success('Halaman berhasil disalin');
            }).catch(err => {
                console.error('Error copying text: ', err);
            });
        });

        const copyNominal = document.querySelector('.copy-nominal');
        copyNominal.addEventListener('click', function (e) {
            e.preventDefault();
            const nominal = this.getAttribute('data-nominal');
            navigator.clipboard.writeText(nominal).then(() => {
                const notify = new Notyf();
                notify.success('Nominal berhasil disalin');
            }).catch(err => {
                console.error('Error copying text: ', err);
            });
        });
    </script>
    @endscript
</div>
