@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="bg-primary min-h-screen flex items-center justify-center relative">
        <a href="{{ route('auth.login.form') }}" class="absolute top-6 left-6 text-white text-2xl">
            <x-icons.arrow-left class="w-6 h-6" />
        </a>

        <div class="w-full max-w-lg text-center px-6">
            <h1 class="text-white text-[40px] font-semibold leading-tight mb-3">
                Forgot Your Password?
            </h1>

            <p class="text-white/80 text-[16px] mb-10 leading-relaxed">
                Enter your email and we’ll send you a verification code.
            </p>

            <div class="flex justify-center mb-10">
                <div class="w-27.5 h-27.5 flex items-center justify-center">
                    <svg class="w-48 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"/><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"/></svg>
                </div>
            </div>

            <form method="POST" action="#" class="w-full">
                @csrf

                <div class="text-left mb-2">
                    <label class="text-white text-sm">Email</label>
                </div>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    autofocus
                    class="w-full h-12 px-5 rounded-full bg-white/80 placeholder-gray-400 focus:outline-none mb-6"
                >

                <button
                    type="submit"
                    class="bg-white text-primary rounded-full shadow-md text-sm font-semibold hover:scale-105 transition px-4 py-2 cursor-pointer"
                >
                    Send Verification Code
                </button>

            </form>

        </div>
    </div>
@endsection
