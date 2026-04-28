@extends('layouts.app')

@section('title', 'User Settings')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            background: #e5e7eb;
            padding: 8px 17px !important;
            border-radius: 0.5rem;
            border: none;
        }

        .ts-control .item {
            background: white;
            border-radius: 0.375rem;
            padding: 4px 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
    </style>
@endpush

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
                    <form id="form-profile" action="{{ route('profile.update.profile', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-2xl font-semibold text-primary mb-2">
                            Edit Profile
                        </h2>
                        <p class="text-sm text-gray-800 mb-6">
                            Keep your personal details private. Information you add here is visible to anyone who can view
                            your profile.
                        </p>

                        <div class="flex items-center gap-4 mb-6">
                            <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                 class="w-14 h-14 rounded-full object-cover">

                            <button type="button" id="change-picture-btn"
                                    class="flex items-center gap-2 bg-primary! text-white px-4 py-1.5 rounded-lg text-sm">
                                <x-icons.picture-upload class="w-5"/>
                                Change Picture
                            </button>

                            <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*">
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Username</label>
                                <x-profile.input type="text" value="{{ old('username') ?? $user->username }}"
                                                 placeholder="Enter your username" name="username"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Name</label>
                                <x-profile.input type="text" value="{{ old('name') ?? $user->name }}"
                                                 placeholder="Enter your name" name="name"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-2 font-medium">Food Preference</label>

                                <select id="preferences" name="preferences[]" multiple>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, old('preferences', $user->preferences->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-center gap-4 mt-6">
                            <button
                                type="button" id="discard-profile-btn"
                                class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-red-500 text-red-500 text-sm font-medium">
                                <x-icons.trash class="w-4"/>
                                Discard Changes
                            </button>

                            <button
                                type="submit"
                                class="flex items-center gap-2 px-5 py-2 rounded-full shadow-md border text-primary! text-sm font-medium">
                                <x-icons.save class="w-4"/>
                                Save Changes
                            </button>
                        </div>

                        <div class="my-10 border-t border-dashed border-gray-300"/>
                    </form>
                </section>

                <section id="account" class="max-w-2xl">
                    <form id="form-account" action="{{ route('profile.update.account', ['user' => auth()->id()]) }}" method="POST">
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
                                <x-profile.input type="email" value="{{ old('email') ?? $user->email }}"
                                                 placeholder="Enter your email" name="email"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Old Password</label>
                                <x-profile.input type="password" placeholder="Enter old password if you want to change"
                                                 name="old_password" autocomplete="off"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">New Password</label>
                                <x-profile.input type="password" placeholder="Enter new password" name="password"
                                                 autocomplete="off"/>
                            </div>

                            <div>
                                <label class="block text-sm text-primary mb-1 font-medium">Confirm New Password</label>
                                <x-profile.input type="password" placeholder="Confirm new password"
                                                 name="password_confirmation" autocomplete="off"/>
                            </div>

                        </div>

                        <div class="flex justify-center gap-4 mt-6">
                            <button type="button" id="discard-account-btn"
                                    class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-red-500 text-red-500 text-sm font-medium">
                                <x-icons.trash class="w-4"/>
                                Discard Changes
                            </button>

                            <button type="submit"
                                    class="flex items-center gap-2 px-5 py-2 rounded-full shadow-md border text-primary! text-sm font-medium">
                                <x-icons.save class="w-4"/>
                                Save Changes
                            </button>
                        </div>
                    </form>

                    <div class="my-10 border-t border-dashed border-gray-300"/>

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

                    <form id="delete-account-form" action="{{ route('profile.destroy', ['user' => auth()->id()]) }}"
                          method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script>
            const initialPreferences = Array.from(
                document.querySelectorAll('#preferences option:checked')
            ).map(option => option.value);

            window.tomSelectInstance = new TomSelect('#preferences', {
                plugins: ['remove_button'],
                placeholder: "Select your preferences...",
                persist: false,
                create: false,
            });
        </script>
    @endpush

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

        const changeBtn = document.getElementById('change-picture-btn');
        const fileInput = document.getElementById('avatar-input');
        const previewImg = document.getElementById('avatar-preview');

        changeBtn.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        const formProfile = document.getElementById('form-profile');
        const formAccount = document.getElementById('form-account');

        const discardProfileBtn = document.getElementById('discard-profile-btn');
        const discardAccountBtn = document.getElementById('discard-account-btn');

        function resetForm(form) {
            form.reset();

            if (window.tomSelectInstance) {
                window.tomSelectInstance.setValue(initialPreferences, true);
                window.tomSelectInstance.refreshItems();
            }
        }

        discardProfileBtn.addEventListener('click', () => {
            Swal.fire({
                title: "Discard changes?",
                text: "Your changes will be lost.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, discard",
            }).then((result) => {
                if (result.isConfirmed) {
                    resetForm(formProfile);
                }
            });
        });

        discardAccountBtn.addEventListener('click', () => {
            Swal.fire({
                title: "Discard changes?",
                text: "Your changes will be lost.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, discard",
            }).then((result) => {
                if (result.isConfirmed) {
                    resetForm(formAccount);
                }
            });
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
