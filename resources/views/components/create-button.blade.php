<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-800 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    <i class="bi bi-plus"></i> {{ $slot }}
</button>
