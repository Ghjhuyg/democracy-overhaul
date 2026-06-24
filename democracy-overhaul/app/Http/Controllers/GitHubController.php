<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;   
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GitHubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }
 
    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Не удалось войти через GitHub']);
        }

        $user = User::where('github_id', $githubUser->id)->first();

        if (!$user) {
            $user = User::where('email', $githubUser->email)->first();

            if ($user) {
                $user->github_id = $githubUser->id;
                $user->save();
            } else {
                $user = User::create([
                    'github_id' => $githubUser->id,
                    'name'      => $githubUser->name ?? $githubUser->nickname ?? 'GitHub User',
                    'email'     => $githubUser->email,
                    'password'  => bcrypt(Str::random(16)),
                    'role'      => 'voter', 
                ]);
            }
        }

        Auth::login($user);

        return redirect()->intended('/bills');
    }
}
