@extends('layouts.auth')

@section('title', 'Reset Password')

@include('partials.flash')
@section('content')
    <div class="bg-primary min-h-screen flex items-center justify-center relative">
        <a href="{{ route('auth.password.verify.form') }}" class="absolute top-6 left-6 text-white text-2xl">
            <x-icons.arrow-left class="w-6 h-6" />
        </a>

        <div class="w-full max-w-2xl text-center px-6">
            <h1 class="text-white text-5xl font-extrabold mb-3 tracking-wider">
                Reset Password
            </h1>

            <p class="text-white/80 mb-10 leading-relaxed">
                Enter your new password and you're all set!
            </p>

            <div class="flex justify-center mb-10">
                <div class="w-27.5 h-27.5 flex items-center justify-center">
                    <x-icons.key class="w-52 text-white" />
                </div>
            </div>

            <form method="POST" action="{{ route('auth.password.reset.update') }}" class="w-full max-w-sm mx-auto">
                @csrf
                @method('PATCH')

                <div class="text-white font-normal text-start space-y-5">
                    <x-auth.input-field label="Password" name="password" :showToggle="true" type="password" placeholder="New Password" autofocus />
                    <x-auth.input-field label="Password Confirmation" name="password_confirmation" :showToggle="true" type="password" placeholder="Confirm New Password" />

                    <x-auth.button type="submit" class="mx-auto block px-10 py-2">
                        Submit
                    </x-auth.button>
                </div>

            </form>

        </div>
    </div>
@endsection
