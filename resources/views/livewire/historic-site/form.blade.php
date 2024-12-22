<x-modal wire:ignore.self name="form" :show="$errors->isNotEmpty()" focusable id="add-modal">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Form Situs
            </h3>
            <button type="button" x-on:click="$dispatch('close-site-modal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5">
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="role_id" :value="__('Kategori')" />
                    <x-select :data="$categories" wire:model="category_id"/>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input wire:model="name" id="name" name="name" type="text" placeholder="Nama User" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Alamat')" />
                    <x-text-input wire:model="address" id="address" name="address" type="text" placeholder="Alamat" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
               <div class="flex">
                   @foreach($images as $image)
                        <img src="{{ asset('images/' . $image) }}" class="w-40" />
                   @endforeach
               </div>
                <div class="col-span-2">
                    <x-input-label for="avatar" :value="__('Dokumentasi')" />
                    <x-text-input wire:model="files" id="files" name="files" type="file" class="mt-1 block w-full border p-1" multiple />
                    <x-input-error class="mt-2" :messages="$errors->get('files')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2" wire:ignore>
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Maps')" />
                    <x-maps />
                </div>
            </div>
            <div>
                <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2" wire:ignore>
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Deskripsi')" />
                    <div id="editor"></div>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 mt-20">
                <x-secondary-button x-on:click="$dispatch('close-site-modal')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button x-on:mouseover="$dispatch('get-content')" wire:click.prevent="saved()">{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
