@section('title', 'Order Form')
<x-order-layout>
    <div class="lg:w-9/12 md:w-9/12 sm:w-full mx-auto py-6">
        <livewire:order-form.form :payload="request()->agid" :referral="request()->ref" />
    </div>
</x-order-layout>
