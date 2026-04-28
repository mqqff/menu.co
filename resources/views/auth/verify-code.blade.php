@extends('layouts.auth')

@section('title', 'Reset Password - Verify Code')

@include('partials.flash')
@section('content')
    <div class="bg-primary min-h-screen flex items-center justify-center relative">
        <a href="{{ route('auth.password.forgot.form') }}" class="absolute top-6 left-6 text-white">
            <x-icons.arrow-left class="w-6 h-6" />
        </a>

        <form method="POST" action="{{ route('auth.password.verify.check') }}" id="otpForm" class="w-full max-w-175 text-center px-6">
            @csrf

            <h1 class="text-white text-5xl font-extrabold mb-3 tracking-wider">
                Verification Code
            </h1>

            <p class="text-white/80 mb-10 leading-relaxed">
                Enter verification code sent to your email!
            </p>

            <input type="hidden" name="otp" id="otp">

            <div class="flex justify-center gap-5 mb-10">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                           class="otp-input w-17.5 h-22.5 text-center text-white text-2xl bg-transparent border-2 border-white rounded-xl outline-none transition {{ $i == 0 ? 'ring-2 ring-white' : '' }}" {{ $i == 0 ? 'autofocus' : '' }}>
                @endfor
            </div>

            <x-auth.button type="submit" class="px-6 py-2 block mx-auto mb-5">
                Verify Code
            </x-auth.button>

            <p class="text-white/80 text-sm">
                Didn’t receive code?
                <a href="#" class="underline text-white">
                    Resend Verification Code
                </a>
            </p>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputs = document.querySelectorAll(".otp-input");
            const hiddenInput = document.getElementById("otp");
            const form = document.getElementById("otpForm");

            function updateOTP() {
                const value = Array.from(inputs).map(i => i.value).join("");
                hiddenInput.value = value;
                if (value.length === inputs.length) {
                    form.submit();
                }
            }

            inputs.forEach((input, index) => {
                input.addEventListener("input", (e) => {
                    let val = e.target.value.replace(/[^0-9]/g, "");
                    e.target.value = val.slice(-1);

                    if (val && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }

                    updateOTP();
                });

                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !input.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener("focus", () => {
                    input.classList.remove("border-white");
                    input.classList.add("border-white", "ring-2", "ring-white");
                });

                input.addEventListener("blur", () => {
                    input.classList.remove("ring-2", "ring-white");
                    input.classList.add("border-white");
                });

                input.addEventListener("paste", (e) => {
                    e.preventDefault();
                    const paste = e.clipboardData.getData("text").replace(/[^0-9]/g, "").slice(0, inputs.length);

                    paste.split("").forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                        }
                    });

                    if (inputs[paste.length - 1]) {
                        inputs[paste.length - 1].focus();
                    }

                    updateOTP();
                });
            });
        });
    </script>
@endsection
