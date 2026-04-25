@props([
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null
])
@props(['error' => null])

<div>
    @if ($label)
        <label class="block text-sm text-primary mb-1 font-medium">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full bg-gray-200 rounded-lg px-4 py-2 outline-none'
        ]) }}
    >
    @if ($error)
        <p class="text-red-500 text-xs mt-1">{{ $error }}</p>
    @endif
</div>
