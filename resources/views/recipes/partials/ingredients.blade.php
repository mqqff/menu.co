<h2 class="text-2xl font-extrabold text-orange mb-3.5">Ingredients</h2>

@foreach ($recipe->ingredientGroups as $group)
    @if ($group->label)
        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-4 mb-2">
            {{ $group->label }}
        </p>
    @endif

    <ul class="space-y-2">
        @foreach ($group->ingredients as $item)
            <li class="border-b border-[#EAE0D8] pb-1.5 text-sm text-gray-700">
                <span class="font-semibold whitespace-nowrap">{{ $item->amount }}</span>
                <span>{{ $item->name }}</span>
            </li>
        @endforeach
    </ul>
@endforeach
