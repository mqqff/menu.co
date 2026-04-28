@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => ''
])

<div>
    @if ($label)
        <label class="block text-sm text-primary mb-1 font-medium">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        value="{{ $value }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full bg-gray-200 rounded-lg px-4 py-2 outline-none'
        ]) }}
    >
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
