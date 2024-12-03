<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public int $no = 1;
    public int $id = 0;
    public string $name;

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
            'items' => Category::query()
                ->when($this->search <> null, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                ->paginate(10)
        ];
    }

    /**
     * Edited Setting
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $category = Category::find($id);
        $this->id = $category->id;
        $this->name = $category->name;
    }

    /**
     * Update or Create Setting
     * @return void
     */
    public function saved(): void
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Stored Setting
     * @return void
     */
    public function store(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $saved = Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'user_id' => auth()->id()
        ]);

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Saved Successfully');
        } else {
            session()->flash('error', 'Error Created');
        }

        $this->redirect('/category', navigate: true);
    }

    /**
     * Updated Setting
     * @return void
     */
    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $saved = Category::where('id', $this->id)
            ->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('error', 'Error Updated');
        }

        $this->redirect('/category', navigate: true);
    }

    /**
     * Deleted Setting
     * @return void
     */
    public function delete(): void
    {
        $setting = Category::find($this->id);
        $this->id = 0;

        if ($setting->delete()) {
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('error', 'Error Deleted');
        }

        $this->redirect('/category', navigate: true);
    }

    /**
     * Filtered Setting
     * @return void
     */
    public  function filtered()
    {
        $this->resetPage();
    }

    private function _reset()
    {
        $this->reset('id', 'name');
    }
} ?>

<div>
    <x-loading />

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input class="w-96" placeholder="Search..." type="search" wire:model="search" wire:input.debounce.500ms="filtered()" />
        </div>

        <x-create-button x-on:click.prevent="$dispatch('open-modal', 'form')">
            Buat Kategori
        </x-create-button>
    </div>
    <div class="relative overflow-x-auto">
        <table class="table mt-3">
            <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td data-label="No">{{ $no++ }}</td>
                    <td data-label="Nama">{{ $item->name }}</td>
                    <td>
                        <x-edit-button wire:click="edit({{ $item->id }})" />
                        <x-delete-button x-on:click.prevent="$dispatch('confirm-delete', {{ $item->id }})" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="w-full mt-3">
            {{ $items->links() }}
        </div>
    </div>

    @include('livewire.setting.category.form')

    <x-modal name="confirm-category-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
        <div class="p-5">
            <h3 class="text-lg font-bold">Hapus</h3>
            <p class="py-4">Yakin hapus data ini?</p>
            <div class="modal-action">
                <x-secondary-button x-on:click="$dispatch('cancel-delete')">
                    {{ __('Batalkan') }}
                </x-secondary-button>

                <x-danger-button wire:click="delete" class="ms-3">
                    {{ __('Hapus') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>

    @script
    <script>
        $wire.on('confirm-delete', (id) => {
            $wire.set('id', id);
            $wire.dispatch('open-modal', 'confirm-category-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-user-deleted');
        });
    </script>
    @endscript
</div>
