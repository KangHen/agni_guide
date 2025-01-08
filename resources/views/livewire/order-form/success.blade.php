<?php

use Livewire\Volt\Component;

new class extends Component {
    public string|null $message = '';

    public function mount(): void {
        $this->message = session()->get('message') ?? null;

        if (!$this->message) {
            $this->redirect('/', navigate: true);
        }
    }
}; ?>

<div>
    @if($message)
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg mx-auto p-6">
            <div class="text-green-500 flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                </svg>
            </div>
            <div class="text-center mt-3">
                <h3 class="text-lg font-bold">{{ $message }}</h3>
                <p>Pembayaran telah di proses, mohon tunggu konfirmasi admin!</p>
            </div>
        </div>
    @endif
</div>
