@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-navy focus:ring-navy rounded-md shadow-sm']) }}>
