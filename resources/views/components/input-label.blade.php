@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-graytext']) }}>
    {{ $value ?? $slot }}
</label>
