@extends('layouts.app')

@section('title', $recipe->title)

@section('content')
    <style>
        .rating {
            direction: rtl;
        }

        .rating input:checked ~ label svg,
        .rating label:hover ~ label svg,
        .rating label:hover svg {
            color: #D97706;
        }

        #recipe-dropdown {
            transform-origin: top right;
            transition: opacity 150ms ease, transform 150ms ease, visibility 150ms;
        }

        #recipe-dropdown.hidden {
            opacity: 0;
            transform: scale(0.95);
            visibility: hidden;
            pointer-events: none;
        }

        #recipe-dropdown.show {
            opacity: 1;
            transform: scale(1);
            visibility: visible;
            pointer-events: auto;
        }
    </style>

    <div class="bg-[#FEFAF6] min-h-screen pt-20 pb-32 px-4">
        <div class="max-w-5xl mx-auto">

            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <div class="shrink-0 w-full md:w-72">
                    <img
                        src="{{ Storage::url($recipe->image) }}"
                        alt="{{ $recipe->title }}"
                        class="w-full h-64 md:h-72 object-cover rounded-2xl shadow-md"
                    />
                </div>

                <div class="flex flex-col justify-start gap-3 pt-1">
                    <h1 class="text-3xl md:text-4xl text-primary tracking-wide font-semibold">
                        {{ $recipe->title }}
                    </h1>

                    <div class="flex items-center gap-3 mt-3">
                        <img
                            src="{{ Storage::url($recipe->user->avatar) }}"
                            alt="{{ $recipe->user->name }}"
                            class="w-10 h-10 rounded-full object-cover"
                        />
                        <div>
                            <p class="text-sm font-semibold text-black">{{ $recipe->user->name }}</p>
                            <p class="text-xs text-gray-700">{{ '@' . $recipe->user->username }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-gray-600">
                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            <x-icons.clock class="w-4 h-4 text-primary" />
                            {{ $recipe->cook_time }}
                        </span>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            <x-icons.user-group class="w-4 h-4 text-primary" />
                            {{ $recipe->servings }}
                        </span>

                        <form action="{{ route('bookmarks.toggle', $recipe->id) }}" method="POST">
                            @csrf

                            <button
                                type="submit"
                                class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium cursor-pointer"
                            >
                                <x-icons.bookmark
                                    class="w-4 h-4 {{ $isBookmarked ? 'fill-primary text-primary' : 'text-primary fill-none' }}"
                                />
                                {{ $recipe->bookmarks_count ?? 0 }}
                            </button>
                        </form>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            @php $rating = $recipe->ratings_avg_value ?? 0; @endphp

                            @for ($i = 1; $i <= 5; $i++)
                                <x-icons.star class="w-4 h-4 {{ $i <= floor($rating) ? 'text-primary' : 'text-gray-300' }}" />
                            @endfor

                            <span>{{ number_format($rating, 1) }} stars</span>
                        </span>

                        <div class="relative" id="three-dot-recipe-wrapper">
                            <button
                                id="three-dot-recipe"
                                type="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                                class="text-gray-400 hover:text-gray-600 transition cursor-pointer"
                            >
                                <x-icons.three-dot class="w-4 h-4" />
                            </button>

                            <div
                                id="recipe-dropdown"
                                class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden py-1"
                            >
                                <button
                                    type="button"
                                    onclick="handleShare()"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-800 hover:bg-gray-50 transition-colors cursor-pointer"
                                >
                                    <x-icons.plane class="w-5 h-5 text-gray-600 shrink-0" />
                                    <span class="font-medium">Share Recipe</span>
                                </button>

                                <a href="{{ route('recipes.edit', ['recipe' => $recipe->id]) }}"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-800 hover:bg-gray-50 transition-colors cursor-pointer"
                                >
                                    <x-icons.pencil class="w-5 h-5 text-gray-600 shrink-0" />
                                    <span class="font-medium">Edit Recipe</span>
                                </a>

                                <div class="h-px bg-gray-100 mx-3"></div>

                                <button
                                    type="button"
                                    onclick="handleReport()"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                >
                                    <x-icons.warning class="w-5 h-5 text-red-600 shrink-0" />
                                    <span class="font-semibold text-red-600">Report Recipe</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed mt-1">
                        {{ $recipe->description }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[289px_1fr] gap-8">
                <aside>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Ingredients</h2>

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
                </aside>

                <main>
                    <h2 class="text-2xl font-semibold text-primary mb-5">Steps</h2>

                    <ol class="space-y-6">
                        @foreach ($recipe->steps as $index => $step)
                            <li>
                                <div class="flex gap-4 items-start">
                                    <div class="shrink-0 w-8 h-8 mt-1 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed flex-1">{{ $step->text }}</p>
                                </div>

                                @if (!empty($step->image))
                                    <div class="ml-11 mt-3">
                                        <img
                                            src="{{ Storage::url($step->image) }}"
                                            alt="Step {{ $index + 1 }}"
                                            class="rounded-xl w-full max-w-xs object-cover shadow-sm"
                                        />
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    @if (!empty($recipe->tips))
                        <div class="mt-10">
                            <h2 class="text-2xl font-semibold text-primary mb-2">Tips</h2>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $recipe->tips }}</p>
                        </div>
                    @endif
                </main>
            </div>

            <section class="mt-12">
                <h2 class="text-2xl font-semibold text-primary mb-5">Comments</h2>

                <div class="space-y-5">
                    @forelse ($recipe->comments as $comment)
                        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAE0D8]">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ Storage::url($comment->user->avatar) }}"
                                        alt="{{ $comment->user->name }}"
                                        class="w-14 h-14 rounded-full object-cover"
                                    />
                                    <div>
                                        <p class="text-md font-bold text-gray-800">{{ $comment->user->name }}</p>
                                        <p class="text-sm text-gray-400">{{ '@' . $comment->user->username }}</p>
                                    </div>
                                </div>

                                <div class="relative" id="comment-wrapper-{{ $comment->id }}">
                                    <button
                                        type="button"
                                        onclick="toggleCommentDropdown({{ $comment->id }})"
                                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer"
                                    >
                                        <x-icons.three-dot class="w-4 h-4" />
                                    </button>

                                    <div
                                        id="comment-dropdown-{{ $comment->id }}"
                                        class="hidden absolute right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden py-1"
                                        style="min-width: 220px;"
                                    >
                                        @auth
                                            @if (auth()->id() === $comment->user->id)
                                                <form method="POST" action="">
                                                    <button
                                                        type="submit"
                                                        class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                                    >
                                                        <x-icons.trash class="w-5 h-5 text-red-600 shrink-0" />
                                                        <span class="font-semibold text-red-600">Delete</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button
                                                    type="button"
                                                    onclick="handleReportComment({{ $comment->id }})"
                                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                                >
                                                    <x-icons.warning class="w-5 h-5 text-red-600 shrink-0" />
                                                    <span class="font-semibold text-red-600">Report Comment</span>
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            @php $commentRating = $comment->rating->value ?? 0; @endphp

                            <div class="flex items-center mt-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icons.star class="w-4 h-4 {{ $i <= $commentRating ? 'text-primary' : 'text-gray-300' }}" />
                                @endfor

                                <span class="text-xs text-gray-400 ml-1">
                                    {{ number_format($commentRating, 1) }} stars
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                    @endforelse
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <img
                        src="{{ Storage::url($recipe->user->avatar) }}"
                        alt="{{ $recipe->user->name }}"
                        class="w-9 h-9 rounded-full object-cover shrink-0"
                    />
                    <form action="#" method="POST" class="flex-1 flex items-center gap-2">
                        @csrf

                        <div class="rating flex justify-end gap-1 mr-2">
                            @for ($i = 5; $i >= 1; $i--)
                                <input
                                    type="radio"
                                    id="star{{ $i }}"
                                    name="rating"
                                    value="{{ $i }}"
                                    class="hidden peer"
                                />
                                <label
                                    for="star{{ $i }}"
                                    class="cursor-pointer text-gray-300 peer-checked:text-primary hover:text-primary transition"
                                >
                                    <x-icons.star class="w-5 h-5" />
                                </label>
                            @endfor
                        </div>

                        <input
                            type="text"
                            name="content"
                            placeholder="Add comment"
                            class="flex-1 bg-white border border-[#EAE0D8] rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 placeholder-gray-400"
                        />
                        <button type="submit" class="shrink-0 text-primary hover:text-[#b84e22] transition cursor-pointer">
                            <x-icons.plane class="w-6 h-6" />
                        </button>
                    </form>
                </div>
            </section>

            <section class="mt-14">
                <h2 class="text-2xl font-semibold text-primary mb-5">Similar Recipes</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($similar_recipes as $similar)
                        <a
                            href="{{ route('recipes.show', $similar->id) }}"
                            class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow aspect-square block"
                        >
                            <img
                                src="{{ Storage::url($similar->image) }}"
                                alt="{{ $similar->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                <p class="text-white text-md font-bold text-center leading-tight line-clamp-2">{{ $similar->title }}</p>
                                <div class="flex items-center justify-center gap-2 mt-1 text-white/70 text-xs">
                                    <div class="flex items-center gap-x-1 justify-center">
                                        <x-icons.clock class="w-4 h-4" />
                                        <span>{{ $similar->cook_time }}</span>
                                    </div>
                                    <span>·</span>
                                    <div class="flex items-center justify-center gap-x-1">
                                        <x-icons.user-group class="w-4 h-4" />
                                        <span>{{ $similar->servings }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

        </div>
    </div>

    <script>
        const btn     = document.getElementById('three-dot-recipe');
        const dropdown = document.getElementById('recipe-dropdown');
        const wrapper  = document.getElementById('three-dot-recipe-wrapper');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('show');
            dropdown.classList.toggle('hidden', isOpen);
            dropdown.classList.toggle('show', !isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.remove('show');
                dropdown.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        function handleShare() {
            closeDropdown();
            if (navigator.share) {
                navigator.share({ title: document.title, url: window.location.href });
            } else {
                navigator.clipboard.writeText(window.location.href)
                    .then(() => {
                        Swal.fire({
                            title: "Link Copied!",
                            text: "The recipe URL has been copied to your clipboard.",
                            icon: "success"
                        });
                    });
            }
        }

        function handleReport() {
            closeDropdown();
            window.location.href = "";
        }

        function closeDropdown() {
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }

        function toggleCommentDropdown(id) {
            document.querySelectorAll('[id^="comment-dropdown-"]').forEach(el => {
                if (el.id !== `comment-dropdown-${id}`) {
                    el.classList.add('hidden');
                }
            });

            const dropdown = document.getElementById(`comment-dropdown-${id}`);
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('[id^="comment-wrapper-"]').forEach(wrapper => {
                if (!wrapper.contains(e.target)) {
                    const id = wrapper.id.replace('comment-wrapper-', '');
                    const dropdown = document.getElementById(`comment-dropdown-${id}`);
                    if (dropdown) dropdown.classList.add('hidden');
                }
            });
        });

        function handleReportComment(id) {
            document.getElementById(`comment-dropdown-${id}`).classList.add('hidden');
            window.location.href = "";
        }
    </script>
@endsection
