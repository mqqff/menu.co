<nav class="w-full px-10 py-4 bg-primary">
    <div class="max-w-7xl mx-auto flex items-center gap-8">

        <a href="/" class="text-white text-2xl font-bold mr-4">Menu.Co</a>

        <a href="#" class="text-white/80 font-semibold hover:text-white transition">Trending</a>
        <a href="#" class="text-white/80 font-semibold hover:text-white transition">Your Recipe</a>

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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="ml-auto">
            @auth
                <div class="flex items-center gap-3">
                    <a href="" class="flex items-center gap-2 bg-white hover:bg-gray-100 rounded-full px-5 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span class="text-primary font-semibold text-sm">Create a Recipe</span>
                    </a>

                    <div class="relative" id="profileDropdown">
                        <button
                            onclick="toggleDropdown()"
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-white focus:outline-none cursor-pointer"
                        >
                            <img src="{{ asset('assets/images/user.jpg') }}" alt="User Image" class="w-full h-full object-cover" />
                        </button>

                        <div
                            id="dropdownMenu"
                            class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 transition-all duration-200 ease-out opacity-0 -translate-y-2 pointer-events-none"
                        >
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span class="font-medium text-sm">Your Profile</span>
                            </a>

                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span class="font-medium text-sm">Settings</span>
                            </a>

                            <hr class="my-1 border-gray-200 mx-4" style="border-color: #D2714A; opacity: 0.4;" />

                            <form method="POST" action="">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                    </svg>
                                    <span class="font-medium text-sm">Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @else
                <a href="{{ route('auth.show-login') }}" class="flex items-center gap-2 bg-white rounded-full px-5 py-2 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
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
</script>
