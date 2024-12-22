<x-modal wire:ignore.self name="form" :show="$errors->isNotEmpty()" focusable id="add-modal">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Form User
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
                    <x-text-input wire:model="name" id="name" name="name" type="text" placeholder="Nama User" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" name="email" type="email" placeholder="Masukkan Email" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="email" :value="__('No Whatsapp')" />
                    <x-text-input wire:model="phone" id="phone" name="phone" type="text" placeholder="Masukkan No Whatsapp" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="email" :value="__('Alamat')" />
                    <x-text-input wire:model="address" id="address" name="address" type="text" placeholder="Masukkan Alamat" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="email" :value="__('Kota')" />
                    <x-text-input wire:model="city" id="city" name="city" type="text" placeholder="Masukkan Kota" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="password" :value="__('Password')" />
                    @if ($id > 0)
                        <p class="text-xs italic text-red-400">Biarkan password jika tidak diubah</p>
                    @endif
                    <x-text-input wire:model="password" id="password" name="password" type="password" class="mt-1 block w-full placeholder-gray-300" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>
            </div>
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <x-input-label for="role_id" :value="__('Role')" />
                    <x-select :data="$roles" wire:model="role_id" id="role_id" name="role_id" required/>
                    <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
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
