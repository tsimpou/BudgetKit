<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileInfoRequest;
use App\Http\Requests\UpdateProfilePasswordRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Display the authenticated user's profile page.
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    // Update the authenticated user's name and email.
    public function updateInfo(UpdateProfileInfoRequest $request)
    {
        Auth::user()->update($request->validated());

        return back()->with('success_info', __('auth.profile_updated'));
    }

    // Change the authenticated user's password after verifying the current one.
    public function updatePassword(UpdateProfilePasswordRequest $request)
    {
        Auth::user()->update([
            'password' => $request->password,
        ]);

        return back()->with('success_password', __('auth.password_updated'));
    }
}
