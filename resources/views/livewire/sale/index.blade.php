<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

new class extends Component {
    use WithPagination, WithFileUploads;

    public int $id = 0;
    public string|null $name = '';
    public string|null $slug = '';
    public array $images = [];
    public string|null $description = '';
    public array $sizes = [];
    public int $price = 0;
    public int $quantity = 0;
    public int $is_published = 0;
    public int $history_site_id = 0;
    public int $is_sold_out = 0;
    public int|null $category_id = 0;

    public int $no = 1;
    public string|null $search = '';

    public $files;
    public array $productSizes = ['s', 'm', 'l', 'xl', 'xxl'];
    public array $soldOuts = [0 => 'Ready', 1 => 'Habis'];
    public array|\Illuminate\Support\Collection $productCategories = [];

    public  function mount(): void
    {
        $this->productCategories = ProductCategory::query()
            ->select('id', 'name')
            ->pluck('name', 'id')
            ->prepend('-Pilih Kategori-', 0)
            ->toArray();
    }

    /**
     * With
     * @return array
     */
    public function with(): array
    {
        return [
            'items' => Product::query()
                ->when($this->search, fn($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
                ->orderByDesc('id')
                ->paginate(10)
        ];
    }

    /**
     * Saved
     * @return void
     */
    public function saved(): void
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'files.*' => 'required|image|mimes:jpeg,png,jpg|max:1024',
            'quantity' => 'required',
        ]);

        $images = [];
        if ($this->files) {
            $path = public_path('images/products');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            foreach ($this->files as $file) {
                $imageName = md5(time()) . rand(111,999);
                $file->storeAs('products', $imageName. '.' .$file->extension(), 'images_public_path');
                $images[] = $imageName. '.' .$file->extension();

                $img = Image::read(public_path('images/products/' . $imageName. '.' .$file->extension()));
                $img->scale(width: 200);
                $img->save('images/products/sm-' . $imageName . '.' . $file->extension());

                $img = Image::read(public_path('images/products/' . $imageName. '.' .$file->extension()));
                $img->scale(width: 600);
                $img->save();
            }

            if ($this->id) {
                foreach ($this->images as $image) {
                   $images[] = $image;
                }
            }

            $this->images = $images;
        }

        $saved = Product::updateOrCreate(
            ['id' => $this->id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'images' => json_encode($this->images),
                'slug' => str($this->name)->slug(),
                'quantity' => $this->quantity,
                'sizes' => json_encode($this->sizes),
                'is_sold_out' => 0,
                'category_id' => $this->category_id,
                'user_id' => auth()->id(),
                'history_site_id' => $this->history_site_id,
            ]
        );

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Saved Successfully');
        } else {
            session()->flash('message', 'Failed to save');
        }

        $this->redirect('/sale', navigate: true);
    }

    /**
     * Edit
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $product = Product::find($id);

        $this->id = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->images = json_decode($product->images);
        $this->price = $product->price;
        $this->sizes = json_decode($product->sizes);
        $this->quantity = $product->quantity;
        $this->history_site_id = $product->history_site_id ?? 0;
        $this->is_sold_out = $product->is_sold_out;

        $this->dispatch('open-edit-modal');
    }

    /**
     * Delete
     * @return void
     */
    public function delete(): void
    {
        $product = Product::find($this->id);

        if ($product) {
            $product->delete();
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('message', 'Failed to delete');
        }

        $this->redirect('/sale', navigate: true);
    }

    /**
     * Set Sold Out
     * @param int $id
     * @param int $value
     * @return void
     */
    public function setSoldOut(int $id, int $value): void
    {
        $product = Product::find($id);

        if ($product) {
            $product->update(['is_sold_out' => $value]);
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('message', 'Failed to update');
        }

        $this->redirect('/sale', navigate: true);
    }

    /**
     * Remove Image
     * @param string $image
     * @return void
     */
    public function removeImage(string $image): void
    {
        $product = Product::find($this->id);

        if ($product) {
            $images = collect($this->images)
                ->filter(fn($img) => $img !== $image)
                ->toArray();

            $product->update(['images' => json_encode($images)]);

            $this->images = json_decode($product->images);
        }
    }

    /**
     * Filtered
     * @return void
     */
    public function filtered(): void
    {
        $this->resetPage();
    }

    /**
     * Reset
     * @return void
     */
    #[\Livewire\Attributes\On('reset-form')]
    public function _reset(): void
    {
        $this->reset('id', 'name', 'description', 'price', 'sizes', 'quantity', 'is_published', 'history_site_id', 'is_sold_out', 'files', 'images', 'category_id');
    }
}; ?>

<div>
    <x-loading />

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input wire:model="search" wire:input.debounce.300ms="filtered()" class="w-96" placeholder="Search..."></x-text-input>
        </div>
        <div class="ml-2">
            <x-create-button x-on:click="$dispatch('open-modal', 'form')">
                {{ __('Buat Produk') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto mt-3">
        @if ($errors->any())
            <ul>
            @foreach($errors->all() as $error)
                <li class="text-red-600">{{ $error }}></li>
            @endforeach
            </ul>
        @endif
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th class="w-12">No</th>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Price</th>
                <th>Status</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        @if($item->images)
                            <img src="{{ asset('images/products/sm-' . json_decode($item->images)[0]) }}" class="w-20" />
                        @endif
                    </td>
                    <td>{{ $item->name }}</td>
                    <td>{{ \Illuminate\Support\Number::format($item->price) }}</td>
                    <td>
                        <x-select :data="$soldOuts" :value="$item->is_sold_out" wire:change="setSoldOut({{ $item->id }}, $event.target.value)" />
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

    @include('livewire.sale.form')

    <x-modal name="confirm-product-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
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
            quill.root.innerHTML = $wire.get('description');

            $wire.dispatch('open-modal', 'form');
        });

        $wire.on('confirm-delete', (id) => {
            $wire.set('id', id);
            $wire.dispatch('open-modal', 'confirm-product-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-product-deleted');
        });

        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        $wire.on('get-content', () => {
            $wire.set('description', quill.root.innerHTML);
        });

        $wire.on('close-product-modal', () => {
            quill.root.innerHTML = '';
            $wire.dispatch('reset-form');
            $wire.dispatch('close-modal', 'form');
        });
    </script>
    @endscript
</div>
