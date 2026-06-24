<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    public function updateRole(Request $request)
    {
        $user = Auth::user();
        $newRole = $request->input('role');

        if (!in_array($newRole, ['voter', 'proposer'])) {
            return back()->withErrors(['role' => 'Недопустимая роль']);
        }

        if ($newRole === 'proposer') {
            if ($user->role !== 'voter') {
                return back()->withErrors(['role' => 'Вы уже являетесь предлагающим']);
            }

            $totalUsers = User::count();
            $proposerCount = User::whereIn('role', ['proposer', 'both'])->count();
            $proposerPercent = $totalUsers > 0 ? ($proposerCount / $totalUsers) * 100 : 0;

            if ($proposerPercent >= 10) {
                return back()->withErrors(['role' => 'Количество предлагающих не может превышать 10% от всех пользователей']);
            }
        }

        if ($newRole === 'voter') {
            if ($user->role !== 'proposer') {
                return back()->withErrors(['role' => 'Вы можете стать голосующим, только если вы предлагающий']);
            }
        }

        $user->role = $newRole;
        $user->save();

        return back()->with('status', 'Роль успешно изменена');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $totalUsers = User::count();
        $proposerCount = User::whereIn('role', ['proposer', 'both'])->count();
        $proposerPercent = $totalUsers > 0 ? round(($proposerCount / $totalUsers) * 100, 2) : 0;

        return view('profile.edit', [
            'user' => $request->user(),
            'totalUsers' => $totalUsers,
            'proposerPercent' => $proposerPercent,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

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
