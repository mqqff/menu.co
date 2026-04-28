@extends('layouts.app')

@section('title', 'Trending Category')

@section('content')
    <div class="min-h-screen bg-[#faf8f5] px-8 py-10">

        <h2 class="text-2xl font-bold text-primary mb-7">
            Trending Category
            <span class="text-gray-400 font-semibold">({{ $categories->count() }})</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
            @foreach ($categories as $category)
                <a href="#"
                   class="relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300 group
                          aspect-4/3 block">

                    <img src="{{ $category->image_url }}"
                         alt="{{ $category->name }}"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    <div class="absolute inset-0 bg-linear-to-t from-black/55 via-black/10 to-transparent"></div>

                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white font-bold text-2xl drop-shadow-md tracking-wide">
                            {{ $category->name }}
                        </span>
                    </div>

                </a>
            @endforeach
        </div>

    </div>
@endsection
