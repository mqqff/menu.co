@extends('layouts.app')

@section('title', 'My Recipes')

@section('content')
    @php
        $published = $recipes->where('status', 'published');
        $drafts = $recipes->where('status', 'draft');
    @endphp

    <div class="min-h-screen bg-[#faf8f5] px-8 py-10">

        <section class="mb-12">
            <h2 class="text-2xl font-bold text-[#c0522a] mb-6">
                My Recipes
                <span class="text-gray-400 font-semibold">({{ $published->count() }})</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">

                <a href="{{ route('recipes.create') }}"
                   class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-gray-300 bg-white
                      min-h-55 text-[#c0522a] hover:border-[#c0522a] hover:bg-orange-50
                      transition-all duration-200 cursor-pointer group">
                    <div class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-orange-100 flex items-center justify-center transition-colors duration-200">
                        <x-icons.plus class="w-6 h-6" />
                    </div>
                    <span class="font-semibold text-md">Add New Recipe</span>
                </a>

                @foreach ($published as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
                       class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 min-h-55 block group">
                        <img src="{{ $recipe->image_url }}"
                             alt="{{ $recipe->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white text-center">
                            <p class="font-bold text-md leading-tight mb-2 drop-shadow">{{ Str::limit($recipe->title, 30) }}</p>
                            <div class="flex items-center justify-center gap-3 text-xs text-white/90">
                                <span class="flex items-center gap-1">
                                <x-icons.bookmark class="w-5 h-5" />
                                {{ $recipe->saves_count }}
                            </span>
                                <span class="flex items-center gap-1">
                                <x-icons.star class="w-5 h-5" />
                                {{ $recipe->rating }}
                            </span>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
        </section>

        @if ($drafts->isNotEmpty())
            <section>
                <h2 class="text-2xl font-bold text-[#c0522a] mb-6">
                    Draft
                    <span class="text-gray-400 font-semibold">({{ $drafts->count() }})</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach ($drafts as $recipe)
                        <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
                           class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 min-h-55 block group">
                            <img src="{{ $recipe->image_url }}"
                                 alt="{{ $recipe->title }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                <p class="font-bold text-md leading-tight mb-2 drop-shadow text-center">
                                    {{ Str::limit($recipe->title, 22) }}
                                </p>
                                <div class="flex items-center justify-center gap-4 text-xs text-white/90">
                                    <span class="flex items-center gap-1">
                                        <x-icons.clock class="w-5 h-5" />
                                        {{ $recipe->cook_time }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-icons.user-group class="w-5 h-5" />
                                        {{ $recipe->servings }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
