<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $onVercel = (bool) (env('VERCEL') || env('VERCEL_ENV'));

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => $request->password,
            'email_verified_at' => $onVercel ? now() : null,
        ]);

        if (! $onVercel) {
            event(new Registered($user));
        }

        Auth::login($user);

        return redirect()->route('home');
    }
}
