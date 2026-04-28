@extends('layouts.app')

@section('title', 'User Settings')

@section('content')
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="max-w-6xl mx-auto flex gap-16 px-4">
            <div class="w-1/4">
                <div class="sticky top-24">
                    <h2 class="text-xl font-semibold text-primary mb-6">
                        Settings
                    </h2>

                    <ul class="space-y-3">
                        <li>
                            <a href="#profile" id="menu-profile"
                               class="text-gray-700 font-semibold text-lg">
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <a href="#account" id="menu-account"
                               class="text-gray-400 font-semibold text-lg">
                                Account
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="w-3/4">
                <section id="profile" class="mb-12 max-w-2xl">
                    <h2 class="text-2xl font-semibold text-primary mb-2">
                        Edit Profile
                    </h2>
                    <p class="text-sm text-gray-800 mb-6">
                        Keep your personal details private. Information you add here is visible to anyone who can view your profile.
                    </p>

                    <div class="flex items-center gap-4 mb-6">
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                             class="w-14 h-14 rounded-full object-cover">

                        <button class="flex items-center gap-2 bg-primary! text-white px-4 py-1.5 rounded-lg text-sm">
                            <x-icons.picture-upload class="w-5"/>
                            Change Picture
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm text-primary mb-1 font-medium">Username</label>
                            <x-profile.input type="text" value="{{ old('username') ?? auth()->user()->username }}" placeholder="Enter your username" name="username"/>
                        </div>

                        <div>
                            <label class="block text-sm text-primary mb-1 font-medium">Name</label>
                            <x-profile.input type="text" value="{{ old('name') ?? auth()->user()->name }}" placeholder="Enter your name" name="name"/>
                        </div>

                        <div>
                            <label class="block text-sm text-primary mb-2 font-medium">Food Preference</label>

                            <div class="flex flex-wrap items-center gap-2 bg-gray-200 p-3 rounded-lg">

                            <span class="bg-white px-3 py-1 rounded-md text-sm flex items-center gap-2 shadow-sm">
                                Seafood <span class="text-red-500">✕</span>
                            </span>

                                <span class="bg-white px-3 py-1 rounded-md text-sm flex items-center gap-2 shadow-sm">
                                Asian Food <span class="text-red-500">✕</span>
                            </span>

                                <span class="bg-white px-3 py-1 rounded-md text-sm flex items-center gap-2 shadow-sm">
                                Noodles <span class="text-red-500">✕</span>
                            </span>

                                <input type="text"
                                       placeholder="Type to add more"
                                       class="bg-transparent outline-none text-sm px-2">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center gap-4 mt-6">
                        <button class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-red-500 text-red-500 text-sm font-medium">
                            <x-icons.trash class="w-4"/>
                            Discard Changes
                        </button>

                        <button class="flex items-center gap-2 px-5 py-2 rounded-full shadow-md border text-primary! text-sm">
                            <x-icons.save class="w-4"/>
                            Save Changes
                        </button>
                    </div>

                    <div class="my-10 border-t border-dashed border-gray-300" />
                </section>

                <section id="account" class="max-w-2xl">
                    <form action="{{ route('profile.update.account', ['user' => auth()->id()]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-2xl font-semibold text-primary mb-2">
                            Account
                        </h2>
                        <p class="text-sm text-gray-800 mb-6">
                            Make changes to your email or password.
                        </p>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Email</label>
                                <x-profile.input type="email" value="{{ old('email') ?? auth()->user()->email }}" placeholder="Enter your email" name="email"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Old Password</label>
                                <x-profile.input type="password" placeholder="Enter old password if you want to change" name="old_password" autocomplete="off"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">New Password</label>
                                <x-profile.input type="password" placeholder="Enter new password" name="password" autocomplete="off"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Confirm New Password</label>
                                <x-profile.input type="password" placeholder="Confirm new password" name="password_confirmation" autocomplete="off"/>
                            </div>

                        </div>

                        <div class="flex justify-center gap-4 mt-6">
                            <button type="button" class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-red-500 text-red-500 text-sm font-medium">
                                <x-icons.trash class="w-4"/>
                                Discard Changes
                            </button>

                            <button type="submit" class="flex items-center gap-2 px-5 py-2 rounded-full shadow-md border text-primary! text-sm">
                                <x-icons.save class="w-4"/>
                                Save Changes
                            </button>
                        </div>
                    </form>

                    <div class="my-10 border-t border-dashed border-gray-300" />

                    <div class="mt-12">
                        <h3 class="text-sm font-semibold text-primary mb-1">
                            Delete Account
                        </h3>

                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-800 flex-1">
                                Permanently delete your data and everything associated with your account
                            </p>
                            <button type="button" id="delete-account-btn"
                                    class="bg-red-500 text-white px-6 py-2 rounded-full text-sm font-semibold cursor-pointer">
                                Delete Account
                            </button>
                        </div>
                    </div>

                    <form id="delete-account-form" action="{{ route('profile.destroy', ['user' => auth()->id()]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </section>
            </div>
        </div>
    </div>

    @if(session('success'))
        {{ session('success') }}
    @endif

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                })
            })
        </script>
    @endif

    <script>
        const profile = document.getElementById('profile');
        const account = document.getElementById('account');

        const menuProfile = document.getElementById('menu-profile');
        const menuAccount = document.getElementById('menu-account');

        window.addEventListener('scroll', () => {
            const scroll = window.scrollY;
            const accountTop = account.offsetTop - 200;

            if (scroll >= accountTop) {
                menuAccount.classList.add('text-gray-700');
                menuAccount.classList.remove('text-gray-400');

                menuProfile.classList.remove('text-gray-700');
                menuProfile.classList.add('text-gray-400');
            } else {
                menuProfile.classList.add('text-gray-700');
                menuProfile.classList.remove('text-gray-400');

                menuAccount.classList.remove('text-gray-700');
                menuAccount.classList.add('text-gray-400');
            }
        });

        const deleteBtn = document.getElementById('delete-account-btn');
        const deleteForm = document.getElementById('delete-account-form');

        deleteBtn.addEventListener('click', () => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            });
        });
    </script>
@endsection
