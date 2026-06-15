<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class CaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Bypass for local development
        if (env('VITE_LOCALHOST_MODE') === true || env('VITE_LOCALHOST_MODE') === 'true') {
            return;
        }

        if (empty($value)) {
            $fail('Silakan selesaikan verifikasi reCAPTCHA.');

            return;
        }

        if (config('services.recaptcha.allow_fallback') && str_starts_with((string) $value, 'captcha-fallback-')) {
            return;
        }

        $response = Http::asForm()->withoutVerifying()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->json('success')) {
            $fail('Verifikasi reCAPTCHA gagal atau token kadaluwarsa.');
        }
    }
}
