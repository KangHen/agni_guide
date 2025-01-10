<?php

use Livewire\Volt\Component;
use App\Models\ProductCategory;

new class extends Component {
    public array $productCategory = [];

    public function mount(): void
    {
        $this->productCategory = ProductCategory::query()
            ->select('id', 'name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}; ?>

<div>
    <nav class="bg-white border-gray-200 dark:bg-gray-900 rounded-t-lg">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="{{ route('commerce') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <x-application-logo class="w-40" />
            </a>
        </div>
    </nav>
    <nav class="bg-white">
        <div class="max-w-screen-xl px-4 py-3 mx-auto">
            <div class="flex items-center">
                <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                    @foreach($productCategory as $key => $value)
                        <li><a href="#{{ $key }}" class="hover:text-gray-900 dark:hover:text-white">{{ $value }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</div>
