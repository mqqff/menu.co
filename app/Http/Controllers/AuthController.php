<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['avatar'] = 'images/user/default.jpg';

        User::create($data);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->with('error', 'Incorrect email or password')->withInput();
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otp = rand(100000, 999999);

        PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset OTP');
        });

        session(['reset_email' => $request->email]);

        return redirect()->route('auth.password.verify.form')->with('success', 'Verification code sent to your email.');
    }

    public function showVerifyCodeForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('auth.password.forgot.form');
        }

        $record = PasswordResetToken::where('email', $email)->first();

        if (!$record) {
            return back()->with('error', 'Invalid OTP');
        }

        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return back()->with('error', 'Invalid OTP');
        }

        if (!Hash::check($request->otp, $record->token)) {
            return back()->with('error', 'Invalid OTP');
        }

        session(['otp_verified' => true]);

        return redirect()->route('auth.password.reset.form',)->with('success', 'Verification successful. You can now reset your password.');
    }

    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $email = session('reset_email');

        if (!session('otp_verified') || !$email) {
            return redirect()->route('auth.password.forgot.form');
        }

        $user = User::where('email', $email)->first();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        PasswordResetToken::where('email', $email)->delete();

        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password reset successful. Please login.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
