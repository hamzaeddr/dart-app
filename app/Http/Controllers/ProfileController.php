<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $profile = Profile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

	public function show(User $user): View
	{
		$profile = Profile::where('user_id', $user->id)->first();

		if (! $profile) {
			$profile = Profile::create(['user_id' => $user->id]);
		}

		return view('profile.show', [
			'user' => $user,
			'profile' => $profile,
		]);
	}

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $profile = Profile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $revolutLink = $validated['revolut_link'] ?? null;
        if ($revolutLink && !preg_match('/^https?:\/\//i', $revolutLink)) {
            $revolutLink = 'https://' . $revolutLink;
        }

        $profile->fill([
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'revolut_link' => $revolutLink,
        ]);

        $profile->save();

        if ($request->hasFile('avatar')) {
            $profile->clearMediaCollection('avatar');
            $profile->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        if ($request->hasFile('revolut_qr')) {
            $profile->clearMediaCollection('revolut_qr');
            $profile->addMediaFromRequest('revolut_qr')->toMediaCollection('revolut_qr');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
