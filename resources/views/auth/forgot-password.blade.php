@extends('layouts.auth')

@section('title', 'Forgot Password')

@include('partials.flash')
@section('content')
    <div class="bg-primary min-h-screen flex items-center justify-center relative">
        <a href="{{ route('auth.login.form') }}" class="absolute top-6 left-6 text-white text-2xl">
            <x-icons.arrow-left class="w-6 h-6" />
        </a>

        <div class="w-full max-w-2xl text-center px-6">
            <h1 class="text-white text-5xl font-extrabold tracking-wider mb-3">
                Forgot Your Password?
            </h1>

            <p class="text-white/80 mb-10 leading-relaxed">
                Enter your email and we’ll send you a verification code.
            </p>

            <div class="flex justify-center mb-10">
                <div class="w-27.5 h-27.5 flex items-center justify-center">
                    <x-icons.key class="w-52 text-white" />
                </div>
            </div>

            <form method="POST" action="{{ route('auth.password.forgot.send') }}" class="w-full mx-auto max-w-sm">
                @csrf

                <div class="space-y-5 text-white font-normal text-start">
                    <x-auth.input-field label="Email" name="email" type="email" placeholder="Enter your email address" autofocus />

                    <x-auth.button type="submit" class="block mx-auto px-8 py-2">
                        Send Email
                    </x-auth.button>
                </div>

            </form>

        </div>
    </div>
@endsection
