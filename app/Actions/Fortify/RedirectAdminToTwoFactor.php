<?php

namespace App\Actions\Fortify;

use App\Models\TwoFactorToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectAdminToTwoFactor
{
    public function handle(Request $request, callable $next): mixed
    {
        $user = auth()->user();

        if (! $user || ! $user->is_admin) {
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

        // TODO: OTPメール送信 to $superAdmin->email

        return redirect('/two-factor-challenge');
    }
}
