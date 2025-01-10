<x-modal wire:ignore.self name="form" :show="$errors->isNotEmpty()" focusable id="add-modal">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Form Product
            </h3>
            <button type="button" x-on:click="$dispatch('close-product-modal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5">
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Kategori')" />
                    <x-select :data="$productCategories" wire:model="category_id"/>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input wire:model="name" id="name" name="name" type="text" placeholder="Nama Produk" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="flex">
                    @foreach($images as $image)
                        <div>
                            <x-secondary-button wire:click="removeImage('{{ $image }}')">
                                <i class="bi bi-trash"></i>
                            </x-secondary-button>
                            <img src="{{ asset('images/products/' . $image) }}" class="w-40" alt="gambar"/>
                        </div>
                    @endforeach
                </div>
                <div class="col-span-2">
                    <x-input-label for="avatar" :value="__('Gambar (lebih > 1 gambar)')" />
                    <x-text-input wire:model="files" id="file" name="files" type="file" class="mt-1 block w-full border p-1" multiple />
                    <x-input-error class="mt-2" :messages="$errors->get('files')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="price" :value="__('Harga')" />
                    <x-text-input wire:model="price" id="price" name="price" type="number" placeholder="Harga" class="mt-1 block w-full placeholder-gray-300" required />
                    <x-input-error class="mt-2" :messages="$errors->get('price')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="quantity" :value="__('QTY')" />
                    <x-text-input wire:model="quantity" id="quantity" name="quantity" type="number" placeholder="QTY" class="mt-1 block w-full placeholder-gray-300" required />
                    <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="sizes" :value="__('Ukuran')" />
                    @foreach($productSizes as $key => $productSize)
                        <x-text-input wire:model="sizes" :value="$productSize" id="sizes" name="sizes[]" type="checkbox" class="mt-1"/>
                        {{ str($productSize)->upper() }}
                    @endforeach
                    <x-input-error class="mt-2" :messages="$errors->get('sizes')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2" wire:ignore>
                <div class="col-span-2">
                    <x-input-label for="description" :value="__('Deskripsi')" />
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    <div id="editor"></div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 mt-20">
                <x-secondary-button x-on:click="$dispatch('close-product-modal')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button x-on:mouseover="$dispatch('get-content')" wire:click.prevent="saved()">{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
