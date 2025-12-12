<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Remove avatar from validated data - we'll handle it separately
        unset($validated['avatar']);
        unset($validated['remove_avatar']);

        $avatarFile = $request->file('avatar');
        $removeAvatar = $request->boolean('remove_avatar');

        $deleteExistingAvatar = function () use ($user): void {
            $currentAvatar = $user->avatar;
            if (!empty($currentAvatar) && !Str::startsWith($currentAvatar, ['http://', 'https://', 'C:\\', 'D:\\'])) {
                if (Storage::disk('public')->exists($currentAvatar)) {
                    Storage::disk('public')->delete($currentAvatar);
                }
            }
        };

        if ($avatarFile && $avatarFile->isValid()) {
            $realPath = $avatarFile->getRealPath();
            if ($realPath && file_exists($realPath)) {
                $deleteExistingAvatar();
                
                // Generate unique filename
                $extension = $avatarFile->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid() . '.' . $extension;
                
                // Store file
                $storedPath = $avatarFile->storeAs('avatars', $filename, 'public');
                
                if ($storedPath) {
                    $validated['avatar'] = $storedPath;
                }
            }
        } elseif ($removeAvatar) {
            $deleteExistingAvatar();
            $validated['avatar'] = null;
        }
        // If neither upload nor remove, don't touch avatar field

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

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
