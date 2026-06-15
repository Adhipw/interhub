<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    public static function getRegion(?string $ip): ?string
    {
        if (! $ip || $ip === '127.0.0.1' || $ip === '::1') {
            return '🏠 Localhost';
        }

        return Cache::remember("ip_region_{$ip}", now()->addDays(7), function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful() && $response->json('status') === 'success') {
                    $city = $response->json('city');
                    $country = $response->json('country');
                    $countryCode = $response->json('countryCode');

                    $flag = '';
                    if ($countryCode) {
                        $countryCode = strtoupper($countryCode);
                        for ($i = 0; $i < strlen($countryCode); $i++) {
                            $flag .= mb_chr(ord($countryCode[$i]) + 127397, 'UTF-8');
                        }
                    }

                    return $flag ? "{$flag} {$city}, {$country}" : "{$city}, {$country}";
                }
            } catch (\Exception $e) {
                // Return null if API fails
            }

            return '🌐 Unknown Region';
        });
    }
}
