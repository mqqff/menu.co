@props([
    'name',
    'label',
    'type'        => 'text',
    'placeholder' => '',
    'value'       => '',
    'showToggle'  => false,
    'autofocus' => false,
    'required' => false,
])

<div class="flex flex-col gap-1">
    <label for="{{ $name }}"
           class="text-sm font-semibold text-[#FDE8D0]">
        {{ $label }}
    </label>

    <div class="relative">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            value="{{ $type !== 'password' ? $value : '' }}"
            autocomplete="{{ $name }}"
            {{ $autofocus ? 'autofocus' : '' }}
            {{ $required ? 'required' : '' }}
            class="w-full rounded-full bg-white/95 text-gray-500 placeholder-gray-400
                   text-sm px-5 py-3 outline-none focus:ring-2 focus:ring-white/60
                   transition-all duration-200
                   {{ $errors->has($name) ? 'border-red-400' : 'border-transparent' }}
                   {{ $showToggle ? 'pr-12' : '' }}"
            {{ $showToggle ? 'x-ref="input_' . $name . '"' : '' }}
        />
        @error($name)
            <p class="text-red-700 font-semibold text-xs mt-2">{{ $message }}</p>
        @enderror

        @if ($showToggle)
            <button
                type="button"
                onclick="togglePassword('{{ $name }}')"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400
                       hover:text-gray-600 transition-colors focus:outline-none"
                aria-label="Toggle password visibility">
                <svg id="eye-icon-{{ $name }}" xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5
                             c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7
                             -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        @endif
    </div>

</div>

@once
    @push('scripts')
    <script>
        function togglePassword(name) {
            const input   = document.getElementById(name);
            const icon    = document.getElementById('eye-icon-' + name);
            const isPass  = input.type === 'password';

            input.type = isPass ? 'text' : 'password';

            icon.innerHTML = isPass
                ? `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                            a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                            M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
                            m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                            m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                            a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                   <path stroke-linecap="round" stroke-linejoin="round"
                         d="M2.458 12C3.732 7.943 7.523 5 12 5
                            c4.478 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.064 7-9.542 7
                            -4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    </script>
    @endpush
@endonce
