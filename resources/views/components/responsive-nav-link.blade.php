@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-gold text-start text-base font-medium text-gold bg-gold/10 focus:outline-none focus:text-gold focus:bg-white/10 focus:border-gold transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-gold hover:bg-white/5 hover:border-gold/50 focus:outline-none focus:text-gold focus:bg-white/5 focus:border-gold transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
