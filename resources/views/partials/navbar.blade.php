<nav class="w-full px-10 py-4 bg-primary">
    <div class="max-w-8xl mx-auto flex items-center gap-8">

        <a href="/" class="text-white text-2xl font-bold mr-4">Menu.Co</a>

        <a href="{{ route('recipes.trending') }}" class="font-semibold hover:text-white transition {{ request()->routeIs('recipes.trending') ? 'text-white' : 'text-white/80' }}">Trending</a>
        @auth <a href="{{ route('recipes.my') }}" class="font-semibold hover:text-white {{ request()->routeIs('recipes.my') ? 'text-white' : 'text-white/80' }} transition">Your Recipes</a>@endauth

        <div class="flex-1 mx-6">
            <form action="" method="GET">
                <div class="flex items-center bg-white rounded-full max-w-sm px-4 py-2 gap-2">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search"
                        class="flex-1 bg-transparent outline-none text-gray-500 text-sm"
                    />
                    <button type="submit">
                        <x-icons.magnifying-glass class="w-5 h-5 text-gray-500" />
                    </button>
                </div>
            </form>
        </div>

        <div class="ml-auto">
            @auth
                <div class="flex items-center gap-3">
                    <a href="{{ route('recipes.create') }}" class="flex items-center gap-2 bg-white hover:bg-gray-100 rounded-full px-5 py-2">
                        <x-icons.plus class="w-5 h-5 text-primary" />
                        <span class="text-primary font-semibold text-sm">Create a Recipe</span>
                    </a>

                    <div class="relative" id="profileDropdown">
                        <button
                            onclick="toggleDropdown()"
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-white focus:outline-none cursor-pointer"
                        >
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="User Image" class="w-full h-full object-cover" />
                        </button>

                        <div
                            id="dropdownMenu"
                            class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 transition-all duration-200 ease-out opacity-0 -translate-y-2 pointer-events-none"
                        >
                            <a href="{{ route('profile.show', ['user' => Auth::user()->username]) }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <x-icons.user class="w-5 h-5 text-gray-500" />
                                <span class="font-medium text-sm">Your Profile</span>
                            </a>

                            <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <x-icons.gear class="w-5 h-5 text-gray-500" />
                                <span class="font-medium text-sm">Settings</span>
                            </a>

                            <hr class="my-1 border-gray-200 mx-4" style="border-color: #D2714A; opacity: 0.4;" />

                            <form method="POST" action="{{ route('auth.logout') }}" id="logoutForm">
                                @csrf
                                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <x-icons.sign-out class="w-5 h-5 text-gray-500" />
                                    <span class="font-medium text-sm">Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @else
                <a href="{{ route('auth.login.form') }}" class="flex items-center gap-2 bg-white rounded-full px-5 py-2 hover:bg-gray-100 transition">
                    <x-icons.sign-in class="w-5 h-5 text-primary" />
                    <span class="text-primary font-semibold text-sm">Log In</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu');
        const isOpen = !menu.classList.contains('opacity-0');

        if (isOpen) {
            menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'translate-y-0');
        } else {
            menu.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
            menu.classList.add('opacity-100', 'translate-y-0');
        }
    }

    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('profileDropdown');
        const menu = document.getElementById('dropdownMenu');
        if (!dropdown.contains(e.target)) {
            menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'translate-y-0');
        }
    });

    const logoutForm = document.getElementById('logoutForm');
    logoutForm.querySelector('button').addEventListener('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out of your account.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "gray",
            confirmButtonText: "Yes, log me out!"
        }).then((result) => {
            if (result.isConfirmed) {
                logoutForm.submit();
            }
        });
    });
</script>
