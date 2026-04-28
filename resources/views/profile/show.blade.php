@extends('layouts.app')

@section('title', $user->username.'\'s Profile')

@section('content')
    <div class="bg-[#faf8f5] min-h-screen px-8 md:px-28 py-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-10">
            <img src="{{ Storage::url($user->avatar) }}"
                 alt="Profile Picture"
                 class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md shrink-0">

            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-primary">{{ $user->name }}</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ "@".$user->username }}</p>
            </div>

            <div class="flex-1">
                <p class="text-gray-600 text-sm font-semibold mb-2">Food Preference:</p>
                <div class="flex flex-wrap gap-2">
                    @forelse ($user->preferences as $pref)
                        <a href="{{ route('recipes.byCategory', $pref->slug) }}"
                           class="border border-gray-300 text-gray-700 text-xs font-semibold px-3 py-1 rounded-lg shadow-md">
                            {{ $pref->name }}
                        </a>
                    @empty
                        <span class="text-gray-400 text-sm">
                            No preferences selected yet.
                        </span>
                    @endforelse
                </div>
            </div>

            <div class="relative" id="profile-dropdown-wrapper">
                <button
                    id="profile-three-dot"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition cursor-pointer"
                >
                    <x-icons.three-dot class="w-5 h-5"/>
                </button>

                <div
                    id="profile-dropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden py-1"
                >
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.settings') }}"
                           class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-800 hover:bg-gray-50 transition"
                        >
                            <x-icons.pencil class="w-5 h-5 text-gray-600 shrink-0" />
                            <span class="font-medium">Edit Profile</span>
                        </a>
                    @else
                        <form id="form-report-user" action="{{ route('profile.report', $user->id) }}" method="POST">
                            @csrf
                            <button
                                type="button"
                                id="report-user-btn"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-50 transition"
                            >
                                <x-icons.warning class="w-5 h-5 text-red-600 shrink-0" />
                                <span class="font-semibold text-red-600">Report User</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-primary">Created Recipes</h2>
                @if(count($created_recipes) > 0)
                    <a href="{{ route('profile.recipes', $user->username) }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse ($created_recipes as $recipe)
                    <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
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
                                    <x-icons.user-group class="w-3 h-3" />
                                    {{ $recipe->servings }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-400">
                        No recipes created yet.
                    </p>
                @endforelse
            </div>
        </section>

        @if(auth()->id() == $user->id)
            <section>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-primary">Saved Recipes</h2>
                    @if(count($saved_recipes) > 0)
                        <a href="{{ route('profile.bookmarks') }}" class="text-sm text-gray-500 hover:text-primary font-semibold transition-colors underline">See More</a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @forelse ($saved_recipes as $recipe)
                        <a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}"
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
                    @empty
                        <p class="text-gray-400">
                            No recipes saved yet.
                        </p>
                    @endforelse
                </div>
            </section>
        @endif
    </div>

    <script>
        const profileBtn = document.getElementById('profile-three-dot');
        const profileDropdown = document.getElementById('profile-dropdown');
        const profileWrapper = document.getElementById('profile-dropdown-wrapper');

        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();

            const isOpen = profileDropdown.classList.contains('show');

            profileDropdown.classList.toggle('hidden', isOpen);
            profileDropdown.classList.toggle('show', !isOpen);
        });

        document.addEventListener('click', (e) => {
            if (!profileWrapper.contains(e.target)) {
                profileDropdown.classList.remove('show');
                profileDropdown.classList.add('hidden');
            }
        });

        const reportBtn = document.getElementById('report-user-btn');
        reportBtn.addEventListener('click', () => {
            Swal.fire({
                title: "Report",
                text: "Please only report if it contains advertisements, nudity, hate speech or irrelevant content. Our team will review it shortly.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Yes, report"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-report-user').submit();
                }
            });
        });
    </script>
@endsection
