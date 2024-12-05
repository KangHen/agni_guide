<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Post;
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
    public string|null $postable_type = '';
    public int $postable_id = 0;

    public int $no = 1;
    public string|null $search = '';

    public $file;

    public  function mount(): void
    {

    }

    public function with(): array
    {
        return [
            'items' => Post::query()
                ->when($this->search, fn($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
                ->paginate(10)
        ];
    }

    public function saved(): void
    {
        $this->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($this->file) {
            $path = public_path('images/posts');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $imageName = md5(time()) . rand(111,999);
            $this->image = $this->file->storeAs('posts', $imageName. '.' .$this->file->extension(), 'images_public_path');

            $img = Image::read(public_path('images/' . $this->image));
            $img->scale(width: 200);

            $img->save('images/posts/sm-' . $imageName . '.' . $this->file->extension());
        }

        $saved = Post::updateOrCreate(
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

        $this->redirect('/post', navigate: true);
    }

    public function edit(int $id): void
    {
        $post = Post::find($id);

        $this->id = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->image = $post->image;

        $this->dispatch('open-edit-modal');
    }

    public function delete(): void
    {
        $post = Post::find($this->id);

        if ($post) {
            $post->delete();
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('message', 'Failed to delete');
        }

        $this->redirect('/post', navigate: true);
    }

    public function filtered(): void
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('reset-form')]
    public function _reset(): void
    {
        $this->reset('id', 'title', 'content', 'image', 'slug', 'is_published', 'postable_type', 'postable_id');
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
                {{ __('Buat Post') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto mt-3">
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th class="w-12">No</th>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Cuplikan Isi</th>
                <th>Publish</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        @if($item->image)
                            <img src="{{ asset('images/' . str($item->image)->replace('posts/', 'posts/sm-')) }}" class="w-20" />
                        @endif
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ substr(strip_tags($item->content), 0, 100) }}</td>
                    <td>{{ $item->is_published }}</td>
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

    @include('livewire.posts.post.form')

    <x-modal name="confirm-post-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
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
            $wire.dispatch('open-modal', 'confirm-post-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-post-deleted');
        });

        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        $wire.on('get-content', () => {
            $wire.set('content', quill.root.innerHTML);
        });

        $wire.on('close-post-modal', () => {
            quill.root.innerHTML = '';
            $wire.dispatch('reset-form');
            $wire.dispatch('close-modal', 'form');
        });
    </script>
    @endscript
</div>
