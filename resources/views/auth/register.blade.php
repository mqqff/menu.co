@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="flex min-h-screen">
        <div class="relative w-full lg:w-1/2 bg-primary flex flex-col px-10 py-8">
            <a href="{{ route('home') }}" class="inline-flex items-center text-white hover:opacity-70 transition-opacity w-fit">
                <x-icons.arrow-left class="w-6 h-6" />
            </a>

            <div class="flex flex-col justify-center flex-1 max-w-sm mx-auto w-full">
                <h1 class="text-white text-5xl font-extrabold mb-8 tracking-wider text-center">
                    Register
                </h1>

                <form method="POST" action="{{ route('auth.register') }}" class="space-y-5">
                    @csrf
                    <x-auth.input-field
                        name="email"
                        label="Email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Email"
                        autofocus="true"
                        required
                    />

                    <x-auth.input-field
                        name="name"
                        label="Name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Name"
                        required
                    />

                    <x-auth.input-field
                        name="username"
                        label="Username"
                        type="text"
                        value="{{ old('username') }}"
                        placeholder="Username"
                        required
                    />

                    <x-auth.input-field
                        name="password"
                        label="Password"
                        type="password"
                        placeholder="Password"
                        :showToggle="true"
                        required
                    />

                    <x-auth.input-field
                        name="password_confirmation"
                        label="Confirm Password"
                        type="password"
                        placeholder="Confirm Password"
                        :showToggle="true"
                        required
                    />

                    <div class="pt-2 flex justify-center">
                        <x-auth.button type="submit" class="px-10 py-2">
                            Submit
                        </x-auth.button>
                    </div>

                    <p class="text-center text-sm text-white pt-1">
                        Already have an account?
                        <a href="{{ route('auth.login.form') }}"
                           class="font-semibold underline underline-offset-2 hover:opacity-80 transition-opacity ml-1">
                            Login
                        </a>
                    </p>
                </form>
            </div>
        </div>

        <div class="hidden lg:flex lg:w-1/2 bg-[#F7B555] items-center justify-center relative overflow-hidden">
            <div class="w-full h-full relative select-none pointer-events-none overflow-hidden">
                <div class="absolute -left-10 top-6 w-64 h-36">
                    <x-auth.egg />
                </div>
                <div class="absolute right-24 -top-18 w-64">
                    <x-auth.lettuce />
                </div>
                <div class="absolute left-4 top-64 w-64">
                    <x-auth.lettuce />
                </div>
                <div class="absolute right-2 top-52 w-64 h-36">
                    <x-auth.egg />
                </div>
                <div class="absolute -left-10 bottom-8 w-64 h-36">
                    <x-auth.egg />
                </div>
                <div class="absolute right-6 bottom-32 w-64">
                    <x-auth.lettuce />
                </div>
                <div class="absolute right-32 -bottom-16 w-64 h-36">
                    <x-auth.egg />
                </div>
            </div>
        </div>
    </div>
@endsection
