@extends('layouts.app')

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
                        src="{{ $recipe->image_url }}"
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
                            src="{{ $recipe->author->avatar_url }}"
                            alt="{{ $recipe->author->name }}"
                            class="w-10 h-10 rounded-full object-cover"
                        />
                        <div>
                            <p class="text-sm font-semibold text-black">{{ $recipe->author->name }}</p>
                            <p class="text-xs text-gray-700">{{ '@' . $recipe->author->username }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-gray-600">
                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $recipe->cook_time }}
                        </span>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            {{ $recipe->servings }}
                        </span>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                            </svg>
                            {{ $recipe->saves_count }}
                        </span>

                        <span class="flex items-center gap-2 border border-gray-200 py-1 px-1.5 rounded-lg shadow-sm font-medium">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= floor($recipe->rating) ? 'text-primary' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                            <span class="text-xs ml-1 text-gray-500">{{ number_format($recipe->rating, 1) }} stars</span>
                        </span>

                        <div class="relative" id="three-dot-recipe-wrapper">
                            <button
                                id="three-dot-recipe"
                                type="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                                class="text-gray-400 hover:text-gray-600 transition cursor-pointer"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="5" cy="12" r="2" />
                                    <circle cx="12" cy="12" r="2" />
                                    <circle cx="19" cy="12" r="2" />
                                </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-700 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                    </svg>
                                    <span class="font-medium">Share Recipe</span>
                                </button>

                                <div class="h-px bg-gray-100 mx-3"></div>

                                <button
                                    type="button"
                                    onclick="handleReport()"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-600 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
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

                    @foreach ($recipe->ingredient_groups as $group)
                        @if ($group['label'])
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-4 mb-2">
                                {{ $group['label'] }}
                            </p>
                        @endif

                        <ul class="space-y-2">
                            @foreach ($group['items'] as $item)
                                <li class="border-b border-[#EAE0D8] pb-1.5 text-sm text-gray-700">
                                    <span class="font-semibold whitespace-nowrap">{{ $item['amount'] }}</span>
                                    <span>{{ $item['name'] }}</span>
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
                                    <p class="text-sm text-gray-700 leading-relaxed flex-1">{{ $step['text'] }}</p>
                                </div>

                                @if (!empty($step['image']))
                                    <div class="ml-11 mt-3">
                                        <img
                                            src="{{ $step['image'] }}"
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
                                        src="{{ $comment->user->avatar_url }}"
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
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="19" cy="12" r="2" />
                                        </svg>
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
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-600 shrink-0">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                        <span class="font-semibold text-red-600">Delete</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button
                                                    type="button"
                                                    onclick="handleReportComment({{ $comment->id }})"
                                                    class="w-full flex items-centfer gap-3 px-4 py-3 text-sm hover:bg-red-50 transition-colors cursor-pointer"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-600 shrink-0">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                    </svg>
                                                    <span class="font-semibold text-red-600">Report Comment</span>
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center mt-2">
                                @php $commentRating = floor($comment->rating); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $commentRating ? 'text-primary' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-400 ml-1">{{ number_format($comment->rating, 1) }} stars</span>
                            </div>

                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                    @endforelse
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <img
                        src="{{ auth()->user()->avatar_url ?? asset('assets/images/user.jpg') }}"
                        alt="You"
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
                                    <svg class="w-5 h-5 transition-all duration-200 ease-in-out" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z" />
                                    </svg>
                                </label>
                            @endfor
                        </div>

                        <input
                            type="text"
                            name="body"
                            placeholder="Add comment"
                            class="flex-1 bg-white border border-[#EAE0D8] rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 placeholder-gray-400"
                        />
                        <button type="submit" class="shrink-0 text-primary hover:text-[#b84e22] transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </section>

            <section class="mt-14">
                <h2 class="text-2xl font-semibold text-primary mb-5">Similar Recipes</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($similar_recipes as $similar)
                        <a
                            href="#"
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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span>{{ $similar->cook_time }}</span>
                                    </div>
                                    <span>·</span>
                                    <div class="flex items-center justify-center gap-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
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
                    .then(() => alert('Link copied to clipboard!'));
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
