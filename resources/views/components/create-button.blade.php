<button {{ $attributes->merge(['class' => 'btn btn-primary btn-sm']) }}>
   <i class="bi bi-plus"></i> {{ $slot }}
</button>
