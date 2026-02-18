<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the two-factor challenge form.
     * Requires 'two_factor_pending' session to be set.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('two_factor_pending')) {
            return redirect('/login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the submitted OTP code and authenticate.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->session()->has('two_factor_pending')) {
            return redirect('/login');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('two_factor_user_id');
        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors(['code' => '認証情報が無効です。']);
        }

        $token = TwoFactorToken::where('user_id', $user->id)->first();

        if (! $token || $token->isExpired() || $token->isMaxAttemptsReached()) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors(['code' => '認証コードが無効または期限切れです。']);
        }

        if ($token->verify($request->input('code'))) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);
            Auth::login($user);

            return redirect('/admin');
        }

        return back()->withErrors(['code' => '認証コードが正しくありません。']);
    }

    /**
     * Resend OTP code (placeholder – email sending implemented separately).
     */
    public function resend(Request $request): RedirectResponse
    {
        if (! $request->session()->has('two_factor_pending')) {
            return redirect('/login');
        }

        return back()->with('status', '認証コードを再送信しました。');
    }

    /**
     * Clear two-factor session and logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);
        Auth::logout();

        return redirect('/login');
    }
}
