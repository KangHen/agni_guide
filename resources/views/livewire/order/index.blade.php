<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;

new class extends Component {
    use WithPagination;

    public int $no = 1;

    public string|null $search = '';

    /** Mount Setting */
    public function mount(): void {
        if (request()->get('page') > 1) {
            $this->no = ((request()->get('page')-1)*$this->paginate) + 1;
        }
    }

    /**
     * @return array
     */
    public  function with(): array
    {
        return [
            'items' => Product::query()
                ->whereHas('order')
                ->when($this->search <> null, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                ->paginate(10)
        ];
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
                    <td data-label="Nama Produk"></td>
                    <td data-label="Jumlah Order"></td>
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
</div>

