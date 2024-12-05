<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use App\Models\HistoricSite;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

new class extends Component {
    use WithPagination, WithFileUploads;

    public int $id = 0;
    public string $name = '';
    public string $description = '';
    public string $address = '';
    public string $longitude = '';
    public string $latitude = '';
    public array $images = [];
    public int $category_id = 0;

    public $files;

    public int $no = 1;
    public string|null $search = '';
    public int $filter_category_id = 0;

    public Collection|array $categories = [];
    public Collection|array $all_categories = [];
    public array $showeds = [
        0 => 'Tidak',
        1 => 'Ya'
    ];

    /**
     * Mount
     *
     * @return void
     */
    public function mount(): void {
        $categories = Category::all();

        $this->categories = $categories->pluck('name', 'id')
            ->prepend('-Pilih Kategori-', 0)
            ->toArray();

        $this->all_categories = $categories->pluck('name', 'id')
            ->prepend('All', 0)
            ->toArray();

        if (request()->get('page') > 1) {
            $this->no = ((request()->get('page')-1)*10) + 1;
        }
    }

    /**
     * With
     *
     * @return array
     */
    public function with(): array
    {
        return [
            'items' => HistoricSite::with('category')
                ->when($this->search, fn($query, $search) => $query->where('name', 'like', "%$search%"))
                ->when($this->filter_category_id, fn($query, $filter_category_id) => $query->where('category_id', $filter_category_id))
                ->paginate(10)
        ];
    }

    /**
     * Edit
     * @param int $id
     * @return void
     * ** */
    public function edit(int $id): void
    {
        $this->id = $id;
        $site = HistoricSite::find($id);

        $this->name = $site->name;
        $this->category_id = $site->category_id;
        $this->description = $site->description;
        $this->address = $site->address;
        $this->longitude = $site->longitude;
        $this->latitude = $site->latitude;
        $this->images = json_decode($site->images);

        $this->dispatch('open-edit-modal');
    }

    /**
     * Store or Update
     *
     * @return void
     */
    public function saved(): void
    {
        $this->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'files.*' => 'required|image|max:1024'
        ]);

        if (!$this->files && !$this->id) {
            $this->addError('files', 'The files field is required.');
            return;
        }

        $path = public_path('images/historic_sites');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $images = [];
        if ($this->files) {
            foreach ($this->files as $image) {
                $photoName = md5(time()) . rand(111,999);
                $images[] = $image->storeAs('historic_sites', $photoName. '.' .$image->extension(), 'images_public_path');
            }
        }

        if ($this->id) {
            $images = $this->images;
            foreach ($images as $itemAdd) {
                $images[] = $itemAdd;
            }
        }

        $saved = HistoricSite::updateOrCreate(
            ['id' => $this->id],
            [
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
                'address' => $this->address,
                'phone' => $this->phone,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'images' => json_encode($images),
                'user_id' => auth()->id()
            ]
        );

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Saved Successfully');
        } else {
            session()->flash('error', 'Error Saved');
        }

        $this->redirect('/historic-site', navigate: true);
    }

    /**
     * Delete
     *
     * @return void
     */
    public function delete(): void
    {
        $site = HistoricSite::find($this->id);

        if ($site) {
            $site->delete();
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('error', 'Error Deleted');
        }

        $this->id = 0;
        $this->redirect('/historic-site', navigate: true);
    }

    /**
     * Set active or not
     *
     * @param int $id
     * @param int $value
     * @return void
     */
    public function setActive(int $id, int $value): void
    {
        $site = HistoricSite::find($id);
        $site->is_show = $value;

        if ($site->save()) {
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('error', 'Error Updated');
        }

        $this->redirect('/historic-site', navigate: true);
    }

    /**
     * Filtered
     *
     * @return void
     */
    public function filtered(): void
    {
        $this->resetPage();
    }

    /**
     * Reset
     *
     * @return void
     */
    #[\Livewire\Attributes\On('reset-form')]
    public function _reset(): void
    {
        $this->reset('id', 'name', 'category_id', 'description', 'address', 'phone', 'longitude', 'latitude', 'images');
    }
}; ?>

<div>
    <x-loading />

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input wire:model="search" wire:input.debounce.300ms="filtered()" class="w-96" placeholder="Search..."></x-text-input>
        </div>
        <div class="flex-1">
            <x-select class="w-72" :data="$all_categories" wire:model="filter_category_id" wire:change="filtered()"></x-select>
        </div>
        <div class="ml-2">
            <x-create-button x-on:click="$dispatch('open-modal', 'form')">
                {{ __('Buat Situs') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto mt-3">
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th>No</th>
                <th>Nama Situs</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Publish</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category?->name ?? '-' }}</td>
                    <td>{{ $item->address }}</td>
                    <td>
                        <x-select :data="$showeds" :value="$item->is_show" wire:change="setActive({{ $item->id }}, $event.target.value)" />
                    </td>
                    <td>
                        <x-edit-button wire:click="edit({{ $item->id }})" />
                        <x-delete-button x-on:click.prevent="$dispatch('confirm-delete', {{ $item->id }})" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No data available</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="w-full mt-3">
            {{ $items->links() }}
        </div>
    </div>

    @include('livewire.historic-site.form')

    <x-modal name="confirm-site-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
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
        $wire.on('open-edit-modal', () => {
            const lng = $wire.get('longitude');
            const lat = $wire.get('latitude');

            if (lng && lat && mapBoxHistorySite) {
                mapBoxHistorySite.flyTo({
                    center: [lng, lat],
                    essential: true
                });
            }

            quill.root.innerHTML = $wire.get('description');

            $wire.dispatch('open-modal', 'form');
        });

        $wire.on('confirm-delete', (id) => {
            $wire.set('id', id);
            $wire.dispatch('open-modal', 'confirm-site-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-site-deleted');
        });

        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        $wire.on('get-content', () => {
            $wire.set('description', quill.root.innerHTML);
        });

        $wire.on('close-site-modal', () => {
            quill.root.innerHTML = '';
            $wire.dispatch('reset-form');
            $wire.dispatch('close-modal', 'form');
        });
    </script>
    @endscript
</div>
