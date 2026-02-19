<?php

namespace App\Actions\Fortify;

use App\Mail\TwoFactorOtpMail;
use App\Models\TwoFactorToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RedirectAdminToTwoFactor
{
    public function handle(Request $request, callable $next): mixed
    {
        $user = auth()->user();

        if (! $user || ! $user->is_admin) {
            $request->session()->forget(['two_factor_pending', 'two_factor_user_id']);

            return $next($request);
        }

        $superAdmin = User::where('id', 1)->where('is_admin', true)->first();
        if (! $superAdmin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'システムエラーが発生しました。管理者にお問い合わせください。',
            ]);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        TwoFactorToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => $otp,
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
            ]
        );

        $loginUserId = $user->id;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->put('two_factor_pending', true);
        $request->session()->put('two_factor_user_id', $loginUserId);

        Mail::to($superAdmin->email)->send(new TwoFactorOtpMail($otp));

        return redirect('/two-factor-challenge');
    }
}
