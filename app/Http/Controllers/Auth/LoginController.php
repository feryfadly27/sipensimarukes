<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        $response = response()->view('auth.login');
        
        // Prevent caching of login page
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        
        return $response;
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Log aktivitas login
            LogAktivitas::catat(
                'Login ke sistem',
                auth()->id(),
                'users',
                null,
                ['username' => $request->username]
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . auth()->user()->nama . '!');
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah'])
            ->withInput($request->only('username'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log aktivitas logout
        LogAktivitas::catat(
            'Logout dari sistem',
            auth()->id(),
            'users'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
        
        // Add cache control headers to prevent back button access
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        
        return $response;
    }
}
