<nav class="w-full px-4 sm:px-6 md:px-10 py-4 bg-primary">
    <div class="max-w-8xl mx-auto flex items-center gap-2 sm:gap-4 md:gap-8">

        <a href="{{ route('home') }}" class="text-white text-xl sm:text-2xl font-bold mr-2 sm:mr-4 shrink-0">Menu.Co</a>

        <div class="hidden lg:flex items-center gap-6 shrink-0">
            <a href="{{ route('recipes.trending') }}" class="font-semibold hover:text-white transition whitespace-nowrap {{ request()->routeIs('recipes.trending') ? 'text-white' : 'text-white/80' }}">Trending</a>
            @auth <a href="{{ route('recipes.my') }}" class="font-semibold hover:text-white whitespace-nowrap {{ request()->routeIs('recipes.my') ? 'text-white' : 'text-white/80' }} transition">Your Recipes</a>@endauth
        </div>

        <div class="hidden sm:block flex-1 min-w-0 mx-2 sm:mx-4">
            <form action="{{ route('recipes.search') }}" method="GET">
                <div class="flex items-center bg-white rounded-full w-full md:max-w-md lg:max-w-lg xl:max-w-xl px-3 md:px-4 py-1.5 md:py-2 gap-2 mx-auto md:mx-0">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search"
                        autocomplete="off"
                        class="flex-1 bg-transparent outline-none text-gray-500 text-xs sm:text-sm min-w-0"
                    />
                    <button type="submit" class="shrink-0">
                        <x-icons.magnifying-glass class="w-4 h-4 md:w-5 md:h-5 text-gray-500" />
                    </button>
                </div>
            </form>
        </div>

        <div class="flex items-center gap-2 ml-auto shrink-0">
            <div class="hidden md:flex items-center">
                @auth
                    <div class="flex items-center gap-3">
                        <a href="{{ route('recipes.create') }}" class="flex items-center gap-2 bg-white hover:bg-gray-100 rounded-full px-4 md:px-5 py-1.5 md:py-2 whitespace-nowrap">
                            <x-icons.plus class="w-4 h-4 md:w-5 md:h-5 text-primary" />
                            <span class="text-primary font-semibold text-xs md:text-sm hidden md:inline">Create a Recipe</span>
                        </a>

                        <div class="relative" id="profileDropdown">
                            <button
                                onclick="toggleDropdown()"
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full overflow-hidden border-2 border-white focus:outline-none cursor-pointer shrink-0"
                            >
                                <img src="{{ auth()->user()->avatar_url }}" alt="User Image" class="w-full h-full object-cover" />
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
                                    <button type="button" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                                        <x-icons.sign-out class="w-5 h-5 text-gray-500" />
                                        <span class="font-medium text-sm">Log Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @else
                    <a href="{{ route('auth.login.form') }}" class="flex items-center gap-2 bg-white rounded-full px-4 md:px-5 py-1.5 md:py-2 hover:bg-gray-100 transition whitespace-nowrap">
                        <x-icons.sign-in class="w-4 h-4 md:w-5 md:h-5 text-primary" />
                        <span class="text-primary font-semibold text-xs md:text-sm">Log In</span>
                    </a>
                @endauth
            </div>

            <button id="hamburgerBtn" class="lg:hidden flex items-center justify-center w-8 h-8 text-white cursor-pointer shrink-0" aria-label="Toggle menu">
            <svg id="hamburgerIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="mobileDrawer" class="fixed inset-0 z-50 hidden">
        <div id="drawerOverlay" class="absolute inset-0 bg-black/40"></div>
        <div class="absolute right-0 top-0 h-full w-72 max-w-[85vw] bg-primary shadow-2xl flex flex-col py-6 px-6 overflow-y-auto">
            <div class="flex justify-end mb-6">
                <button id="drawerCloseBtn" class="text-white/80 hover:text-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-5 text-white">
                <a href="{{ route('recipes.trending') }}" class="text-lg font-semibold {{ request()->routeIs('recipes.trending') ? 'text-white' : 'text-white/80' }}">Trending</a>
                @auth
                    <a href="{{ route('recipes.my') }}" class="text-lg font-semibold {{ request()->routeIs('recipes.my') ? 'text-white' : 'text-white/80' }}">Your Recipes</a>
                @endauth

                <hr class="border-white/20 my-2" />

                <form action="{{ route('recipes.search') }}" method="GET" class="mb-2">
                    <div class="flex items-center bg-white/20 rounded-full px-4 py-2 gap-2">
                        <input
                            type="text"
                            name="q"
                            placeholder="Search recipes..."
                            autocomplete="off"
                            class="flex-1 bg-transparent outline-none text-white text-sm placeholder:text-white/60 min-w-0"
                        />
                        <button type="submit" class="shrink-0 text-white/80">
                            <x-icons.magnifying-glass class="w-5 h-5" />
                        </button>
                    </div>
                </form>

                <hr class="border-white/20 my-2" />

                @auth
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ auth()->user()->avatar_url }}" alt="User" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                        <div>
                            <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/60">{{ '@'.auth()->user()->username }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.show', ['user' => Auth::user()->username]) }}" class="flex items-center gap-3 text-white/80 hover:text-white transition">
                        <x-icons.user class="w-5 h-5" />
                        <span>Your Profile</span>
                    </a>
                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 text-white/80 hover:text-white transition">
                        <x-icons.gear class="w-5 h-5" />
                        <span>Settings</span>
                    </a>
                    <a href="{{ route('recipes.create') }}" class="flex items-center gap-3 text-white/80 hover:text-white transition">
                        <x-icons.plus class="w-5 h-5" />
                        <span>Create a Recipe</span>
                    </a>

                    <hr class="border-white/20 my-2" />

                    <form method="POST" action="{{ route('auth.logout') }}" id="mobileLogoutForm">
                        @csrf
                        <button type="button" onclick="mobileLogout()" class="flex items-center gap-3 text-white/80 hover:text-white transition cursor-pointer w-full text-left">
                            <x-icons.sign-out class="w-5 h-5" />
                            <span>Log Out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.login.form') }}" class="flex items-center gap-3 text-white/80 hover:text-white transition">
                        <x-icons.sign-in class="w-5 h-5" />
                        <span>Log In</span>
                    </a>
                    <a href="{{ route('auth.register.form') }}" class="flex items-center gap-3 text-white/80 hover:text-white transition">
                        <x-icons.user class="w-5 h-5" />
                        <span>Register</span>
                    </a>
                @endauth
            </div>
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
        if (dropdown && !dropdown.contains(e.target)) {
            menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'translate-y-0');
        }
    });

    const logoutForm = document.getElementById('logoutForm');
    if (logoutForm) {
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
    }

    function mobileLogout() {
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
                document.getElementById('mobileLogoutForm').submit();
            }
        });
    }

    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const drawerCloseBtn = document.getElementById('drawerCloseBtn');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');

    function openDrawer() {
        mobileDrawer.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        hamburgerIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
    }

    function closeDrawer() {
        mobileDrawer.classList.add('hidden');
        document.body.style.overflow = '';
        hamburgerIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function() {
            if (mobileDrawer.classList.contains('hidden')) {
                openDrawer();
            } else {
                closeDrawer();
            }
        });
    }

    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', closeDrawer);
    }

    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', closeDrawer);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!mobileDrawer.classList.contains('hidden')) {
                closeDrawer();
            }
            const menu = document.getElementById('dropdownMenu');
            if (menu && !menu.classList.contains('opacity-0')) {
                menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
                menu.classList.remove('opacity-100', 'translate-y-0');
            }
        }
    });
</script>
