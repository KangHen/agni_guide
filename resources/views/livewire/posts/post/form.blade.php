<x-modal wire:ignore.self name="form" :show="$errors->isNotEmpty()" focusable id="add-modal">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Form Post
            </h3>
            <button type="button" x-on:click="$dispatch('close-post-modal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5">
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Judul')" />
                    <x-text-input wire:model="title" id="title" name="title" type="text" placeholder="Nama User" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="flex">
                    @if($image)
                        <img src="{{ asset('images/' . $image) }}" class="w-40" />
                    @endif
                </div>
                <div class="col-span-2">
                    <x-input-label for="avatar" :value="__('Image')" />
                    <x-text-input wire:model="file" id="file" name="file" type="file" class="mt-1 block w-full border p-1" />
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2" wire:ignore>
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Konten')" />
                    <div id="editor"></div>
                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 mt-20">
                <x-secondary-button x-on:click="$dispatch('close-post-modal')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button x-on:mouseover="$dispatch('get-content')" wire:click.prevent="saved()">{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
