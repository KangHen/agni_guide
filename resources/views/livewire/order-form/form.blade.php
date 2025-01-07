<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\User;
Use App\Models\Product;
use Illuminate\Support\Str;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Crypt;

new class extends Component {
    public string|null $name;
    public string|null $email;
    public int|null $phone;
    public string|null $address;
    public int $quantity = 1;
    public int $product_id = 0;
    public string $image = '';

    public Product|null $product;
    public $data;

    public function mount(string|null $payload): void
    {
        if (!$payload) {
            redirect()->route('welcome');
            return;
        }

        try {
            $extract = Crypt::decryptString($payload);
            $this->data = json_decode($extract);
            $this->product_id = $this->data->product_id;
            $this->product = Product::find($this->product_id);

            if (!$this->product) {
                redirect()->route('welcome');
            }

            $images = $this->product->images ? json_decode($this->product->images) : [];
            $this->image = $images ? asset('images/products/' . $images[0]) : '';
        } catch (Exception $e) {
            redirect()->route('welcome');
        }
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|integer',
            'address' => 'required|string'
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'phone.required' => 'No Telpon harus diisi',
            'address.required' => 'Alamat harus diisi',
        ]);

        $randomPassword = Str::password();

        $user = User::firstOrCreate([
            'email' => $this->email
            ],[
            'password' => $randomPassword,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => true,
            'role_id' => UserRole::GUEST
        ]);

        $orderCode = 'AG'.Str::random();
        $order = Order::query()
            ->create([
                'user_id' => $user->id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'coupon' => $this->data->coupon,
                'order_code' => $orderCode,
                'discount' => 0,
                'total' => $this->product->price,
                'status' => 'pending',
                'grand_total' => $this->product->price * $this->quantity
            ]);

        $this->redirect('/order-form-detail/' . $orderCode, navigate: true );
    }
}; ?>

<div>
    <x-loading/>
    <div class="lg:flex md:flex sm:block justify-between items-end">
        <div class="mb-3">
            <h1 class="mb-0 font-bold text-2xl">Form Registrasi</h1>
            <p class="font-normal">{{ $product->name }}</p>
        </div>
        <div class="mb-3">
            <img src="{{ $image }}" class="lg:w-72 md:w-72 sm:w-full rounded-md shadow-md" alt="product image">
        </div>
    </div>

    <div class="bg-white shadow-md overflow-hidden sm:rounded-lg  mx-auto">
        <div class="bg-white">
            <div class="p-6 text-gray-900">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2  gap-4">
                    <div></div>
                    <div>
                        <div class="flex">
                            <span
                                class="quantity-minus cursor-pointer inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                   class="bi bi-plus-lg" viewBox="0 0 16 16">
                                  <path fill-rule="evenodd"
                                        d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                              </svg>
                            </span>
                            <input wire:model="quantity" type="text" min="1"
                                  class="text-center rounded-none bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                  placeholder="1">
                            <span wire:click="quantity++" class="cursor-pointer inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                   class="bi bi-dash-lg" viewBox="0 0 16 16">
                                  <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8"/>
                              </svg>
                            </span>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')"/>
                        <x-text-input wire:model="email" type="email" name="email" placeholder="youremail@gmail.com"
                                      class="block mt-1 w-full"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="name" :value="__('Nama Peserta')"/>
                        <x-text-input wire:model="name" name="name" placeholder="Nama anda" class="block mt-1 w-full"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('No Telpon')"/>
                        <x-text-input wire:model="phone" name="phone" placeholder="628xxxxxxxxx"
                                      class="block mt-1 w-full"/>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="address" :value="__('Alamat')"/>
                        <x-text-input wire:model="address" name="address" placeholder="Jl. Basuki Rachmat..."
                                      class="block mt-1 w-full"/>
                        <x-input-error :messages="$errors->get('address')" class="mt-2"/>
                    </div>
                    <div>
                        <x-primary-button wire:click.prevent="register()">
                            {{ __('Daftar') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const minus = $wire.$el.querySelector('.quantity-minus');
        minus.addEventListener('click', () => {
            if ($wire.quantity > 1) {
                $wire.quantity--;
            }
        })
    </script>
    @endscript
</div>
