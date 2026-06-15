<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ApiTranslationController extends Controller
{
    public function show($locale)
    {
        $path = base_path("lang/{$locale}.json");

        if (! File::exists($path)) {
            // Fallback to en if requested locale doesn't exist
            $path = base_path('lang/en.json');
        }

        if (! File::exists($path)) {
            return response()->json([], 200);
        }

        $json = File::get($path);

        return response()->json(json_decode($json, true));
    }
}
