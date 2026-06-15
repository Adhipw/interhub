<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'InternHub API',
    version: '1.0.0',
    description: 'API Documentation for InternHub 2026',
    contact: new OA\Contact(email: 'admin@internhub.my.id')
)]
#[OA\Server(url: 'http://localhost:8000/api', description: 'Local Development Server')]
abstract class Controller
{
    use AuthorizesRequests;
}
