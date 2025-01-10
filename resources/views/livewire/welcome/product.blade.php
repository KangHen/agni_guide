<?php

use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Crypt;

new class extends Component {
    public Collection|null $cityTours = null;
    public bool $hasProduct = false;

    public function mount(): void
    {
        $cityTourId = ProductCategory::query()
            ->where('slug', 'city-tour')
            ->first();

        if (!$cityTourId) {
            $this->hasProduct = false;
        } else {
            $this->hasProduct = true;
            $this->cityTours = Product::query()
                ->where('category_id', $cityTourId->id)
                ->latest('id')
                ->limit(2)
                ->get()
                ->map(function ($product) {
                    $product->image = json_decode($product->images)[0] ?? null;
                    return $product;
                });
        }
    }

    public function buyProduct(int $id): void
    {
        $data = [
            'product_id' => $id,
            'coupon' => null,
            'discount' => 0
        ];

        $payload = Crypt::encryptString(json_encode($data));
        $this->redirect('/order-form?agid='.$payload);
    }
}; ?>

<div>
    @if($hasProduct)
    <div class="relative isolate bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl" aria-hidden="true">
            <div class="mx-auto aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base/7 font-semibold text-indigo-600">#City Tour</h2>
            <p class="mt-2 text-balance text-5xl font-semibold tracking-tight text-gray-900 sm:text-6xl">City Tour Tuban</p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg font-medium text-gray-600 sm:text-xl/8">Pilih paket city tour yang tersedia, dan jelajahi kekayaan budaya, sejarah, dan destinasi menarik di Kabupaten Tuban.</p>
        <div class="mx-auto mt-16 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-20 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2">
            @foreach($cityTours as $ct)
            @if ($loop->index == 1 || $cityTours->count() == 1)
            <div class="relative rounded-3xl rounded-t-3xl bg-slate-50 p-8 ring-1 ring-gray-900/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-bl-3xl lg:rounded-tr-none">
                <h3 id="tier-hobby" class="text-base/7 font-semibold text-gray-500">{{ $ct->name }}</h3>
                <p class="mt-4 flex items-baseline gap-x-2 mb-3">
                    <span class="text-5xl font-semibold tracking-tight text-gray-500">{{ number_format($ct->price) }}</span>
                    <span class="text-base text-gray-500">/tour</span>
                </p>
                <img class="w-100 rounded-lg grayscale" src="{{ asset('images/products/' .$ct->image) }}" />

                <div class="sold-out absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                  <span class="text-white text-lg font-bold uppercase bg-red-600 px-4 py-2 rounded-lg shadow-lg">
                    Sold Out
                  </span>
                </div>
            </div>
            @endif
            @if ($loop->index == 0)
            <div class="relative rounded-3xl bg-white/60 p-8 shadow-2xl ring-1 ring-gray-900/10 sm:p-10">
                <h3 id="tier-enterprise" class="text-base/7 font-semibold text-orange-400">{{ $ct->name }}</h3>
                <p class="mt-4 flex items-baseline gap-x-2 mb-3">
                    <span class="text-5xl font-semibold tracking-tight text-gray-900">{{ number_format($ct->price) }}</span>
                    <span class="text-base text-gray-400">/tour</span>
                </p>
                <img class="w-100 rounded-lg" src="{{ asset('images/products/' .$ct->image) }}" />
                <a wire:click="buyProduct({{ $ct->id }})" aria-describedby="tier-enterprise" class="cursor-pointer mt-8 block rounded-md bg-orange-400 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-orange-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:mt-10">Beli Paket Tour</a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
