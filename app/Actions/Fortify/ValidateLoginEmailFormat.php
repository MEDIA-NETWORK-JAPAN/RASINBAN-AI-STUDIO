<?php

namespace App\Actions\Fortify;

class ValidateLoginEmailFormat
{
    public function handle($request, $next): mixed
    {
        $request->validate([
            'email' => ['email'],
        ]);

        return $next($request);
    }
}
