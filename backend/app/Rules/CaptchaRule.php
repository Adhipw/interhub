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
        if (empty($value)) {
            $fail('Silakan selesaikan verifikasi reCAPTCHA.');

            return;
        }

        if (config('services.recaptcha.allow_fallback') && str_starts_with((string) $value, 'captcha-fallback-')) {
            return;
        }

        $secretKey = (string) config('services.recaptcha.secret_key');

        if ($secretKey === '') {
            $fail('Konfigurasi reCAPTCHA server belum lengkap. Hubungi administrator.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable) {
            $fail('Verifikasi reCAPTCHA sedang tidak dapat diproses. Silakan coba lagi.');

            return;
        }

        if (! $response->ok() || ! $response->json('success')) {
            $fail('Verifikasi reCAPTCHA gagal atau token kadaluwarsa.');
        }
    }
}
