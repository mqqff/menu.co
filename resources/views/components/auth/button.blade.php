@props([
    'type' => 'submit'
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'bg-white text-primary rounded-full shadow-md text-sm font-bold hover:scale-105 transition cursor-pointer'
    ]) }}
>
    {{ $slot }}
</button>
