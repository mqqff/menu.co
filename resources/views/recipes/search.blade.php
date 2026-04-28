@extends('layouts.app')

@section('title', request('q') . ' - Search Results')

@section('content')
    <div class="min-h-screen bg-[#faf8f5] px-8 py-10">

        <section class="mb-12">
            <h2 class="text-2xl font-bold text-primary mb-6">
                {{ request('q') }}
                <span class="text-gray-400 font-semibold">({{ $recipes->count() }})</span>
            </h2>

            @forelse ($recipes as $recipe)
                @if ($loop->first)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @endif

                        <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
                           class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 min-h-55 block group">

                            <img src="{{ $recipe->image_url }}"
                                 alt="{{ $recipe->title }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>

                            <div class="absolute bottom-0 left-0 right-0 p-4 text-white text-center">
                                <p class="font-bold text-md leading-tight mb-2 drop-shadow">
                                    {{ Str::limit($recipe->title, 30) }}
                                </p>

                                <div class="flex items-center justify-center gap-3 text-xs text-white/90">
                            <span class="flex items-center gap-1">
                                <x-icons.bookmark class="w-5 h-5" />
                                {{ $recipe->cook_time }}
                            </span>

                                    <span class="flex items-center gap-1">
                                <x-icons.star class="w-5 h-5" />
                                {{ $recipe->servings }} servings
                            </span>
                                </div>
                            </div>
                        </a>

                        @if ($loop->last)
                    </div>
                @endif

            @empty
                <div class="flex flex-col items-center justify-center text-center py-14">
                    <p class="text-xl font-semibold text-gray-600 mb-2">
                        No results found
                    </p>
                    <p class="text-gray-400">
                        Please try another keyword or check your spelling
                    </p>
                </div>
            @endforelse

        </section>

    </div>
@endsection
