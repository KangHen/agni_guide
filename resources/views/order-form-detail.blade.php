@section('title', 'Order Detail')
<x-order-layout>
    <div class="lg:w-9/12 md:w-9/12 sm:w-full mx-auto py-6">
        <livewire:order-form.detail :code="request()->code" />
    </div>
</x-order-layout>
