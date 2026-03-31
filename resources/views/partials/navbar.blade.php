<nav style="background-color: #D2714A;" class="w-full px-10 py-4">
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
                    <button class="w-10 h-10 rounded-full overflow-hidden border-2 border-white focus:outline-none cursor-pointer">
                        <img src="{{ asset('assets/images/user.jpg') }}" alt="User Image" class="w-full h-full object-cover" />
                    </button>
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
