<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new class extends Component {
    use WithPagination;

    public int $id = 0;
    public int $productId = 0;
    public string|null $search = null;
    public int $no = 1;
    public array $allStatuses = [
        'semua', 'pending', 'process', 'done', 'cancel'
    ];
    public string $filter_status = 'semua';
    public int $processCount = 0;
    public string|null $image;

    public function mount(int $id = 0): void {
        if (!$id) {
            $this->redirect('/order');
        }

        $this->productId = $id;
        $this->processCount = Order::query()
            ->where('product_id', $this->productId)
            ->where('status', 'process')
            ->count();
    }

    public function with(): array {
        return [
            'items' => Order::with('user')
                ->where('product_id', $this->productId)
                ->whereHas('user')
                ->when($this->search, fn($query, $search) => $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$search.'%')))
                ->when($this->filter_status <> 'semua', fn($query) => $query->where('status', $this->filter_status))
                ->paginate(10)
        ];
    }

    /**
     * showOrderInProcess
     * @return void
     */
    public function showOrderInProcess(): void {
        $this->filter_status = 'process';
        $this->resetPage();
    }

    /**
     * @param string $image
     * @return void
     */
    public function showConfirmation(string $image, int $id): void
    {
        $this->id = 0;
        $path = public_path('uploads/confirms/' . $image);

        if (file_exists($path)) {
            $this->id = $id;
            $this->image = url('uploads/confirms/' . $image);
            $this->dispatch('open-modal', 'show-confirmation');
        }
    }

    /**
     * saved
     * @return void
     */
    public function saved(): void
    {
        if (!$this->id) {
            session()->flash('error', 'ID Tidak di Temukan!');
            $this->redirect('/order/product/' . $this->productId);
        }

        $order = Order::query()
            ->where('id', $this->id)
            ->update([
                'status' => 'done'
            ]);

        if ($order) {
            session()->flash('message', 'Konfirmasi Berhasil!');
        } else {
            session()->flash('error', 'Gagal Konfirmasi!');
        }

        $this->redirect('/order/product/' . $this->productId);
    }

    /**
     * filtered function
     * @return void
     */
    public  function filtered(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <x-loading />

    @if($processCount > 0)
        <div class="flex items-center p-4 mb-4 text-sm text-orange-800 rounded-lg bg-orange-50" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">Informasi</span> {{ $processCount }} Pembayaran perlu di lakukan konfirmasi
                <a wire:click="showOrderInProcess()" class="text-orange-400 font-bold cursor-pointer py-1 px-3 border rounded-md border-orange-400 hover:text-orange-800">Tampilan</a>
            </div>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input class="w-96" placeholder="Search..." type="search" wire:model="search" wire:input.debounce.500ms="filtered()" />
        </div>
        <div class="flex-1">
            <x-select class="w-72" :keyBind="false" :data="$allStatuses" wire:model="filter_status" wire:change="filtered()"></x-select>
        </div>
    </div>
    <div class="relative overflow-x-auto">
        <table class="table mt-3">
            <thead>
            <tr>
                <th class="w-12">No.</th>
                <th>Nama Peserta</th>
                <th>Email</th>
                <th>No Telepon</th>
                <th>Alamat</th>
                <th>Bukti</th>
                <th>Status</th>
                <th>Referral</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td data-label="No">{{ $no++ }}</td>
                    <td data-label="Nama Peserta">{{ $item->user->name }}</td>
                    <td data-label="Email">{{ $item->user->email }}</td>
                    <td data-label="No Telepon">{{ $item->user->phone }}</td>
                    <td data-label="Alamat">{{ $item->user->address }}</td>
                    <td data-label="Bukti">
                        @if($item->upload_image)
                            <a wire:click="showConfirmation('{{ $item->upload_image }}', {{ $item->id }})" class="text-orange-400 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                </svg>
                            </a>
                        @endif
                    </td>
                    <td>
                        {{ $item->status }}
                    </td>
                    <td>
                        {{ $item->referral_code }}
                    </td>
                    <td>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="w-full mt-3">
            {{ $items->links() }}
        </div>
    </div>

    <x-modal wire:ignore.self name="show-confirmation" :show="$errors->isNotEmpty()" focusable id="show-confirmation">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Mohon Cermati Bukti Pembayaran
                </h3>
                <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <img src="{{ $image }}" class="w-full rounded-md"/>
            </div>
            <div class="flex items-center justify-between gap-4 p-4">
                <x-secondary-button x-on:click="$dispatch('close)">
                    {{ __('Tolak') }}
                </x-secondary-button>
                <x-primary-button wire:click="saved()">{{ __('Setujui') }}</x-primary-button>
            </div>
        </div>
    </x-modal>

</div>
