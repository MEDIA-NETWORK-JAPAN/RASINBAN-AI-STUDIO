<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorOtpMail;
use App\Models\TwoFactorToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        $admin = User::where('id', 1)->where('is_admin', true)->first();
        if (! $admin) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors([
                'code' => 'システムエラーが発生しました。管理者にお問い合わせください。',
            ]);
        }

        $userId = $request->session()->get('two_factor_user_id');
        $token = $userId ? TwoFactorToken::where('user_id', $userId)->first() : null;
        $attempts = $token?->attempts ?? 0;
        $maxAttempts = 5;

        return view('auth.two-factor-challenge', [
            'adminName' => $admin->name,
            'attempts' => $attempts,
            'maxAttempts' => $maxAttempts,
        ]);
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
        $user = $userId ? User::where('id', $userId)->where('is_admin', true)->first() : null;

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
            $request->session()->regenerate();
            Auth::login($user);

            return redirect('/admin');
        }

        $token->refresh();
        if ($token->isMaxAttemptsReached()) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors(['code' => '試行回数の上限に達しました。']);
        }

        return back()->withErrors(['code' => '認証コードが正しくありません。']);
    }

    /**
     * Resend OTP code to the super admin email.
     */
    public function resend(Request $request): RedirectResponse
    {
        if (! $request->session()->has('two_factor_pending')) {
            return redirect('/login');
        }

        $userId = $request->session()->get('two_factor_user_id');
        $user = $userId ? User::where('id', $userId)->where('is_admin', true)->first() : null;

        if (! $user) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors(['code' => '認証情報が無効です。']);
        }

        $superAdmin = User::where('id', 1)->where('is_admin', true)->first();
        if (! $superAdmin) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return redirect('/login')->withErrors(['code' => 'システムエラーが発生しました。']);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        TwoFactorToken::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $otp, 'expires_at' => now()->addMinutes(10), 'attempts' => 0]
        );
        Mail::to($superAdmin->email)->send(new TwoFactorOtpMail($otp, $user));

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
