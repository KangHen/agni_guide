<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Page;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

new class extends Component {
    use WithPagination, WithFileUploads;

    public int $id = 0;
    public string|null $title = '';
    public string|null $content = '';
    public string|null $image = '';
    public string|null $slug = '';
    public int $is_published = 0;

    public int $no = 1;
    public string|null $search = '';

    public $file;
    public array $status = [0 => 'Tidak', 1 => 'Publish'];

    public  function mount(): void
    {}

    /**
     * With
     * @return array
     */
    public function with(): array
    {
        return [
            'items' => Page::query()
                ->when($this->search, fn($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
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
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($this->file) {
            $path = public_path('images/pages');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $imageName = md5(time()) . rand(111,999);
            $this->image = $this->file->storeAs('pages', $imageName. '.' .$this->file->extension(), 'images_public_path');

            $img = Image::read(public_path('images/' . $this->image));
            $img->scale(width: 200);

            $img->save('images/pages/sm-' . $imageName . '.' . $this->file->extension());
        }

        $saved = Page::updateOrCreate(
            ['id' => $this->id],
            [
                'title' => $this->title,
                'content' => $this->content,
                'image' => $this->image,
                'slug' => str($this->title)->slug(),
                'is_published' => 0,
                'user_id' => auth()->id()
            ]
        );

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Saved Successfully');
        } else {
            session()->flash('message', 'Failed to save');
        }

        $this->redirect('/page', navigate: true);
    }

    /**
     * Edit
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $page = Page::find($id);

        $this->id = $page->id;
        $this->title = $page->title;
        $this->content = $page->content;
        $this->image = $page->image;

        $this->dispatch('open-edit-modal');
    }

    /**
     * Delete
     * @return void
     */
    public function delete(): void
    {
        $page = Page::find($this->id);

        if ($page) {
            $page->delete();
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('message', 'Failed to delete');
        }

        $this->redirect('/page', navigate: true);
    }

    /**
     * Set Active
     * @param int $id
     * @param int $value
     * @return void
     */
    public function setActive(int $id, int $value): void
    {
        $page = Page::find($id);
        $page->is_published = $value;

        if ($page->save()) {
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('error', 'Error Updated');
        }

        $this->redirect('/page', navigate: true);
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
        $this->reset('id', 'title', 'content', 'image', 'slug', 'is_published');
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
                {{ __('Buat Halaman') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto mt-3">
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th class="w-12">No</th>
                <th>Judul</th>
                <th>Cuplikan Isi</th>
                <th class="w-72">Publish</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ substr(strip_tags($item->content), 0, 100) }}</td>
                    <td>
                        <x-select :data="$status" :value="$item->is_published" wire:change="setActive({{ $item->id }}, $event.target.value)" />
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

    @include('livewire.posts.page.form')

    <x-modal name="confirm-page-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
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
            quill.root.innerHTML = $wire.get('content');

            $wire.dispatch('open-modal', 'form');
        });

        $wire.on('confirm-delete', (id) => {
            $wire.set('id', id);
            $wire.dispatch('open-modal', 'confirm-page-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-page-deleted');
        });

        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        $wire.on('get-content', () => {
            $wire.set('content', quill.root.innerHTML);
        });

        $wire.on('close-page-modal', () => {
            quill.root.innerHTML = '';
            $wire.dispatch('reset-form');
            $wire.dispatch('close-modal', 'form');
        });
    </script>
    @endscript
</div>
