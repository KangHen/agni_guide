<x-modal wire:ignore.self name="form" :show="$errors->isNotEmpty()" focusable id="add-modal">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Form Kategori Produk
            </h3>
            <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5">
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input wire:model="name" id="name" name="name" type="text" placeholder="Nama Kategori Produk" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button wire:click.prevent="saved()">{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
