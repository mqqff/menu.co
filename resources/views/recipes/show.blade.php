@extends('layouts.app')

@section('title', $recipe->title)

@section('content')
    <style>
        .rating { direction: rtl; }

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

    <div class="bg-[#FEFAF6] min-h-screen pt-20 pb-32">
        <div class="max-w-6xl mx-auto px-5">
            <section class="flex flex-col md:flex-row gap-8 items-start">
                <div class="w-sm shrink-0">
                    <img
                        src="{{ $recipe->image_url }}"
                        alt="{{ $recipe->title }}"
                        class="w-full h-72 object-cover rounded-2xl shadow-md mb-7"
                    />

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

                </div>

                <div class="flex-1">

                    <h1 class="text-3xl md:text-4xl text-primary tracking-wide font-bold">
                        {{ $recipe->title }}
                    </h1>

                    <div class="flex items-center gap-2.5 mb-4 mt-5">
                        <img
                            src="{{ Storage::url($recipe->user->avatar) }}"
                            alt="{{ $recipe->user->name }}"
                            class="w-10 h-10 rounded-full object-cover border border-white"
                        >
                        <div>
                            <p class="text-md font-bold text-gray-800">{{ $recipe->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ '@'.$recipe->user->username }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mb-4 text-sm text-gray-600">

                    <span class="flex items-center gap-2 border border-gray-200 py-1 px-2 rounded-lg shadow-sm font-medium">
                        <x-icons.clock class="w-4 h-4 text-primary" />
                        {{ $recipe->cook_time }}
                    </span>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-2 rounded-lg shadow-sm font-medium">
                        <x-icons.user-group class="w-4 h-4 text-primary" />
                        {{ $recipe->servings }}
                    </span>

                        <form action="{{ route('bookmarks.toggle', $recipe->id) }}" method="POST">
                            @csrf
                            <button class="flex items-center gap-2 border border-gray-200 py-1 px-2 rounded-lg shadow-sm font-medium">
                                <x-icons.bookmark
                                    class="w-4 h-4 {{ $isBookmarked ? 'fill-primary text-primary' : 'text-primary fill-none' }}"
                                />
                                {{ $recipe->bookmarks_count ?? 0 }}
                            </button>
                        </form>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-2 rounded-lg shadow-sm font-medium">
                        @php $rating = $recipe->ratings_avg_value ?? 0; @endphp

                            @for ($i = 1; $i <= 5; $i++)
                                <x-icons.star class="w-4 h-4 {{ $i <= floor($rating) ? 'text-primary' : 'text-gray-300' }}" />
                            @endfor

                        <span>{{ number_format($rating, 1) }}</span>
                    </span>

                        <div class="relative" id="three-dot-recipe-wrapper">
                            <button id="three-dot-recipe" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                <x-icons.three-dot class="w-4 h-4" />
                            </button>

                            <div id="recipe-dropdown"
                                 class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 py-1">

                                <button
                                    type="button"
                                    onclick="handleShare()"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-800 hover:bg-gray-50 transition-colors cursor-pointer"
                                >
                                    <x-icons.plane class="w-5 h-5 text-gray-600 shrink-0" />
                                    <span class="font-medium">Share Recipe</span>
                                </button>

                                @if(auth()->id() === $recipe->user_id)
                                    <a href="{{ route('recipes.edit', ['recipe' => $recipe->id]) }}"
                                       class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-800 hover:bg-gray-50 transition-colors cursor-pointer"
                                    >
                                        <x-icons.pencil class="w-5 h-5 text-gray-600 shrink-0" />
                                        <span class="font-medium">Edit Recipe</span>
                                    </a>
                                @endif

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

                    <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                        {{ $recipe->description }}
                    </p>

                    <h2 class="text-2xl font-extrabold text-orange mb-3.5">Steps</h2>

                    <ol class="space-y-6">
                        @foreach ($recipe->steps as $index => $step)
                            <li>
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange text-white flex items-center justify-center font-bold shrink-0">
                                        {{ $index + 1 }}
                                    </div>

                                    <p class="text-sm text-gray-700 leading-relaxed flex-1">
                                        {{ $step->text }}
                                    </p>
                                </div>

                                @if ($step->image)
                                    <img
                                        src="{{ $step->image_url }}"
                                        class="ml-11 mt-3 rounded-xl w-full max-w-xs object-cover shadow-sm"
                                    >
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    @if ($recipe->tips)
                        <div class="mt-6">
                            <h2 class="text-2xl font-extrabold text-orange mb-3.5">Tips</h2>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $recipe->tips }}</p>
                        </div>
                    @endif

                </div>
            </section>

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

                                <div class="relative comment-wrapper">
                                    <button
                                        type="button"
                                        class="comment-toggle-btn text-gray-400 hover:text-gray-600 transition cursor-pointer"
                                    >
                                        <x-icons.three-dot class="w-4 h-4" />
                                    </button>

                                    <div
                                        class="comment-dropdown hidden absolute right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden py-1"
                                        style="min-width: 220px;"
                                    >
                                        <button
                                            type="button"
                                            class="edit-btn w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 transition-colors cursor-pointer"
                                            data-id="{{ $comment->id }}"
                                            data-content="{{ $comment->content }}"
                                            data-rating="{{ $comment->rating->value ?? 0 }}"
                                        >
                                            <x-icons.pencil class="w-5 h-5 text-gray-600 shrink-0" />
                                            <span class="font-medium">Edit</span>
                                        </button>
                                        @if (auth()->check() && auth()->id() == $comment->user_id)
                                            <form method="POST" action="{{ route('comments.destroy', $comment->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    class="delete-btn w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                                >
                                                    <x-icons.trash class="w-5 h-5 text-red-600 shrink-0" />
                                                    <span class="font-semibold text-red-600">Delete</span>
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                class="report-btn w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                            >
                                                <x-icons.warning class="w-5 h-5 text-red-600 shrink-0" />
                                                <span class="font-semibold text-red-600">Report Comment</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @php $commentRating = $comment->rating->value ?? 0; @endphp

                            <div class="flex items-center mt-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icons.star class="w-4 h-4 {{ $i <= $commentRating ? 'text-primary' : 'text-gray-300' }}" />
                                @endfor
                            </div>

                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                    @endforelse
                </div>

                @auth
                    <div class="mt-5 flex items-center gap-3">
                        <img
                            src="{{ Storage::url($recipe->user->avatar) }}"
                            alt="{{ $recipe->user->name }}"
                            class="w-9 h-9 rounded-full object-cover shrink-0"
                        />
                        <form action="{{ route('comments.store', $recipe->id) }}" method="POST" id="comment-form"
                              class="flex-1 flex items-center gap-2 {{ $hasReviewed ? 'opacity-50 pointer-events-none' : '' }}">
                            @csrf

                            <div class="rating flex justify-end gap-1 mr-2">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input
                                        type="radio"
                                        id="star{{ $i }}"
                                        name="rating"
                                        value="{{ $i }}"
                                        class="hidden peer rating-input"
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
                                id="comment-content"
                                name="content"
                                placeholder="Add Review"
                                autocomplete="off"
                                class="flex-1 bg-white border border-[#EAE0D8] rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 placeholder-gray-400"
                            />
                            <button type="submit" class="shrink-0 text-primary! hover:text-[#b84e22] transition cursor-pointer">
                                <x-icons.plane class="w-6 h-6" />
                            </button>
                        </form>
                    </div>
                @endauth
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
                                src="{{ $similar->image_url }}"
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

        document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const wrapper = this.closest('.comment-wrapper');
                const dropdown = wrapper.querySelector('.comment-dropdown');

                document.querySelectorAll('.comment-dropdown').forEach(el => {
                    if (el !== dropdown) el.classList.add('hidden');
                });

                dropdown.classList.toggle('hidden');
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.comment-dropdown').forEach(el => {
                el.classList.add('hidden');
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const form = this.closest('form');

                this.closest('.comment-wrapper')
                    .querySelector('.comment-dropdown')
                    .classList.add('hidden');
                Swal.fire({
                    title: "Are you sure?",
                    text: "This comment will be permanently deleted.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Yes, delete it"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.report-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                this.closest('.comment-wrapper')
                    .querySelector('.comment-dropdown')
                    .classList.add('hidden');
                Swal.fire({
                    title: "Report this comment?",
                    text: "We will review this content.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Yes, report"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // TODO: route report
                        console.log('Reported');
                    }
                });
            });
        });

        const commentForm = document.getElementById('comment-form');
        commentForm.addEventListener('submit', function(e) {
            const content = this.querySelector('input[name="content"]').value.trim();
            const rating = this.querySelector('input[name="rating"]:checked');

            if (!content || !rating) {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Form',
                    text: 'Please fill in both content and rating.'
                });
            }
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();

                const content = this.dataset.content;
                const rating  = this.dataset.rating;

                commentForm.classList.remove('pointer-events-none', 'opacity-50');

                document.getElementById('comment-content').value = content;

                document.querySelectorAll('.rating-input').forEach(input => {
                    input.checked = false;
                });

                const selected = document.querySelector(`.rating-input[value="${rating}"]`);
                if (selected) selected.checked = true;

                this.closest('.comment-dropdown').classList.add('hidden');

                document.getElementById('comment-form').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        });
    </script>
@endsection
