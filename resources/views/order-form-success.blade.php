@section('title', 'Order Success')
<x-order-layout>
    <div class="lg:w-1/3 md:w-1/3 sm:w-full mx-auto py-6">
        <livewire:order-form.success :code="request()->code" />
    </div>
</x-order-layout>
