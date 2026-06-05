<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── 顯示註冊頁 ──────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    // ── 處理註冊 ────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:20',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'          => '請輸入暱稱',
            'name.max'               => '暱稱最多 20 個字',
            'email.required'         => '請輸入 Email',
            'email.email'            => 'Email 格式不正確',
            'email.unique'           => '這個 Email 已經被註冊了',
            'password.required'      => '請輸入密碼',
            'password.min'           => '密碼至少 8 個字元',
            'password.confirmed'     => '兩次密碼不一致',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/')->with('success', '歡迎來到 TimeTown，' . $user->name . '！');
    }

    // ── 顯示登入頁 ──────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    // ── 處理登入 ────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => '請輸入 Email',
            'email.email'       => 'Email 格式不正確',
            'password.required' => '請輸入密碼',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect('/')->with('success', '歡迎回來，' . Auth::user()->name . '！');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email 或密碼不正確']);
    }

    // ── 登出 ────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
