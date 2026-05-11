@extends('layouts.app')

@section('title', 'Menu.co - Home')

@section('content')
<div class="bg-[#faf8f5]">
    <div class="relative bg-primary overflow-hidden" id="hero-slider">
        <div class="relative h-85 md:h-95">
            <div class="hero-slide absolute inset-0 px-10 md:px-16 flex flex-col justify-center transition-opacity duration-700 opacity-100 z-10">
                <div class="absolute right-8 top-6">
                    <x-auth.egg />
                </div>
                <div class="absolute -left-12 -bottom-24 opacity-70">
                    <x-auth.lettuce />
                </div>
                <p class="text-white/90 font-semibold text-3xl mb-1">Menu.co Monthly Event!</p>
                <h1 class="text-white font-black text-4xl md:text-8xl leading-tight mb-4">#MenuCoAprilicious</h1>
                <p class="text-white text-md max-w-lg leading-relaxed z-50">
                    Share your most delicious dish and share it on your Instagram with the hashtag to win
                    for each category <strong>Rp.200.000!!</strong>
                </p>
            </div>

            <div class="hero-slide absolute inset-0 px-10 md:px-16 flex flex-col justify-center transition-opacity duration-700 opacity-0 z-0">
                <div class="absolute right-12 top-8 w-32 h-32 rounded-full bg-[#f5e9c8]/60"></div>
                <p class="text-white/90 font-semibold text-3xl mb-1">New This Week</p>
                <h1 class="text-white font-black text-4xl md:text-8xl leading-tight mb-4">#FreshFromKitchen</h1>
                <p class="text-white/85 text-md max-w-lg leading-relaxed">
                    Discover the newest recipes added by our community every week. Get inspired and start cooking today!
                </p>
            </div>

            <div class="hero-slide absolute inset-0 px-10 md:px-16 flex flex-col justify-center transition-opacity duration-700 opacity-0 z-0">
                <div class="absolute right-8 top-6   ">
                    <x-auth.egg />
                </div>
                <p class="text-white/90 font-semibold text-3xl mb-1">Community Spotlight</p>
                <h1 class="text-white font-black text-4xl md:text-8xl leading-tight mb-4">#MenuCoChefs</h1>
                <p class="text-white/85 text-md max-w-lg leading-relaxed">
                    Our community is growing! Share your secret recipes and become a featured chef on Menu.co.
                </p>
            </div>
        </div>

        <div class="absolute bottom-5 left-0 right-0 flex justify-center gap-2 z-20" id="hero-dots">
            <button data-index="0" class="hero-dot bg-white"></button>
            <button data-index="1" class="hero-dot bg-white/40"></button>
            <button data-index="2" class="hero-dot bg-white/40"></button>
        </div>
    </div>

    <div class="px-6 md:px-10 py-10 space-y-12">
        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">Trending Category</h2>
                @if(count($trending_categories) > 0)
                    <a href="{{ route('recipes.trending.categories') }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
                @endif
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide scroll-smooth" x-ref="track">
                @forelse ($trending_categories as $category)
                    <a href="{{ route('recipes.byCategory', $category->slug) }}"
                       class="relative shrink-0 w-80 h-72 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group block">
                        <img src="{{ $category->image_url }}"
                             alt="{{ $category->name }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/10 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-white font-bold text-3xl drop-shadow-md">{{ $category->name }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 italic">No trending categories at the moment. Check back later!</p>
                @endforelse
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">Trending Recipes</h2>
                @if(count($trending_recipes) > 0)
                    <a href="{{ route('recipes.trending') }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
                @endif
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                @forelse ($trending_recipes as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
                       class="relative shrink-0 w-80 h-72 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group block">
                        <img src="{{ $recipe->image_url }}"
                             alt="{{ $recipe->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/15 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-white text-center">
                            <p class="font-bold text-xl leading-tight mb-1.5">{{ Str::limit($recipe->title, 28) }}</p>
                            <div class="flex items-center justify-center gap-3 text-sm text-white/85">
                                <span class="flex items-center gap-1">
                                    <x-icons.clock class="w-4 h-4" />
                                    {{ $recipe->cook_time }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icons.user-group class="w-4 h-4" />
                                    {{ $recipe->servings }} servings
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 italic">No trending recipes at the moment. Check back later!</p>
                @endforelse
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">Recently Added</h2>
                @if(count($recently_added) > 0)
                    <a href="{{ route('recipes.recent') }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
                @endif
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                @forelse ($recently_added as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
                       class="relative shrink-0 w-80 h-72 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group block">
                        <img src="{{ $recipe->image_url }}"
                             alt="{{ $recipe->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/15 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-white text-center">
                            <p class="font-bold text-xl leading-tight mb-1.5">{{ Str::limit($recipe->title, 28) }}</p>
                            <div class="flex items-center justify-center gap-3 text-sm text-white/85">
                                <span class="flex items-center gap-1">
                                    <x-icons.clock class="w-4 h-4" />
                                    {{ $recipe->cook_time }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icons.user-group class="w-4 h-4" />
                                    {{ $recipe->servings }} servings
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 italic">No recently added recipes at the moment. Check back later!</p>
                @endforelse
            </div>
        </section>

    </div>
</div>

@push('scripts')
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('.hero-slide');
            const dots   = document.querySelectorAll('.hero-dot');
            let current  = 0;
            let timer;

            function goTo(index) {
                slides.forEach(function (s) {
                    s.classList.remove('opacity-100', 'z-10');
                    s.classList.add('opacity-0', 'z-0');
                });

                dots.forEach(function (d) {
                    d.classList.remove('bg-white');
                    d.classList.add('bg-white/40');
                });

                slides[index].classList.remove('opacity-0', 'z-0');
                slides[index].classList.add('opacity-100', 'z-10');
                dots[index].classList.remove('bg-white/40');
                dots[index].classList.add('bg-white');

                current = index;
            }

            function startAutoplay() {
                timer = setInterval(function () {
                    goTo((current + 1) % slides.length);
                }, 10000);
            }

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    clearInterval(timer);
                    goTo(parseInt(dot.dataset.index));
                    startAutoplay();
                });
            });

            startAutoplay();
        });
    </script>
@endpush
@endsection
