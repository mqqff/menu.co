@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="bg-[#faf8f5] min-h-screen px-8 md:px-28 py-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-10">
            <img src="https://i.pravatar.cc/150?img=12"
                 alt="Profile Picture"
                 class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md shrink-0">

            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-primary">Natasya Salsabila</h1>
                <p class="text-gray-500 text-sm mt-0.5">@natasyasalsabila</p>
            </div>

            <div class="flex-1">
                <p class="text-gray-600 text-sm font-semibold mb-2">Food Preference:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($prefs as $pref)
                        <span class="border border-gray-300 text-gray-700 text-xs font-semibold px-3 py-1 rounded-lg shadow-md">
                        {{ $pref }}
                    </span>
                    @endforeach
                </div>
            </div>

            <button class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition">
                <x-icons.three-dot class="w-5 h-5"/>
            </button>
        </div>

        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">My Recipe</h2>
                <a href="{{ route('recipes.my') }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($my_recipes as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => 1]) }}"
                       class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group block aspect-4/3">
                        <img src="{{ Storage::url($recipe->image_url) }}"
                             alt="{{ $recipe->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/65 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-white text-center">
                            <p class="font-bold text-lg leading-tight mb-1.5">{{ Str::limit($recipe->title, 26) }}</p>
                            <div class="flex items-center justify-center gap-3 text-sm text-white/85">
                                <span class="flex items-center gap-1">
                                    <x-icons.clock class="w-3 h-3" />
                                    {{ $recipe->cook_time }}
                                </span>
                                    <span class="flex items-center gap-1">
                                    <x-icons.user-group class="w-3 h-3" />
                                    {{ $recipe->servings }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">Saved Recipe</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($saved_recipes as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => 1]) }}"
                       class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group block aspect-4/3">
                        <img src="{{ $recipe->image_url }}"
                             alt="{{ $recipe->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/65 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-white text-center">
                            <p class="font-bold text-lg leading-tight mb-1.5">{{ Str::limit($recipe->title, 26) }}</p>
                            <div class="flex items-center justify-center gap-3 text-sm text-white/85">
                                <span class="flex items-center gap-1">
                                    <x-icons.clock class="w-3 h-3" />
                                    {{ $recipe->cook_time }}
                                </span>
                                    <span class="flex items-center gap-1">
                                    <x-icons.clock class="w-3 h-3" />
                                    {{ $recipe->servings }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection
