<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductCategory;

new class extends Component {
    use WithPagination;

    public int $no = 1;

    public string|null $search = '';
    public array $productCategories = [];
    public int $filter_category_id = 0;

    /** Mount Setting */
    public function mount(): void {
        session()->remove('message');

        if (request()->get('page') > 1) {
            $this->no = ((request()->get('page')-1)*$this->paginate) + 1;
        }

        $this->productCategories = ProductCategory::query()
            ->select('id', 'name')
            ->get()
            ->pluck('name', 'id')
            ->prepend('-Pilih Kategori-', 0)
            ->toArray();
    }

    /**
     * @return array
     */
    public  function with(): array
    {
        return [
            'items' => Product::withSum('order', 'quantity')
                ->whereHas('order')
                ->when($this->search <> null, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                ->when($this->filter_category_id > 0, fn($q) => $q->where('category_id', $this->filter_category_id))
                ->paginate(10)
        ];
    }

    /**
     * @param int $id
     * @return void
     */
    public function product(int $id): void
    {
        $this->redirect('/order/product/'.$id, navigate: true);
    }

    /**
     * Filtered Setting
     * @return void
     */
    public  function filtered(): void
    {
        $this->resetPage();
    }
} ?>

<div>
    <x-loading />

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input class="w-96" placeholder="Search..." type="search" wire:model="search" wire:input.debounce.500ms="filtered()" />
        </div>
        <div class="flex-1 text-right">
            <x-select :data="$productCategories"
                      wire:model="filter_category_id"
                      wire:change="filtered()"
            ></x-select>
        </div>
    </div>
    <div class="relative overflow-x-auto">
        <table class="table mt-3">
            <thead>
            <tr>
                <th class="w-12">No.</th>
                <th>Nama Produk</th>
                <th>Jumlah Order</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td data-label="No">{{ $no++ }}</td>
                    <td data-label="Nama Produk">{{ $item->name }}</td>
                    <td data-label="Jumlah Order">{{ $item->order_sum_quantity }}</td>
                    <td>
                        <x-primary-button wire:click="product({{ $item->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                            </svg>
                        </x-primary-button>
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
</div>

